<?php

namespace unofficial;

function admin() {
    return ((\wcaoauth::me()->id ?? FALSE) == 6834);
}

function getCompetitions($me, $mine) {
    $me_id = ($me->id ?? -1);
    $admin = admin();
    $sql = "SELECT
        unofficial_competitions.website,
        unofficial_competitions.id,
        unofficial_competitions.show, 
        unofficial_competitions.competitor,
        unofficial_competitions.secret,
        unofficial_competitions.name,
        unofficial_competitions.details,
        unofficial_competitions.date,
        dict_competitors.name competitor_name,
        dict_competitors.country competitor_country,
        unofficial_organizers.id organizer,
        unofficial_competitions.competitor = $me_id my
    FROM unofficial_competitions
    JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor 
    LEFT OUTER JOIN unofficial_organizers 
    ON unofficial_organizers.competition = unofficial_competitions.id and unofficial_organizers.wcaid='$me_id' 
    WHERE unofficial_competitions.competitor = $me_id OR unofficial_organizers.id     
    ";
    if (!$mine) {
        $sql .= "OR unofficial_competitions.show ";
        if ($admin) {
            $sql .= "OR TRUE";
        }
    }

    $sql .= " ORDER BY unofficial_competitions.date DESC";

    return \db::rows($sql);
}

function getCompetition($secret, $me = FALSE) {
    $me_id = ($me->id ?? -1);
    $admin = admin() ? 'TRUE' : 'FALSE';
    $sql = "SELECT
        unofficial_competitions.website,
        unofficial_competitions.id,
        unofficial_competitions.show, 
        unofficial_competitions.competitor,
        unofficial_competitions.secret,
        unofficial_competitions.secretRegistration,
        unofficial_competitions.shareRegistration,
        unofficial_competitions.name,
        unofficial_competitions.details,
        unofficial_competitions.date,
        dict_competitors.name competitor_name,
        dict_competitors.wcaid competitor_wcaid,
        dict_competitors.country competitor_country,
        (unofficial_competitions.competitor = $me_id OR $admin) my,
        unofficial_organizers.id >0 organizer
    FROM unofficial_competitions
    JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor 
    LEFT OUTER JOIN unofficial_organizers 
    ON unofficial_organizers.competition = unofficial_competitions.id and unofficial_organizers.wcaid='$me_id' 
    WHERE  unofficial_competitions.secret = '$secret' 
    ";
    return \db::row($sql);
}

function getEventsDict() {

    $rows = \db::rows("SELECT "
                    . " unofficial_events_dict.id id,"
                    . " unofficial_events_dict.image, "
                    . " unofficial_events_dict.name,"
                    . " unofficial_events_dict.code,"
                    . " unofficial_events_dict.result_dict,"
                    . " unofficial_events_dict.special"
                    . " FROM unofficial_events_dict "
                    . " ORDER BY id");
    $events_dict = [];
    foreach ($rows as $row) {
        $events_dict[$row->id] = $row;
    }
    return $events_dict;
}

function getFormatsDict() {

    $rows = \db::rows("SELECT "
                    . " unofficial_formats_dict.id id,"
                    . " unofficial_formats_dict.format, "
                    . " unofficial_formats_dict.attempts,"
                    . " unofficial_formats_dict.name,"
                    . " unofficial_formats_dict.code"
                    . " FROM unofficial_formats_dict "
                    . " ORDER BY code");
    $formats_dict = [];
    foreach ($rows as $row) {
        $formats_dict[$row->id] = $row;
    }
    return $formats_dict;
}

function getRoundsDict() {

    $rows = \db::rows("SELECT "
                    . " id, name, image"
                    . " FROM unofficial_rounds_dict "
                    . " ORDER BY id");
    $rounds_dict = [];
    foreach ($rows as $row) {
        $rounds_dict[$row->id] = $row;
    }
    return $rounds_dict;
}

function getResultsDict() {

    $rows = \db::rows("SELECT "
                    . " id, name"
                    . " FROM unofficial_results_dict "
                    . " ORDER BY id");
    $results_dict = [];
    foreach ($rows as $row) {
        $results_dict[$row->id] = $row;
    }
    return $results_dict;
}

function getEvents($id) {

    return \db::rows("SELECT "
                    . " unofficial_events_dict.id event_dict,"
                    . " COALESCE(unofficial_events.name, unofficial_events_dict.name) name,"
                    . " COALESCE(unofficial_events.result_dict, unofficial_events_dict.result_dict) result_dict,"
                    . " unofficial_events.id, "
                    . " unofficial_events.rounds, "
                    . " unofficial_events.format_dict"
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id "
                    . " ORDER BY  unofficial_events_dict.id");
}

