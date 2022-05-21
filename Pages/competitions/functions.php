<?php

namespace unofficial;

require_once 'rankings_functions.php';

function admin() {
    return (\wcaoauth::me()->wca_id ?? FALSE) == \config::get('Admin', 'wcaid');
}

function federation() {
    return
            in_array((\wcaoauth::me()->wca_id ?? FALSE), explode(",", \config::get('Federation', 'wcaid')))
            or admin();
}

function getCompetitions($me, $mine, $ranked_only = false) {
    $me_id = ($me->id ?? -2);
    $me_wcaid = ($me->wca_id ?? -2);
    $admin = admin();
    $federation = federation();
    $RU = t('', 'RU');



    $organizer_ids = [];
    $judge_ids = [];
    $competitor_ids = [];

    if ($me_wcaid) {
        foreach (\db::rows(" 
                select distinct c.id from `unofficial_competitions` c
                join `unofficial_organizers` o on o.competition=c.id
                where lower(o.wcaid)=lower('$me_wcaid') 
                union 
                select distinct c.id from `unofficial_competitions` c
                where c.competitor = $me_id 
                ") as $row) {
            $organizer_ids[] = $row->id;
        }


        foreach (\db::rows(" select distinct c.id from `unofficial_competitions` c
                join `unofficial_competition_judges` j on j.competition_id=c.id
                where lower(j.judge)=lower('$me_wcaid') ") as $row) {
            $judge_ids[] = $row->id;
        }
        foreach (\db::rows(" select distinct cn.id from `unofficial_competitions` cn
                join `unofficial_competitors` cr on cr.competition=cn.id
                join unofficial_fc_wca wca on wca.FCID = cr.FCID
                where lower(wca.wcaid) =  lower('$me_wcaid')") as $row) {
            $competitor_ids[] = $row->id;
        }
    }
    if ($mine) {
        $ids = array_merge($judge_ids, $organizer_ids, $competitor_ids);
    } elseif ($ranked_only) {
        $competition_ids = \db::rows("
            select distinct 
                c.id 
            from `unofficial_competitions` c
            where  c.ranked " . (($admin or $federation) ? "" : "and c.show"));

        $ids = [];
        foreach ($competition_ids as $competition_id) {
            $ids[] = $competition_id->id;
        }
    } else {
        $competition_ids = \db::rows("
            select distinct 
                c.id 
            from `unofficial_competitions` c
            where 1=1 " . (($admin or $federation) ? "" : "and c.show"));

        $ids = $organizer_ids;
        foreach ($competition_ids as $competition_id) {
            $ids[] = $competition_id->id;
        }
    }

    $sql = "SELECT
        unofficial_competitions.website,
        unofficial_competitions.id,
        unofficial_competitions.show, 
        unofficial_competitions.competitor,
        coalesce(unofficial_competitions.rankedID, unofficial_competitions.secret) secret,
        unofficial_competitions.name,
        unofficial_competitions.details,
        unofficial_competitions.date,
        unofficial_competitions.date_to,
        unofficial_competitions.ranked,
        unofficial_competitions.rankedApproved approved,        
        (date(unofficial_competitions.date) > current_date) upcoming,
        (date(unofficial_competitions.date) = current_date 
        or date(unofficial_competitions.date_to) = current_date) run,
        coalesce(dict_competitors.name$RU, dict_competitors.name) competitor_name,
        dict_competitors.country competitor_country,
        dict_competitors.wcaid competitor_wcaid,
        without_FCID.count without_FCID,
        unofficial_competitions.id in ('" . implode("','", $judge_ids) . "') is_judge,
        unofficial_competitions.id in ('" . implode("','", $organizer_ids) . "') is_organizer,
        unofficial_competitions.id in ('" . implode("','", $competitor_ids) . "') is_competitor
    FROM unofficial_competitions
    JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor 
    left outer JOIN (select count(*) count,competition from `unofficial_competitors` where FCID is null group by competition) without_FCID 
        on without_FCID.competition=unofficial_competitions.id
    where unofficial_competitions.id in('" . implode("','", $ids) . "')    
    ORDER BY unofficial_competitions.date DESC";

    return \db::rows($sql);
}

function getCompetition($secret, $me = FALSE) {
    $me_id = ($me->id ?? -1);
    $me_wcaid = ($me->wca_id ?? -1);
    $admin = admin() ? 'TRUE' : 'FALSE';
    $RU = t('', 'RU');
    $sql = "SELECT
        unofficial_competitions.website,
        unofficial_competitions.id,
        unofficial_competitions.show, 
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
        coalesce(dict_competitors.name$RU,dict_competitors.name) competitor_name,
        dict_competitors.wcaid competitor_wcaid,
        dict_competitors.country competitor_country,
        (unofficial_competitions.competitor = $me_id OR $admin) my,
        unofficial_organizers.id > 0 organizer
    FROM unofficial_competitions
    LEFT OUTER JOIN dict_competitors on dict_competitors.wid = unofficial_competitions.competitor 
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
                    . " unofficial_events_rounds.cumulative, "
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
                    . " unofficial_events.id,"
                    . " COALESCE(unofficial_events.result_dict, unofficial_events_dict.result_dict) result_dict, "
                    . " COALESCE(unofficial_events.name,unofficial_events_dict.name$RU) name "
                    . " FROM unofficial_events"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_events.competition = $id"
                    . " ORDER BY unofficial_events_dict.`order` ");
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
                    . " unofficial_events.format_dict,"
                    . " unofficial_events_rounds.comment,"
                    . " unofficial_events_rounds.cutoff,"
                    . " unofficial_events_rounds.time_limit,"
                    . " unofficial_events_rounds.cumulative,"
                    . " unofficial_events_rounds.next_round_procent,"
                    . " unofficial_events_rounds.next_round_value"
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
                    . " case when 'RU'='$RU' then unofficial_competitors.name else coalesce(unofficial_fc_wca.wca_name,unofficial_competitors.name) end name,"
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
        foreach (\db::rows("select FCID "
                . "from `unofficial_competitors` "
                . "where upper(trim(name)) = upper(trim('$competitor->name')) "
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
                    . " LEFT OUTER JOIN dict_competitors on unofficial_organizers.wcaid = dict_competitors.wcaid "
                    . " WHERE competition=$id");
    $data->organizers = $organizers;

    $judges = \db::rows("SELECT 
                            cj.judge wcaid,
                            coalesce(dc.name$RU, dc.name) name,
                            coalesce(jrd.role$RU, jrd.role) role
                        FROM unofficial_competition_judges cj
                        JOIN unofficial_judge_roles_dict jrd on jrd.id=cj.dict_judge_role
                        JOIN dict_competitors dc on dc.wcaid=cj.judge 
                        WHERE cj.competition_id=$id");

    $data->judges = $judges;

    return $data;
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
                    . " COALESCE(unofficial_events_rounds.cumulative,0) cumulative, "
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
                    . " unofficial_results_dict.code result_code  "
                    . " FROM unofficial_events_rounds "
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict"
                    . " JOIN unofficial_results_dict on (unofficial_results_dict.id = unofficial_events.result_dict OR unofficial_results_dict.id = unofficial_events_dict.result_dict)"
                    . " JOIN unofficial_formats_dict on unofficial_formats_dict.id = unofficial_events.format_dict"
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
    if (in_array(strtolower($attempt), ['dnf', 'dns', '-cutoff', '0', false])) {
        return 999999;
    } else {
        $value = substr("00000" . str_replace(['.', ':'], '', $attempt), -6, 6);
        $minute = substr($value, 0, 2);
        $second = substr($value, 2, 2);
        $milisecond = substr($value, 4, 2);
        return $minute * 100 * 60 + $second * 100 + $milisecond;
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

function getResutsByCompetitor($competitor_id) {
    $RU = t('', 'RU');
    return \db::rows("SELECT"
                    . " unofficial_events_dict.id event_dict,"
                    . " COALESCE(unofficial_events.name,unofficial_events_dict.name$RU) event_name,"
                    . " unofficial_events_dict.image event_image,"
                    . " unofficial_events_rounds.round,"
                    . " unofficial_competitors_result.place, "
                    . " unofficial_competitions.id competition_id, "
                    . " unofficial_competitions.name competition_name, "
                    . " coalesce(unofficial_competitions.rankedID, unofficial_competitions.secret) secret,"
                    . " CASE WHEN unofficial_events_rounds.round = unofficial_events.rounds THEN 1 ELSE 0 END final,"
                    . " CASE WHEN unofficial_events.rounds = unofficial_events_rounds.round AND unofficial_competitors_result.place<=3 and upper(unofficial_competitors_result.best)!='DNF' THEN 1 ELSE 0 END podium,"
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
                    . " JOIN unofficial_events_dict on unofficial_events_dict.id = unofficial_events.event_dict "
                    . " JOIN unofficial_rounds_dict on unofficial_rounds_dict.id = unofficial_events_rounds.round "
                    . " WHERE unofficial_competitors.id = $competitor_id"
                    . " ORDER BY "
                    . " unofficial_events_dict.name,"
                    . " unofficial_events_rounds.round DESC");
}

function getFavicon($website, $hide_block = true) {
    if ($website) {
        preg_match('/.*(instagram|facebook).*/', $website, $ban);
        preg_match('/https?:\/\/(?:www\.|)([\w.-]+).*/', $website, $matches);

        if (isset($ban[1])) {
            return;
        }
        ?> 
        <a target="_blank" href="<?= $website ?>">
            <?= $matches[1] ?? $website ?>
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
            if ($attempt->average and!in_array(strtolower($attempt->average), ['dnf', '-cutoff'])) {
                $results[$attempt->code][$attempt->round]['average'] = $attempt->average;
                $average_order[$attempt->code][$attempt->round] = $attempt->average_order;
            }
        }
    }

    return $results;
    
   
}

function getText($code){
    $text= \db::row("select text from unofficial_text where code='$code' and is_archive!=1")->text??false;
    if(!$text){
        $L = t('EN', 'RU');
        $text= \db::row("select text from unofficial_text where code='$code$L' and is_archive!=1")->text??false;    
    }
    return $text;
}