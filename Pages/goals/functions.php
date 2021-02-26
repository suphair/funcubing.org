<?php

namespace goals;

function getCompetitors($comp) {
    return \db::rows("
        SELECT
            dict_competitors.wid, 
            dict_competitors.name,
            dict_competitors.wcaid,
            dict_competitors.country,
            goals_competitors.timestamp
        FROM goals_competitions
        JOIN goals ON goals_competitions.wca = goals.Competition
        JOIN goals_competitors 
            ON goals_competitors.competitorWid = goals.competitor
            AND goals_competitors.competitionWca = goals_competitions.wca
            AND goals_competitors.eventCode = goals.event
        JOIN dict_competitors ON dict_competitors.wid = goals.competitor
        WHERE goals_competitions.wca = '$comp'
        ORDER BY dict_competitors.name
    ");
}

function getCount($comp) {
    return \db::rows("
        SELECT
            count(*) goalCount,
            goals.Competitor wid,
            goals_events.code event,
            SUM(goals.complete) completeCount
        FROM goals
           JOIN goals_competitions on goals_competitions.WCA = goals.Competition
           JOIN goals_events on goals_events.code = goals.event
           WHERE goals_competitions.wca = '$comp'   
        GROUP BY 
            goals_competitions.ID,
            goals.Competitor,
            goals_events.code  
    ");
}

function getCompetition($comp) {
    return \db::row("
        SELECT
            goals_competitions.dateStart < now() close,
            goals_competitions.resultsLoad,
            COUNT(distinct goals.competitor) Competitors,
            COUNT(distinct goals.id) Goals,
            goals_competitions.*,
            dict_countries.name country_name
        FROM goals_competitions
            LEFT OUTER JOIN goals ON goals_competitions.wca = goals.Competition
            LEFT OUTER JOIN goals_events ON goals_events.code = goals.event
            LEFT OUTER JOIN dict_countries ON dict_countries.ISO2 = goals_competitions.Country
        WHERE goals_competitions.WCA = '$comp'
        GROUP BY goals_competitions.ID, dict_countries.name
        ORDER BY goals_competitions.dateEnd DESC, goals_competitions.wca
    ");
}

function getGoals($comp) {
    return \db::rows("
        SELECT
            goals.event,
            goals.format,
            goals.result,
            goals.record,
            goals.progress,
            goals.goal,
            goals.complete,
            goals.competitor wid,
            goals_events.name eventName 
        FROM goals 
        JOIN goals_events ON goals_events.code = goals.event
        WHERE
            goals.competition = '$comp'
        ORDER BY 
            goals_events.id,
            goals.Format
        ");
}

function recordFormat($result, $event, $type) {
    if (!$result) {
        return '';
    }

    if ($event == '333fm' and $type == 'average') {
        return sprintf("%01.2f", $result / 100);
    }
    if ($event == '333fm' and $type == 'single') {
        return $result;
    }

    $result_str = '';
    $minute = 0;
    if ($result > 6000) {
        $minute = floor($result / 6000);
        $result_str .= $minute . ":";
        $result -= $minute * 6000;
    }

    $second = floor($result / 100);
    if ($result < 1000 and $minute) {
        $result_str .= "0" . $second;
    } else {
        $result_str .= $second;
    }

    $result_str .= ".";

    $milisecond = $result - $second * 100;
    if ($milisecond < 10) {
        $result_str .= "0" . $milisecond;
    } else {
        $result_str .= $milisecond;
    }
    return $result_str;
}

function progress($record, $goal) {
    if (!$record or!$goal) {
        return FALSE;
    }
    $goalInt = resultInt($goal);
    $recordInt = resultInt($record);
    if ($goalInt >= $recordInt) {
        return FALSE;
    }
    return (round(($recordInt - $goalInt) / $recordInt * 100, 1)) . '%';
}

function resultInt($result) {
    if (in_array($result, ['DNF', 'DNS'])) {
        return 0;
    }
    $resultInt = str_replace([":", "."], "_", "0" . $result);
    $a = explode("_", $resultInt);
    switch (sizeof($a)) {
        case 3:
            return ($a[0] * 60 * 100) + $a[1] * 100 + $a[2];
        case 2:
            return $a[0] * 100 + $a[1];
        case 1:
            return $resultInt;
    }
    return 0;
}

function resultStr($result, $event, $format) {
    switch ($result) {
        case false: return '';
        case -1: return 'DNF';
        case -2: return 'DNS';
    }

    if ($event == '333fm') {
        return $format == 'single' ? $result : sprintf('%0.2f', $result / 100);
    }

    $minute = (int) floor($result / 60 / 100);
    $second = (int) floor(($result - $minute * 60 * 100) / 100);
    $milisecond = (int) ($result - $minute * 60 * 100 - $second * 100);

    if ($minute == 0 and $second == 0) {
        return sprintf('0.%02d', $milisecond);
    } else if ($minute == 0) {
        return sprintf('%d.%02d', $second, $milisecond);
    } else {
        return sprintf('%d:%02d.%02d', $minute, $second, $milisecond);
    }
}

function updateCompetition($competition) {

    $competitionEvent = (array) $competition->event_ids;
    $goalsEvent = array_column(\db::rows("SELECT code FROM goals_events ORDER BY id"), 'code');
    $events = array_values(array_intersect($goalsEvent, $competitionEvent));

    \db::exec("INSERT IGNORE INTO goals_competitions (WCA)"
            . " VALUES ('{$competition->id}')");

    \db::exec("UPDATE goals_competitions "
            . " SET name = '" . \db::escape($competition->name) . "', "
            . " country = '{$competition->country_iso2}', "
            . " city = '{$competition->city}', "
            . " dateStart = '{$competition->start_date}', "
            . " dateEnd = '{$competition->end_date}', "
            . " events = '" . json_encode($events) . "', "
            . " timestamp = now() "
            . " WHERE WCA = '{$competition->id}'"
    );
}

function cron() {
    $details = [
        'competitions' => ['update' => 0, 'delete' => 0],
        'results' => ['all' => 0, 'load' => 0, 'update' => 0]
    ];

    $competitionsDetails = array_column(
            \db::rows("SELECT wca FROM goals_competitions "
                    . " WHERE dateStart > current_date() "
                    . " AND timestamp < DATE_ADD(current_date(),INTERVAL - 4 HOUR) ")
            , 'wca');
    foreach ($competitionsDetails as $wca) {
        $competitionData = \wcaapi::getCompetition($wca, __FILE__ . ': ' . __LINE__, [], FALSE);
        $error = $competitionData->error ?? FALSE;
        if (!$error and!($competitionData->cancelled_at ?? FALSE)) {
            updateCompetition($competitionData);
            $details['competitions']['update']++;
        }
        if ($error == "Competition with id $wca not found" or ($competitionData->cancelled_at ?? FALSE)) {
            \db::exec("DELETE FROM goals_competitions WHERE wca='$wca'");
            $details['competitions']['delete']++;
        }
    }

    $competitionsResult = array_column(
            \db::rows("SELECT DISTINCT goals_competitions.wca "
                    . " FROM goals_competitions "
                    . " JOIN goals ON goals_competitions.wca = goals.competition "
                    . " WHERE goals_competitions.dateStart < current_date() "
                    . " AND goals_competitions.dateEnd > DATE_ADD(current_date(), INTERVAL -4 Week)")
            , 'wca');
    $details['results']['all'] = sizeof($competitionsResult);

    foreach ($competitionsResult as $wca) {
        $results = \wcaapi::getCompetitionResults($wca, __FILE__ . ': ' . __LINE__, [], FALSE);

        if ($results ?? FALSE) {
            continue;
        }

        $details['results']['update']++;
        \db::exec("UPDATE goals_competitions SET resultsLoad = 1 WHERE wca = '$wca'");
        $resultsLoad = ['single' => [], 'average' => []];
        foreach ($results as $result) {
            $best = $result->best;
            $wcaid = $result->wca_id;
            $event = $result->event_id;
            $average = $result->average;

            $resultsLoad['single'][$wcaid][$event] ??= [0];
            $resultsLoad['average'][$wcaid][$event] ??= [0];

            $resultsLoad['single'][$wcaid][$event][] = $best;
            $resultsLoad['average'][$wcaid][$event][] = $average;
        }

        $goals = \db::rows("SELECT goals.*, dict_competitors.wcaid wcaid FROM goals  "
                        . " JOIN dict_competitors ON dict_competitors.wid = goals.competitor "
                        . " WHERE goals.competition='$wca'");
        foreach ($goals as $goal) {
            $results = $resultsLoad[$goal->format][$goal->wcaid][$goal->event] ?? [0];
            if ($results) {
                $resultBest = max(array_diff($results, [-1, -2]));
                $result = resultStr($resultBest, $goal->event, $goal->format);
                $complete = !resultInt($result) ? 0 : (int) (resultInt($result) <= resultInt($goal->goal));
                \db::exec("UPDATE goals SET result = '$result', complete = $complete WHERE id = '{$goal->id}' ");
            }
        }
    }
    return json_encode($details);
}

function getCompetitionRegistration($wca) {
    $persons = \wcaapi::getCompetitionRegistrations($wca, __FILE__ . ': ' . __LINE__, [], false);
    $registrations = [];
    foreach ($persons as $person) {
        $registrations[$person->user_id] = $person->event_ids;
    }
    return $registrations;
}

function getPersonRecords($wca) {
    $person = \wcaapi::getPerson($wca, __FILE__ . ': ' . __LINE__, [], false);
    $records = [];
    foreach ($person->personal_records ?? [] as $event => $record_types) {
        foreach ($record_types as $record_type => $record) {
            $records[$event][$record_type] = $record->best;
        }
    }
    return $records;
}
