<?php

namespace unofficial;

require_once 'rankings_functions.php';

function getCompetition($secret, $me = FALSE) {
    $me_id = ($me->id ?? -1);
    $me_wcaid = ($me->wca_id ?? -1);
    $admin = (\api\get_me()->is_admin ?? false) ? 'TRUE' : 'FALSE';
    $RU = t('', 'RU');
    $sql = "SELECT
        unofficial_competitions.website,
        unofficial_competitions.city,
        unofficial_competitions.id,
        unofficial_competitions.show, 
        unofficial_points_dict.code points,
        unofficial_competitions.competitor,
        coalesce(unofficial_competitions.rankedID, unofficial_competitions.secret) secret,
        unofficial_competitions.secret secret_base,
        unofficial_competitions.secretRegistration,
        unofficial_competitions.shareRegistration,
        unofficial_competitions.name,
        unofficial_competitions.details,
        unofficial_competitions.logo,
        unofficial_competitions.date,
        unofficial_competitions.date_to,
        unofficial_competitions.ranked,
        unofficial_competitions.rankedApproved approved,
        unofficial_competitions.rankedID,
        unofficial_competitions.rankedCompetitors,
        coalesce(dict_competitors.name$RU,dict_competitors.name) competitor_name,
        dict_competitors.nameRU competitor_nameRU,
        dict_competitors.name competitor_nameEN,
        dict_competitors.wcaid competitor_wcaid,
        dict_competitors.country competitor_country,
        (unofficial_competitions.competitor = $me_id OR $admin) my,
        unofficial_organizers.id > 0 organizer
    FROM unofficial_competitions
    LEFT OUTER JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor 
    LEFT OUTER JOIN unofficial_points_dict on unofficial_points_dict.id = unofficial_competitions.points 
    LEFT OUTER JOIN unofficial_organizers 
    ON unofficial_organizers.competition = unofficial_competitions.id and unofficial_organizers.wcaid='$me_wcaid' 
    WHERE  (unofficial_competitions.secret = '$secret' or upper(unofficial_competitions.rankedID) = upper('$secret'))
    ";
    return \db::row($sql);
}

function getEventsDict() {
    $RU = t('', 'RU');
    $rows = \db::rows("SELECT "
                    . " unofficial_events_dict.id id,"
                    . " unofficial_events_dict.image, "
                    . " coalesce(unofficial_events_dict.name$RU,unofficial_events_dict.name) name,"
                    . " unofficial_events_dict.code,"
                    . " unofficial_events_dict.result_dict,"
                    . " unofficial_events_dict.special"
                    . " FROM unofficial_events_dict "
                    . " ORDER BY `order`");
    $events_dict = [];
    foreach ($rows as $row) {
        $row->extraevents = (strpos($row->image, 'ee-') !== false);
        $events_dict[$row->id] = $row;
    }
    return $events_dict;
}

function getFormatsDict() {
    $RU = t('', 'RU');
    $rows = \db::rows("SELECT "
                    . " unofficial_formats_dict.id id,"
                    . " unofficial_formats_dict.format, "
                    . " unofficial_formats_dict.attempts,"
                    . " unofficial_formats_dict.name$RU name,"
                    . " unofficial_formats_dict.code,"
                    . " unofficial_formats_dict.cutoff_attempts,"
                    . " unofficial_formats_dict.cutoff_name$RU cutoff_name"
                    . " FROM unofficial_formats_dict "
                    . " ORDER BY code");
    $formats_dict = [];
    foreach ($rows as $row) {
        $formats_dict[$row->id] = $row;
    }
    return $formats_dict;
}

function getRoundsDict() {
    $RU = t('', 'RU');
    $rows = \db::rows("SELECT "
                    . " id, name, image, fullName$RU fullName, fullName fullNameEN, smallName$RU smallName"
                    . " FROM unofficial_rounds_dict "
                    . " ORDER BY id");
    $rounds_dict = [];
    foreach ($rows as $row) {
        $rounds_dict[$row->id] = $row;
    }
    $rounds_dict[0] = $row;
    return $rounds_dict;
}

function getResultsDict() {
    $RU = t('', 'RU');
    $rows = \db::rows("SELECT "
                    . " id, name$RU name, code, smallName"
                    . " FROM unofficial_results_dict "
                    . " ORDER BY id");
    $results_dict = [];
    foreach ($rows as $row) {
        $results_dict[$row->id] = $row;
    }
    return $results_dict;
}

function getPointsDict() {
    $RU = t('', 'RU');
    $rows = \db::rows("SELECT "
                    . " id, name$RU name, code, icon, description$RU description"
                    . " FROM unofficial_points_dict "
                    . " ORDER BY id");
    $results_dict = [];
    foreach ($rows as $row) {
        $results_dict[$row->code] = $row;
    }
    return $results_dict;
}