function getEventsRounds($id) {
    $rows = \db::rows("SELECT "
                    . " unofficial_events_rounds.event, "
                    . " unofficial_events_rounds.round, "
                    . " unofficial_events_rounds.comment "
                    . " FROM unofficial_events "
                    . " JOIN unofficial_events_rounds ON  unofficial_events_rounds.event = unofficial_events.id"
                    . " WHERE unofficial_events.competition = $id ");

    $events_rounds = [];
    foreach ($rows as $row) {
        $events_rounds[$row->event][$row->round] = $row;
    }

    return $events_rounds;
}

function getCountsByComp($id) {
    $rows = \db::rows("SELECT "
                    . " MAX(Round) rounds,"
                    . " MAX(format) MeetingFormat,"
                    . " unofficial_events_dict.id event_dict,"
                    . " SUM(COALESCE(unofficial_competitors_events.place,0)) withResult,"
                    . " Count(distinct unofficial_competitors.id) competitors,"
                    . " SUM(unofficial_events.amount) amount  "
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " LEFT OUTER JOIN unofficial_competitors ON unofficial_competitors.competition = unofficial_events.id"
                    . " LEFT OUTER JOIN unofficial_competitors_events "
                    . "     ON unofficial_competitors_events.competitor = unofficial_competitors.id "
                    . "     AND unofficial_competitors_events.event = unofficial_events.id"
                    . " WHERE unofficial_events.competition = $id"
                    . " GROUP BY unofficial_events_dict.id");
    $results = [];
    foreach ($rows as $row) {
        $results[$row->event_dict] = $row;
    }
    return $results;
}

function generateSecret() {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    foreach (range(0, 9) as $empty) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}