function getEvents($id) {
    $RU = t('', 'RU');
    return \db::rows("SELECT "
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events_dict.code code,"
                    . " COALESCE(unofficial_events.name,unofficial_events_dict.name$RU) name,"
                    . " COALESCE(unofficial_events.result_dict, unofficial_events_dict.result_dict) result_dict,"
                    . " unofficial_events.id, "
                    . " unofficial_events.rounds, "
                    . " unofficial_events.format_dict"
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id "
                    . " ORDER BY  unofficial_events_dict.`order`");
}

function getEventsRounds($id) {
    $rows = \db::rows("SELECT "
                    . " unofficial_events_rounds.event, "
                    . " unofficial_events_rounds.round, "
                    . " unofficial_events_rounds.comment, "
                    . " unofficial_events_rounds.cutoff, "
                    . " unofficial_events_rounds.time_limit, "
                    . " unofficial_events_rounds.time_limit_cumulative, "
                    . " unofficial_events_rounds.next_round_value, "
                    . " unofficial_events_rounds.next_round_procent "
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
    $RU = t('', 'RU');
    $data = new \stdClass();
    $data->competition = new \stdClass();
    $data->competition->competitors = [];
    foreach (\db::rows("SELECT id, name, FCID FROM unofficial_competitors WHERE competition = $id ORDER BY name") as $competitor) {
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
                    . " unofficial_events_dict.code event_code,"
                    . " unofficial_events_dict.special,"
                    . " unofficial_events_dict.image,"
                    . " unofficial_events.rounds,"
                    . " unofficial_events.result_dict,"
                    . " unofficial_events.format_dict, "
                    . " unofficial_events.rounds event_rounds, "
                    . " unofficial_events.id, "
                    . " COALESCE(unofficial_events.result_dict, unofficial_events_dict.result_dict) result_dict, "
                    . " COALESCE(unofficial_events.name,unofficial_events_dict.name$RU) name "
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id"
                    . " ORDER BY unofficial_events_dict.`order` ");
    $data->events = [];
    foreach ($events as $event) {
        $event->extraevents = (strpos($event->image, 'ee-') !== false);
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
                    . " unofficial_events.format_dict,"
                    . " unofficial_events_rounds.comment,"
                    . " unofficial_events_rounds.cutoff,"
                    . " unofficial_events_rounds.time_limit,"
                    . " unofficial_events_rounds.time_limit_cumulative,"
                    . " unofficial_events_rounds.next_round_procent,"
                    . " unofficial_events_rounds.next_round_value, "
                    . " unofficial_events.update_at "
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_rounds ON unofficial_events_rounds.event = unofficial_events.id"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id"
                    . " ORDER by unofficial_events_dict.`order`, unofficial_events_rounds.round");
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
                . " LEFT OUTER JOIN unofficial_fc_wca on unofficial_fc_wca.FCID = unofficial_competitors.FCID"
                . " WHERE unofficial_events_rounds.event = $round->event "
                . "AND unofficial_events_rounds.round = $round->round "
                . "ORDER BY case when 'RU'='$RU' then unofficial_competitors.name else coalesce(unofficial_fc_wca.wca_name,unofficial_competitors.name) end") as $competitor) {
            $data->rounds[$round->event_dict][$round->round]->competitors[$competitor->id] = $competitor;
        }
    }


    $competitors = \db::rows("SELECT "
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events_rounds.event,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_competitors.id competitor,"
                    . " case when 'RU'='$RU' then unofficial_competitors.name else coalesce(unofficial_fc_wca.wca_name,unofficial_competitors.name) end name,"
                    . " unofficial_competitors.name name_original, "
                    . " unofficial_competitors.FCID,"
                    . " unofficial_competitors.card, "
                    . " unofficial_competitors.id, "
                    . " unofficial_competitors.non_resident, "
                    . " unofficial_fc_wca.wcaid, "
                    . " unofficial_fc_wca.nonwca "
                    . " FROM unofficial_competitors"
                    . " LEFT OUTER JOIN unofficial_competitors_round ON unofficial_competitors_round.competitor = unofficial_competitors.id"
                    . " LEFT OUTER JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " LEFT OUTER JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " LEFT OUTER JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " LEFT OUTER JOIN unofficial_fc_wca on unofficial_fc_wca.FCID = unofficial_competitors.FCID"
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

        $FCID_candidates = [];
        $name = \db::escape($competitor->name);
        foreach (\db::rows("select FCID "
                . "from `unofficial_competitors` "
                . "where upper(trim(name)) = upper(trim('$name')) "
                . "and FCID is not null and FCID<>'' and FCID<>'$competitor->FCID'") as $FCID_candidate) {
            $FCID_candidates[] = $FCID_candidate->FCID;
        }
        $data->competitors[$competitor->id]->FCID_candidates = $FCID_candidates;
    }

    $data->event_dict = new \stdClass();
    $data->event_dict->by_id = [];
    $data->event_dict->by_code = [];
    $event_dicts = \db::rows("SELECT "
                    . " unofficial_events_dict.id,"
                    . " unofficial_events_dict.image, "
                    . " coalesce(unofficial_events_dict.name$RU, unofficial_events_dict.name) name,"
                    . " unofficial_events_dict.code,"
                    . " unofficial_events_dict.result_dict,"
                    . " unofficial_events_dict.special"
                    . " FROM unofficial_events_dict "
                    . " ORDER BY id");
    foreach ($event_dicts as $event_dict) {
        $data->event_dict->by_id[$event_dict->id] = $event_dict;
        $data->event_dict->by_code[$event_dict->code] = $event_dict;
    }

    $organizers = \db::rows("SELECT "
                    . " coalesce(dict_competitors.name$RU,dict_competitors.name) competitor_name,"
                    . " dict_competitors.name competitor_nameEN,"
                    . " dict_competitors.nameRU competitor_nameRU,"
                    . " unofficial_organizers.wcaid competitor_wcaid"
                    . " FROM unofficial_organizers"
                    . " LEFT OUTER JOIN dict_competitors on unofficial_organizers.wcaid in(dict_competitors.wcaid, dict_competitors.wid) "
                    . " WHERE competition=$id");
    $data->organizers = $organizers;

    $delegates = \db::rows("SELECT 
                            cj.delegate wcaid,
                            coalesce(dc.name$RU, dc.name) name,
                            coalesce(jrd.role$RU, jrd.role) role
                        FROM unofficial_competition_delegates cj
                        JOIN unofficial_delegate_roles_dict jrd on jrd.id=cj.dict_delegate_role
                        JOIN dict_competitors dc on dc.wcaid=cj.delegate 
                        WHERE cj.competition_id=$id");

    $data->delegates = $delegates;

    $data->competitors = resolved_competitor_dublicate($data->competitors);

    return $data;
}

function resolved_competitor_dublicate($competitors) {
    $count_names = [];
    foreach ($competitors as $competitor_id => $competitor) {
        $name_wo_fcid = trim(str_replace($competitor->FCID, '', $competitor->name));
        if (trim($competitor->name) != $name_wo_fcid) {
            $competitors[$competitor_id]->name_clear = $name_wo_fcid;
            $competitors[$competitor_id]->fcid_show = true;
            $count_names[$competitor->name_clear] ??= 0;
        } else {
            $competitors[$competitor_id]->name_clear = $competitor->name;
        }
    }
    foreach ($competitors as $competitor_id => $competitor) {
        if (isset($count_names[$competitor->name_clear])) {
            $count_names[$competitor->name_clear]++;
        }
    }

    foreach (array_keys($count_names) as $name) {
        if ($count_names[$name] < 2) {
            unset($count_names[$name]);
        }
    }
    foreach ($competitors as $competitor_id => $competitor) {
        $competitors[$competitor_id]->fcid_show = isset($count_names[$competitor->name_clear]);
        $competitors[$competitor_id]->name_full = $competitor->name_clear . ' ' .
                ($competitors[$competitor_id]->fcid_show ? " $competitor->FCID" : '');
    }
    return $competitors;
}

function getCompetitorsSession($id, $session) {
    return \db::rows("SELECT id, name "
                    . " FROM unofficial_competitors "
                    . " WHERE session = '$session'"
                    . " AND competition = $id");
}

function getEventByEventround($eventround) {
    $RU = t('', 'RU');
    return \db::row("SELECT"
                    . " COALESCE(unofficial_events_rounds.comment,'') comment, "
                    . " COALESCE(unofficial_events_rounds.cutoff,'') cutoff, "
                    . " COALESCE(unofficial_events_rounds.time_limit,'') time_limit, "
                    . " COALESCE(unofficial_events_rounds.time_limit_cumulative,'') time_limit_cumulative, "
                    . " unofficial_events_dict.image, "
                    . " unofficial_events_dict.special, "
                    . " unofficial_events_rounds.round, "
                    . " unofficial_events_rounds.next_round_procent, "
                    . " unofficial_events_rounds.next_round_value, "
                    . " unofficial_events.rounds, "
                    . " unofficial_formats_dict.attempts, "
                    . " unofficial_formats_dict.format, "
                    . " unofficial_formats_dict.cutoff_attempts, "
                    . " unofficial_events_dict.code, "
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final, "
                    . " COALESCE(unofficial_events.name,unofficial_events_dict.name$RU) name,"
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_results_dict.name result_name,  "
                    . " coalesce(event_unofficial_results_dict.code, unofficial_results_dict.code) result_code  "
                    . " FROM unofficial_events_rounds "
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict"
                    . " JOIN unofficial_results_dict on (unofficial_results_dict.id = unofficial_events.result_dict OR unofficial_results_dict.id = unofficial_events_dict.result_dict)"
                    . " JOIN unofficial_formats_dict on unofficial_formats_dict.id = unofficial_events.format_dict "
                    . " LEFT OUTER JOIN unofficial_results_dict event_unofficial_results_dict on event_unofficial_results_dict.id = unofficial_events.result_dict"
                    . " WHERE unofficial_events_rounds.id = $eventround");
}