function getCompetitionData($id) {
    $data = new \stdClass();
    $data->competition = new \stdClass();
    $data->competition->competitors = [];
    foreach (\db::rows("SELECT id, name FROM unofficial_competitors WHERE competition = $id ORDER BY name") as $competitor) {
        $data->competition->competitors[$competitor->id] = $competitor;
    }
    $data->competition->events = [];
    foreach (\db::rows("SELECT DISTINCT event_dict FROM unofficial_events WHERE competition = $id") as $event) {
        $data->competition->events[] = $event;
    }
    $data->competition->rounds_max = \db::row("SELECT MAX(rounds) rounds FROM unofficial_events WHERE competition = $id")->rounds ?? 0;
    $data->competition->delete = !($data->competition->competitors OR $data->competition->events);


    $events = \db::rows("SELECT "
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events.rounds,"
                    . " unofficial_events.result_dict,"
                    . " unofficial_events.format_dict, "
                    . " unofficial_events.rounds event_rounds, "
                    . " unofficial_events.id,"
                    . " COALESCE(unofficial_events.result_dict, unofficial_events_dict.result_dict) result_dict, "
                    . " COALESCE(unofficial_events.name, unofficial_events_dict.name) name "
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id");
    $data->events = [];
    foreach ($events as $event) {
        $data->events[$event->event_dict] = $event;
        $data->events[$event->event_dict]->rounds = [];
        foreach (\db::rows("SELECT round, id "
                . " FROM unofficial_events_rounds"
                . " WHERE event = $event->id") as $round) {
            $data->events[$event->event_dict]->rounds[$round->round] = $round;
        }
    }


    $rounds = \db::rows("SELECT "
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events_rounds.id,"
                    . " unofficial_events_rounds.event,"
                    . " unofficial_events_rounds.round,"
            . " unofficial_events.rounds rounds,"
                    . " unofficial_events_rounds.comment"
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_rounds ON unofficial_events_rounds.event = unofficial_events.id"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id"
                    . " ORDER by unofficial_events_dict.order, unofficial_events_rounds.round");
    $data->rounds = [];
    $data->event_rounds = [];
    foreach ($rounds as $round) {
        $data->event_rounds[$round->id] = $round;
        $data->rounds[$round->event_dict][$round->round] = new \stdClass();
        $data->rounds[$round->event_dict][$round->round]->round = $round;
        foreach (\db::rows("SELECT "
                . " unofficial_competitors_round.competitor id, "
                . " unofficial_competitors_result.attempts, "
                . " unofficial_competitors_result.place "
                . " FROM unofficial_competitors_round"
                . " JOIN unofficial_competitors on unofficial_competitors.id = unofficial_competitors_round.competitor"
                . " LEFT OUTER JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id "
                . " JOIN unofficial_events_rounds ON unofficial_competitors_round.round = unofficial_events_rounds.id"
                . " WHERE unofficial_events_rounds.event = $round->event "
                . "AND unofficial_events_rounds.round = $round->round "
                . "ORDER BY unofficial_competitors.name") as $competitor) {
            $data->rounds[$round->event_dict][$round->round]->competitors[$competitor->id] = $competitor;
        }
    }


    $competitors = \db::rows("SELECT "
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events_rounds.event,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_competitors.id competitor,"
                    . " unofficial_competitors.name,"
                    . " unofficial_competitors.id"
                    . " FROM unofficial_competitors"
                    . " LEFT OUTER JOIN unofficial_competitors_round ON unofficial_competitors_round.competitor = unofficial_competitors.id"
                    . " LEFT OUTER JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " LEFT OUTER JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " LEFT OUTER JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_competitors.competition = $id");
    $data->competitors = [];
    foreach ($competitors as $competitor) {
        $data->competitors[$competitor->id] = $competitor;
        $competitor_events = \db::rows("SELECT "
                        . " DISTINCT unofficial_events.event_dict, "
                        . " MAX(CASE WHEN unofficial_competitors_result.competitor_round IS NOT NULL THEN true ELSE false END) result "
                        . " FROM unofficial_competitors_round "
                        . " LEFT OUTER JOIN unofficial_competitors_result ON unofficial_competitors_result.competitor_round = unofficial_competitors_round.id "
                        . " JOIN unofficial_competitors on unofficial_competitors.id = unofficial_competitors_round.competitor "
                        . " JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round "
                        . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event "
                        . " WHERE unofficial_competitors.id = $competitor->id"
                        . " GROUP BY unofficial_events.event_dict");
        $data->competitors[$competitor->id]->events = [];
        foreach ($competitor_events as $competitor_event) {
            $data->competitors[$competitor->id]->events[$competitor_event->event_dict] = $competitor_event;
        }

        $data->competitors[$competitor->id]->delete = !sizeof(\db::rows("SELECT id "
                                . " FROM unofficial_competitors_result "
                                . " JOIN unofficial_competitors_round on unofficial_competitors_round.id = unofficial_competitors_result.competitor_round"
                                . " WHERE unofficial_competitors_round.competitor = $competitor->id"));
    }

    $data->event_dict = new \stdClass();
    $data->event_dict->by_id = [];
    $data->event_dict->by_code = [];
    $event_dicts = \db::rows("SELECT "
                    . " unofficial_events_dict.id,"
                    . " unofficial_events_dict.image, "
                    . " unofficial_events_dict.name,"
                    . " unofficial_events_dict.code,"
                    . " unofficial_events_dict.result_dict,"
                    . " unofficial_events_dict.special"
                    . " FROM unofficial_events_dict "
                    . " ORDER BY id");
    foreach ($event_dicts as $event_dict) {
        $data->event_dict->by_id[$event_dict->id] = $event_dict;
        $data->event_dict->by_code[$event_dict->code] = $event_dict;
    }

    return $data;
}

function getCompetitorsSession($id, $session) {
    return \db::rows("SELECT id, name "
                    . " FROM unofficial_competitors "
                    . " WHERE session = '$session'"
                    . " AND competition = $id");
}

function getEventByEventround($eventround) {
    return \db::row("SELECT"
                    . " COALESCE(unofficial_events_rounds.comment,'') comment, "
                    . " unofficial_events_dict.image, "
                    . " unofficial_events_rounds.round, "
                    . "unofficial_events.rounds, "
                    . " unofficial_formats_dict.attempts, "
                    . " unofficial_formats_dict.format, "
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final, "
                    . " COALESCE(unofficial_events.name, unofficial_events_dict.name) name,"
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_results_dict.name result  "
                    . " FROM unofficial_events_rounds "
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict"
                    . " JOIN unofficial_results_dict on unofficial_results_dict.id = unofficial_events_dict.result_dict"
                    . " JOIN unofficial_formats_dict on unofficial_formats_dict.id = unofficial_events.format_dict"
                    . " WHERE unofficial_events_rounds.id = $eventround");
}

function getCompetitorsByEventround($eventround) {
    $competitors = [];
    foreach (\db::rows("SELECT"
            . " unofficial_competitors.name, "
            . " unofficial_competitors.id, "
            . " unofficial_competitors_result.place, "
            . " unofficial_competitors_round.id competitor_round, "
            . " unofficial_competitors_result.attempts, "
            . " unofficial_competitors_result.average, "
            . " unofficial_competitors_result.mean, "
            . " unofficial_competitors_result.best, "
            . " unofficial_competitors_result.attempt1, "
            . " unofficial_competitors_result.attempt2, "
            . " unofficial_competitors_result.attempt3, "
            . " unofficial_competitors_result.attempt4, "
            . " unofficial_competitors_result.attempt5, "
            . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3 THEN 1 ELSE 0 END podium,"
            . " CASE WHEN competitors_round_next.id IS NOT NULL THEN 1 ELSE 0 END next_round "
            . " FROM unofficial_events_rounds "
            . " JOIN unofficial_competitors_round ON unofficial_competitors_round.round = unofficial_events_rounds.id"
            . " JOIN unofficial_competitors on unofficial_competitors_round.competitor = unofficial_competitors.id"
            . " LEFT OUTER JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
            . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
            . " LEFT OUTER JOIN unofficial_events_rounds events_rounds_next ON events_rounds_next.event = unofficial_events.id AND events_rounds_next.round = unofficial_events_rounds.round + 1"
            . " LEFT OUTER JOIN unofficial_competitors_round competitors_round_next ON competitors_round_next.round =  events_rounds_next.id AND competitors_round_next.competitor = unofficial_competitors.id"
            . " WHERE unofficial_events_rounds.id = $eventround"
            . " ORDER by COALESCE(unofficial_competitors_result.place,9999),"
            . " unofficial_competitors.name") as $competitor) {
        $competitors[$competitor->id] = $competitor;
    }
    return $competitors;
}

function getCompetitorsByEventdictRound($event_dict, $round) {
    $competitors = [];
    foreach (\db::rows("SELECT"
            . " unofficial_competitors.name, "
            . " unofficial_competitors.id, "
            . " unofficial_competitors_result.place, "
            . " unofficial_competitors_round.id competitor_round "
            . " FROM unofficial_events_rounds "
            . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
            . " JOIN unofficial_competitors_round ON unofficial_competitors_round.round = unofficial_events_rounds.id"
            . " JOIN unofficial_competitors on unofficial_competitors_round.competitor = unofficial_competitors.id"
            . " LEFT OUTER JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
            . " WHERE unofficial_events.event_dict = $event_dict"
            . " AND unofficial_events_rounds.round = $round"
            . " ORDER BY COALESCE(unofficial_competitors_result.place,999), unofficial_competitors.name ") as $competitor) {
        $competitors[$competitor->id] = $competitor;
    }
    return $competitors;
}

function attempt_to_int($attempt) {
    if (in_array($attempt, ['dnf', 'dns', '-cutoff', '0', false])) {
        return 999999;
    } else {
        $value = substr("0000000" . str_replace(['.', ':'], '', $attempt), -8, 8);
        $minute = substr($value, 0, 2);
        $second = substr($value, 3, 2);
        $milisecond = substr($value, 6, 2);
        return $minute * 100 * 60 + $second * 100 + $milisecond;
    }

    /*
      $value = DataBaseClass::Escape($value);
      if ($value == 'DNF' or $value == '-cutoff') {
      $mili = $mili * 1000000 + 999999;
      } else {
      $value_t = substr("0000000" . $value, -8, 8);
      $minute = substr($value_t, 0, 2);
      $second = substr($value_t, 3, 2);
      $milisecond = substr($value_t, 6, 2);
      $value = str_replace(array("00:0", "00:", "0:0", "0:"), "", $value);
      $mili = $mili * 1000000 + $minute * 100 * 60 + $second * 100 + $milisecond;

     */
}

function getCompetitor($competitor_id) {
    if (!is_numeric($competitor_id)) {
        return false;
    }
    return \db::row("SELECT "
                    . " unofficial_competitors.id, "
                    . " unofficial_competitors.name, "
                    . " dict_competitors.name creator_name,"
                    . " unofficial_competitions.id competition_id,"
                    . " unofficial_competitions.secret competition_secret"
                    . " FROM unofficial_competitors"
                    . " JOIN unofficial_competitions ON unofficial_competitions.id = unofficial_competitors.competition "
                    . " JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor "
                    . " WHERE unofficial_competitors.id = $competitor_id");
}

function getCompetitionsByCompetitor($competitor_id) {

    return \db::rows("SELECT"
                    . " unofficial_competitions.name,"
                    . " unofficial_competitions.date,"
                    . " unofficial_competitions.secret, "
                    . " unofficial_competitors.id competitor_id "
                    . " FROM unofficial_competitions "
                    . " JOIN unofficial_competitors ON unofficial_competitors.competition = unofficial_competitions.id "
                    . " JOIN unofficial_competitors main_competitor ON main_competitor.name = unofficial_competitors.name "
                    . " JOIN unofficial_competitions main_competition ON main_competition.id = main_competitor.competition "
                    . " AND main_competition.competitor = unofficial_competitions.competitor "
                    . " WHERE main_competitor.id = $competitor_id"
                    . " ORDER by unofficial_competitions.date DESC");
}

function getResutsByCompetitorMain($competitor_id) {
    return \db::rows("SELECT"
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events_dict.name event_name,"
                    . " unofficial_events_dict.image event_image,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_competitors_result.place, "
                    . " unofficial_competitions.name competition_name, "
                    . " unofficial_competitions.date competition_date, "
                    . " unofficial_competitions.secret,"
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final, "
                    . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3 THEN 1 ELSE 0 END podium,"
                    . " unofficial_competitors_result.attempt1, "
                    . " unofficial_competitors_result.attempt2, "
                    . " unofficial_competitors_result.attempt3, "
                    . " unofficial_competitors_result.attempt4, "
                    . " unofficial_competitors_result.attempt5, "
                    . " unofficial_competitors_result.average, "
                    . " unofficial_competitors_result.mean, "
                    . " unofficial_competitors_result.best "
                    . " FROM unofficial_competitions "
                    . " JOIN unofficial_competitors ON unofficial_competitors.competition = unofficial_competitions.id "
                    . " JOIN unofficial_competitors_round ON unofficial_competitors_round.competitor = unofficial_competitors.id"
                    . " JOIN unofficial_competitors_result ON unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
                    . " JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN  unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict "
                    . " JOIN unofficial_competitors main_competitor ON main_competitor.name = unofficial_competitors.name "
                    . " JOIN unofficial_competitions main_competition ON main_competition.id = main_competitor.competition "
                    . " AND main_competition.competitor = unofficial_competitions.competitor "
                    . " WHERE main_competitor.id = $competitor_id"
                    . " AND unofficial_events_dict.special = 0"
                    . " ORDER BY "
                    . " unofficial_events_dict.name,"
                    . " unofficial_events_rounds.round DESC,"
                    . " unofficial_competitions.date DESC");
}

function getResutsByCompetitor($competitor_id) {
    return \db::rows("SELECT"
                    . " unofficial_events_dict.id event_dict,"
                    . " COALESCE(unofficial_events_dict.name, unofficial_events.name) event_name,"
                    . " unofficial_events_dict.image event_image,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_competitors_result.place, "
                    . " unofficial_competitions.id competition_id, "
                    . " unofficial_competitions.name competition_name, "
                    . " unofficial_competitions.secret,"
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final,"
                    . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3 THEN 1 ELSE 0 END podium,"
                    . "unofficial_rounds_dict.name round_name,  "
                    . " unofficial_competitors_result.attempt1, "
                    . " unofficial_competitors_result.attempt2, "
                    . " unofficial_competitors_result.attempt3, "
                    . " unofficial_competitors_result.attempt4, "
                    . " unofficial_competitors_result.attempt5, "
                    . " unofficial_competitors_result.average, "
                    . " unofficial_competitors_result.mean, "
                    . " unofficial_competitors_result.best,"
                    . " unofficial_competitors.name competitor_name,"
                    . " unofficial_competitors.id competitor_id"
                    . " FROM unofficial_competitions "
                    . " JOIN unofficial_competitors ON unofficial_competitors.competition = unofficial_competitions.id "
                    . " JOIN unofficial_competitors_round ON unofficial_competitors_round.competitor = unofficial_competitors.id"
                    . " JOIN unofficial_competitors_result ON unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
                    . " JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN  unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict "
                    . " JOIN unofficial_rounds_dict on unofficial_rounds_dict.id = unofficial_events_rounds.round "
                    . " WHERE unofficial_competitors.id = $competitor_id"
                    . " ORDER BY "
                    . " unofficial_events_dict.name,"
                    . " unofficial_events_rounds.round DESC");
}

function getFavicon($url) {
    return "http://www.google.com/s2/favicons?domain=$url";
}