function getCompetitorsByEventround($eventround, $event = null) {
    $RU = t('', 'RU');
    $competitors = [];
    foreach (\db::rows("SELECT"
            . " case when 'RU'='$RU' then unofficial_competitors.name else coalesce(unofficial_fc_wca.wca_name,unofficial_competitors.name) end name,"
            . " unofficial_competitors.FCID, "
            . " unofficial_competitors.id, "
            . " unofficial_competitors.card, "
            . " unofficial_competitors.non_resident, "
            . " unofficial_competitors_result.place, "
            . " unofficial_competitors_round.id competitor_round, "
            . " unofficial_competitors_result.attempts, "
            . " unofficial_competitors_result.average, "
            . " unofficial_competitors_result.mean, "
            . " unofficial_competitors_result.best, "
            . " unofficial_competitors_result.order, "
            . " unofficial_competitors_result.attempt1, "
            . " unofficial_competitors_result.attempt2, "
            . " unofficial_competitors_result.attempt3, "
            . " unofficial_competitors_result.attempt4, "
            . " unofficial_competitors_result.attempt5, "
            . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3  and upper(unofficial_competitors_result.best)!='DNF' THEN 1 ELSE 0 END podium,"
            . " CASE WHEN competitors_round_next.id IS NOT NULL THEN 1 ELSE 0 END next_round "
            . " FROM unofficial_events_rounds "
            . " JOIN unofficial_competitors_round ON unofficial_competitors_round.round = unofficial_events_rounds.id"
            . " JOIN unofficial_competitors on unofficial_competitors_round.competitor = unofficial_competitors.id"
            . " LEFT OUTER JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
            . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
            . " LEFT OUTER JOIN unofficial_events_rounds events_rounds_next ON events_rounds_next.event = unofficial_events.id AND events_rounds_next.round = unofficial_events_rounds.round + 1"
            . " LEFT OUTER JOIN unofficial_competitors_round competitors_round_next ON competitors_round_next.round =  events_rounds_next.id AND competitors_round_next.competitor = unofficial_competitors.id"
            . " LEFT OUTER JOIN unofficial_fc_wca on unofficial_fc_wca.FCID = unofficial_competitors.FCID"
            . " WHERE unofficial_events_rounds.id = $eventround"
            . " ORDER by COALESCE(unofficial_competitors_result.place,9999),"
            . " unofficial_competitors.name") as $competitor) {
        $competitors[$competitor->id] = $competitor;
    }

    if ($event) {
        $place_competitors = 0;
        $next_round_competitors = 0;
        foreach ($competitors as $competitor) {
            $competitor->next_round_register = $competitor->next_round;
            if ($competitor->place) {
                $place_competitors++;
            }
            if ($competitor->next_round) {
                $next_round_competitors++;
            }
        }
        if (!$event->final and!$next_round_competitors and $event->next_round_value) {
            if ($event->next_round_procent) {
                $next_round_competitors = floor($place_competitors / 100.0 * $event->next_round_value);
            } else {
                $next_round_competitors = min([
                    floor($place_competitors / 100.0 * 75),
                    $event->next_round_value
                ]);
            }
            foreach ($competitors as $competitor) {
                if ($competitor->place and $competitor->place <= $next_round_competitors) {
                    $competitor->next_round = true;
                } else {
                    $competitor->next_round = false;
                }
            }
        }
    }

    $competitors = resolved_competitor_dublicate($competitors);

    return $competitors;
}

function getCompetitorsByEventdictRound($comp_id, $event_dict, $round) {
    $competitors = [];
    foreach (\db::rows("SELECT"
            . " unofficial_competitors.name, "
            . " unofficial_competitors.FCID, "
            . " unofficial_competitors.id, "
            . " unofficial_competitors_result.place, "
            . " unofficial_competitors_round.id competitor_round "
            . " FROM unofficial_events_rounds "
            . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
            . " JOIN unofficial_competitors_round ON unofficial_competitors_round.round = unofficial_events_rounds.id"
            . " JOIN unofficial_competitors on unofficial_competitors_round.competitor = unofficial_competitors.id"
            . " LEFT OUTER JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
            . " WHERE unofficial_events.competition =$comp_id "
            . " AND unofficial_events.event_dict = $event_dict"
            . " AND unofficial_events_rounds.round = $round"
            . " ORDER BY COALESCE(unofficial_competitors_result.place,999), unofficial_competitors.name ") as $competitor) {
        $competitors[$competitor->id] = $competitor;
    }
    return $competitors;
}

function attempt_to_int($attempt) {
    if (in_array($attempt, ['DNF', 'DNS', '0', false])) {
        return 999999;
    } else {
        $value = substr("00000" . str_replace(['.', ':'], '', $attempt), -6, 6);
        $minute = substr($value, 0, 2);
        $second = substr($value, 2, 2);
        $milisecond = substr($value, 4, 2);
        return $minute * 100 * 60 + $second * 100 + $milisecond;
    }
}

function attempt_to_int_fm($attempt) {
    if (in_array($attempt, ['DNF', 'DNS', '0', false])) {
        return 999999;
    } else {
        return $attempt;
    }
}

function getCompetitor($competitor_id) {
    if (!is_numeric($competitor_id)) {
        return false;
    }
    return \db::row("SELECT "
                    . " unofficial_competitors.id, "
                    . " unofficial_competitors.name, "
                    . " unofficial_competitors.FCID, "
                    . " unofficial_competitions.competitor creator_id,"
                    . " dict_competitors.name creator_name,"
                    . " unofficial_competitions.id competition_id,"
                    . " coalesce(unofficial_competitions.rankedID, unofficial_competitions.secret) competition_secret"
                    . " FROM unofficial_competitors"
                    . " JOIN unofficial_competitions ON unofficial_competitions.id = unofficial_competitors.competition "
                    . " JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor "
                    . " WHERE unofficial_competitors.id = $competitor_id");
}

function getOrganizer($organizer_id) {
    if (!is_numeric($organizer_id ?? null)) {
        return false;
    }
    return \db::row("SELECT "
                    . " dict_competitors.country, dict_competitors.name, dict_competitors.wid, dict_competitors.wcaid "
                    . " FROM dict_competitors"
                    . " JOIN unofficial_competitions on unofficial_competitions.competitor = dict_competitors.wid "
                    . " WHERE  dict_competitors.wid = $organizer_id");
}

function getOrganizers() {
    $organizers = [];
    foreach (\db::rows("select distinct "
            . " dict_competitors.wid, "
            . " dict_competitors.name, "
            . " dict_competitors.country, "
            . " dict_competitors.wcaid "
            . " from unofficial_competitions "
            . " join `dict_competitors` on `dict_competitors`.wid = `unofficial_competitions`.competitor"
            . " where unofficial_competitions.show = 1 "
            . " order by dict_competitors.name") as $organizer_row) {
        $organizers[$organizer_row->wid] = $organizer_row;
        $short_name = '';
        foreach (explode(" ", $organizer_row->name) as $word) {
            $short_name .= $word[0];
        }
        $organizers[$organizer_row->wid]->short_name = $short_name;
    }
    return $organizers;
}

function getPartners($organizer_id) {
    $partners = [];
    foreach (\db::rows("select "
            . " unofficial_partners.partner,"
            . " dict_competitors.name, "
            . " dict_competitors.country, "
            . " dict_competitors.wcaid "
            . " from unofficial_partners "
            . " join `dict_competitors` on `dict_competitors`.wid = `unofficial_partners`.partner"
            . " where unofficial_partners.competitor = $organizer_id"
            . " order by dict_competitors.name") as $partner) {
        $partners[$partner->partner] = $partner;
    }
    return $partners;
}

function getCompetitionsByCompetitor($competitor_id) {

    return \db::rows("SELECT"
                    . " unofficial_competitions.name,"
                    . " unofficial_competitions.date,"
                    . " unofficial_competitions.date_to,"
                    . " unofficial_competitions.website,"
                    . " (date(unofficial_competitions.date) > current_date) upcoming,"
                    . " (date(unofficial_competitions.date) = current_date 
                         or date(unofficial_competitions.date_to) = current_date ) run,"
                    . " coalesce(unofficial_competitions.rankedID, unofficial_competitions.secret) secret, "
                    . " unofficial_competitors.id competitor_id,"
                    . " unofficial_competitions.competitor competition_competitor_id, "
                    . " dict_competitors.name competition_competitor_name "
                    . " FROM unofficial_competitions "
                    . " JOIN unofficial_competitors ON unofficial_competitors.competition = unofficial_competitions.id "
                    . " JOIN unofficial_competitors main_competitor ON main_competitor.name = unofficial_competitors.name "
                    . " JOIN unofficial_competitions main_competition ON main_competition.id = main_competitor.competition "
                    . " JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor "
                    . " WHERE main_competitor.id = $competitor_id"
                    . " ORDER by unofficial_competitions.date DESC");
}

function getResutsByCompetitorMain($competitor_id) {
    $RU = t('', 'RU');
    return \db::rows("SELECT"
                    . " unofficial_events_dict.id event_dict,"
                    . " coalesce(unofficial_events.name,unofficial_events_dict.name$RU) event_name,"
                    . " unofficial_events_dict.image event_image,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_rounds_dict.smallName$RU round_name, "
                    . " unofficial_competitors_result.place, "
                    . " unofficial_competitions.name competition_name, "
                    . " unofficial_competitions.date competition_date, "
                    . " unofficial_competitions.date_to competition_date_to, "
                    . " unofficial_competitions.competitor competition_competitor_id, "
                    . " dict_competitors.name competition_competitor_name, "
                    . " unofficial_competitions.secret,"
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final, "
                    . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3 and upper(unofficial_competitors_result.best)!='DNF' THEN 1 ELSE 0 END podium,"
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
                    . " JOIN  unofficial_rounds_dict on unofficial_rounds_dict.id = unofficial_events_rounds.round "
                    . " JOIN unofficial_competitors main_competitor ON main_competitor.name = unofficial_competitors.name "
                    . " JOIN unofficial_competitions main_competition ON main_competition.id = main_competitor.competition "
                    . " JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor "
                    . " WHERE main_competitor.id = $competitor_id"
                    . " AND unofficial_events_dict.special = 0"
                    . " ORDER BY "
                    . " unofficial_events_dict.name,"
                    . " unofficial_competitions.date DESC,"
                    . " unofficial_events_rounds.round DESC");
}

function getResutsByCompetitor($competitor_id, $order = false) {
    $RU = t('', 'RU');
    if (!$order) {
        $order = "unofficial_events_dict.order, unofficial_events_rounds.round DESC";
    }
    return \db::rows("SELECT"
                    . " unofficial_competitors.FCID, "
                    . " unofficial_events_dict.id event_dict,"
                    . " unofficial_events_dict.code event_code,"
                    . " COALESCE(unofficial_events.name,unofficial_events_dict.name$RU) event_name,"
                    . " unofficial_events_dict.image event_image,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_events_rounds.id round_id,"
                    . " unofficial_competitors_result.place, "
                    . " unofficial_competitions.id competition_id, "
                    . " unofficial_competitions.name competition_name, "
                    . " unofficial_competitions.date competition_date_from, "
                    . " coalesce(unofficial_competitions.rankedID, unofficial_competitions.secret) secret,"
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final,"
                    . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3 and upper(unofficial_competitors_result.best)!='DNF' THEN 1 ELSE 0 END podium,"
                    . " unofficial_rounds_dict.name round_name,  "
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN unofficial_final_dict.fullName$RU ELSE unofficial_rounds_dict.fullName$RU END round_full_name,"
                    . " unofficial_competitors_result.attempt1, "
                    . " unofficial_competitors_result.attempt2, "
                    . " unofficial_competitors_result.attempt3, "
                    . " unofficial_competitors_result.attempt4, "
                    . " unofficial_competitors_result.attempt5, "
                    . " unofficial_competitors_result.average, "
                    . " unofficial_competitors_result.mean, "
                    . " unofficial_competitors_result.best,"
                    . " unofficial_competitors_result.order_best, "
                    . " unofficial_competitors.name competitor_name,"
                    . " unofficial_competitors.id competitor_id"
                    . " FROM unofficial_competitions "
                    . " JOIN unofficial_competitors ON unofficial_competitors.competition = unofficial_competitions.id "
                    . " JOIN unofficial_competitors_round ON unofficial_competitors_round.competitor = unofficial_competitors.id"
                    . " LEFT OUTER JOIN unofficial_competitors_result ON unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
                    . " JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict "
                    . " JOIN unofficial_rounds_dict on unofficial_rounds_dict.id = unofficial_events_rounds.round "
                    . " JOIN unofficial_rounds_dict unofficial_final_dict on unofficial_final_dict.id = 4"
                    . " WHERE unofficial_competitors.id = $competitor_id"
                    . " ORDER BY "
                    . $order);
}

function getFavicon($website, $only_domen) {
    if ($website) {
        preg_match('/.*(instagram|facebook).*/', $website, $ban);
        preg_match('/https?:\/\/(?:www\.|)([\w.-]+).*/', $website, $matches);

        if (isset($ban[1])) {
            return;
        }
        if ($only_domen) {
            $results = array_reverse(explode(".", $matches[1]));
            $result = $results[1] . "." . $results[0];
        } else {
            $result = $matches[1];
        }
        ?> 
        <a target="_blank" href="<?= $website ?>">
            <?= $result ?? $website ?>
        </a> 
        <?php
    }
}

function getLink($website) {
    if ($website) {
        preg_match('/.*(instagram|facebook).*/', $website, $ban);
        preg_match('/https?:\/\/(?:www\.|)([\w.-]+.*)/', $website, $matches);
        $result = $matches[1];
        $website_cut = substr($result, 0, 30);
        $website_cut .= ($website_cut == $result) ? '' : '...';
        if (isset($ban[1])) {
            return;
        }
        $result = $matches[1];
        ?> 
        <a target="_blank" href="<?= $website ?>">
            <?= $website_cut ?>
        </a> 
        <?php
    }
}

function updateCompetitionCard($competition_id) {
    $competitors = \db::rows("select `unofficial_competitors`.id, `unofficial_competitors`.card 
        from `unofficial_competitions` 
        join `unofficial_competitors` 
            on unofficial_competitors.competition=unofficial_competitions.id
        where `unofficial_competitions`.id = $competition_id 
            order by case when `unofficial_competitors`.card is null then 1 else 0 end,
                `unofficial_competitors`.card,
                `unofficial_competitors`.id
        ");
    $card = 0;
    foreach ($competitors as $competitor) {
        if ($competitor->card) {
            $card = $competitor->card;
        } else {
            $card++;
            \db::exec("update `unofficial_competitors` set card = $card where id = $competitor->id");
        }
    }
}

function getBestAttempts($comp_id) {
    $attempts = \db::rows("select
        e_dict.code,
        rounds.round,
        result.best,
        coalesce(result.mean, result.average) average,
        result.order,
        replace(replace(coalesce(result.mean,result.average),'.',''),':','') average_order
        from 
        `unofficial_competitors_result` result
        join `unofficial_competitors_round` round on round.id=result.competitor_round
        join `unofficial_events_rounds` rounds on rounds.id=round.round
        join `unofficial_events` events on events.id=rounds.event
        join `unofficial_events_dict` e_dict on e_dict.id=events.event_dict
        join `unofficial_competitions` competition on competition.id=events.competition
        where competition.id = $comp_id
        order by e_dict.code,rounds.round,result.order");
    $results = [];
    foreach ($attempts as $attempt) {
        if (!isset($best_order[$attempt->code][$attempt->round])
                or $best_order[$attempt->code][$attempt->round] > $attempt->order) {
            if ($attempt->best and!in_array(strtolower($attempt->average), ['dnf', 'dns'])) {
                $results[$attempt->code][$attempt->round]['best'] = str_replace(['(', ')'], '', $attempt->best);
                $best_order[$attempt->code][$attempt->round] = $attempt->order;
            }
        }
    }

    foreach ($attempts as $attempt) {
        if (!isset($average_order[$attempt->code][$attempt->round])
                or $average_order[$attempt->code][$attempt->round] > $attempt->average_order) {
            if ($attempt->average and!in_array($attempt->average, ['DNF'])) {
                $results[$attempt->code][$attempt->round]['average'] = $attempt->average;
                $average_order[$attempt->code][$attempt->round] = $attempt->average_order;
            }
        }
    }

    return $results;
}

function getText($code) {
    $text = \db::row("select text from unofficial_text where code='$code' and is_archive!=1")->text ?? false;
    if (!$text) {
        $L = t('EN', 'RU');
        $text = \db::row("select text from unofficial_text where code='$code$L' and is_archive!=1")->text ?? false;
    }
    return $text;
}

function getCompetitionPointsTop12($competition_id) {
    $RU = t('', 'RU');
    $rows = \db::rows("
        select 
            ed.id event_id,
            e.rounds,
            LEAST(count_competitor.count,12) count,
            c.id competitor_id,
            c.name competitor_name,
            c.FCID competitor_FCID,
            c_result.place,
            GREATEST(LEAST(count,12) + 1 - c_result.place,0) point
        from `unofficial_events` e 
        join `unofficial_events_rounds` er on er.event=e.id and er.round=e.rounds
        join `unofficial_events_dict` ed on ed.id=e.event_dict
        join (
                select count(*) count,round.round
                        from `unofficial_competitors_round` round
                        join `unofficial_competitors_result` result on result.competitor_round=round.id 
                        join `unofficial_events_rounds` er on er.id = round.round
                        join `unofficial_events` e on e.id=er.event
                where best!='DNF' or e.rounds >1
                group by round
        ) count_competitor on count_competitor.round=er.id
        join `unofficial_competitors_round` c_round on c_round.round = er.id
        join `unofficial_competitors_result` c_result on c_result.competitor_round = c_round.id 
        join `unofficial_competitors` c on c.id = c_round.competitor 
        where e.competition = $competition_id
            and ed.special = 0
            and GREATEST(LEAST(count,12) + 1 - c_result.place,0)>0
        order by ed.order");
    $results = (object) ['head' => [], 'competitors' => []];
    foreach ($rows as $row) {
        if (!isset($results->head[$row->event_id])) {
            $results->head[$row->event_id] = (object) [
                        'count' => $row->count,
                        'rounds' => $row->rounds
            ];
        }
        if (!isset($results->competitors[$row->competitor_id])) {
            $results->competitors[$row->competitor_id] = (object) [
                        'points' => 0,
                        'name' => $row->competitor_name,
                        'id' => $row->competitor_id,
                        'FCID' => $row->competitor_FCID,
                        'events' => [],
            ];
        }
        if ($row->point) {
            $results->competitors[$row->competitor_id]->points += $row->point;
        }
        $results->competitors[$row->competitor_id]->events[$row->event_id] = (object) [
                    'point' => $row->point,
                    'place' => $row->place
        ];
    }

    usort($results->competitors, function($a, $b) {
        if ($a->points != $b->points) {
            return $a->points < $b->points;
        }
        return $a->name > $b->name;
    });

    return $results;
}

function getCompetitionPointsAll($competition_id) {
    $RU = t('', 'RU');

    $rows = \db::rows("
       select 
	c.id,
	e.event_dict as event_id,
	counts.count - coalesce(cre4.place,cre3.place,cre2.place,cre1.place) + 1 point,
        counts.count as count,
	case
                when cre4.place is not null then 4 
		when cre3.place is not null then 3 
		when cre2.place is not null then 2 
		when cre1.place is not null then 1 
	end round,
        e.rounds,
        coalesce(cre4.place,cre3.place,cre2.place,cre1.place) as place,
	com.id competitor_id,
	com.name competitor_name,
	com.name competitor_FCID
        from unofficial_competitions c 
        join `unofficial_events` e on e.competition=c.ID
        left outer join `unofficial_events_rounds` er1 on er1.event=e.id and er1.round=1
        left outer join `unofficial_events_rounds` er2 on er2.event=e.id and er2.round=2
        left outer join `unofficial_events_rounds` er3 on er3.event=e.id and er3.round=3
        left outer join `unofficial_events_rounds` er4 on er4.event=e.id and er4.round=4
        left outer join `unofficial_competitors_round` cr1 on cr1.round=er1.id
        left outer join `unofficial_competitors_round` cr2 on cr2.round=er2.id and cr2.competitor=cr1.competitor
        left outer join `unofficial_competitors_round` cr3 on cr3.round=er3.id and cr3.competitor=cr1.competitor
        left outer join `unofficial_competitors_round` cr4 on cr4.round=er4.id and cr4.competitor=cr1.competitor
        left outer join `unofficial_competitors_result` cre1 on cre1.competitor_round=cr1.id
        left outer join `unofficial_competitors_result` cre2 on cre2.competitor_round=cr2.id
        left outer join `unofficial_competitors_result` cre3 on cre3.competitor_round=cr3.id
        left outer join `unofficial_competitors_result` cre4 on cre4.competitor_round=cr4.id
        left outer join `unofficial_competitors` com on cr1.competitor=com.id
        join 
        (select e.event_dict, c.id, count(distinct cro.competitor) count
        from `unofficial_competitors_result` cre
        join `unofficial_competitors_round` cro on cre.competitor_round=cro.id
        join `unofficial_events_rounds` er on er.id = cro.round
        join `unofficial_events` e on e.id =er.event
        join `unofficial_competitions` c on c.id=e.competition
        group by e.event_dict,e.competition) counts on counts.id=c.id and counts.event_dict=e.event_dict
        join `unofficial_events_dict` ed on ed.id=e.event_dict
        where c.id='$competition_id' and ed.special = 0
        order by ed.order");

    $results = (object) ['head' => [], 'competitors' => []];
    foreach ($rows as $row) {
        if (!isset($results->head[$row->event_id])) {
            $results->head[$row->event_id] = (object) [
                        'count' => $row->count,
                        'rounds' => $row->rounds
            ];
        }
        if (!isset($results->competitors[$row->competitor_id])) {
            $results->competitors[$row->competitor_id] = (object) [
                        'points' => 0,
                        'name' => $row->competitor_name,
                        'id' => $row->competitor_id,
                        'FCID' => $row->competitor_FCID,
                        'events' => [],
            ];
        }
        if ($row->point) {
            $results->competitors[$row->competitor_id]->points += $row->point;
        }
        $results->competitors[$row->competitor_id]->events[$row->event_id] = (object) [
                    'point' => $row->point,
                    'round' => $row->round,
                    'place' => $row->place
        ];
    }

    usort($results->competitors, function($a, $b) {
        if ($a->points != $b->points) {
            return $a->points < $b->points;
        }
        return $a->name > $b->name;
    });

    return $results;
}

function set_fc_id($id, $name) {

    $name1 = mb_substr(explode(' ', $name)[0] ?? false, 0, 1, "UTF-8");
    $name2 = mb_substr(explode(' ', $name)[1] ?? false, 0, 1, "UTF-8");
    $preinput = rus_lat($name1) . rus_lat($name2);
    $competitor_find = \db::row(
                    "select  distinct
                            cr.name,
                            cr.FCID,
                            case when cr.name='$name' then cr.FCID 
                            else concat('$preinput',right(concat('00',right(FCID,2)+1),2))
                            end FCID_set
                    from `unofficial_competitors` cr
                            join `unofficial_competitions` cn on cr.competition=cn.id
                    where cn.ranked=1 
                        and (left(FCID,2)='$preinput' or cr.name='$name')
                        and FCID is not null    
                    order by cr.name='$name' desc, FCID desc 
            ");

    if ($competitor_find->FCID_set ?? false) {
        $FCID_set = $competitor_find->FCID_set;
    } else {
        $FCID_set = $preinput . "01";
    }
    \db::exec("update `unofficial_competitors` set FCID='$FCID_set' where id=$id");
    return $FCID_set;
}

function getCompetitionSheets($competition_id) {
    $rows = \db::rows("
        select * from 
        unofficial_competition_sheets
        where is_archive = 0 and competition_id = $competition_id
        order by `order`");

    return $rows;
}

function getCompetitorWcaName($wcaid, $fcid) {
    if (!$wcaid) {
        $nameRU = \db::row("select * from unofficial_competitors where FCID = '$fcid'")->name ?? false;
        $name = transliterate($nameRU);
        return $name;
    }

    $name = \db2::row("select name from Persons where id='$wcaid' order by subid desc")->name ?? false;
    $name = trim(explode('(', $name)[0]);
    $rename = \db::row("select name from `unofficial_rename` where wcaid = '$wcaid'")->name ?? false;
    if ($rename) {
        $name = $rename;
    }
    if (!$name) {
        $nameRU = \db::row("select * from unofficial_competitors where FCID = '$fcid'")->name ?? false;
        $name = transliterate($nameRU);
    }
    return $name;
}

function getEvent($events_dict, $event_id) {
    foreach ($events_dict as $event) {
        if ($event->id === $event_id or $event->code === $event_id) {
            return $event;
        }
    }
    return null;
}
