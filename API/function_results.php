<?php

namespace api;

function results($competition_id, $event_id = false) {
    $RU = t('', 'RU');
    $results = \db::rows("
           select 
                round.id,
                competitor.id registration_id,
                case when '$RU'='RU' then competitor.name else coalesce(wca.wca_name,competitor.name) end name,
                competitor.FCID,
                result.place pos,
                event_dict.code event,
                rounds.round,
                result.attempt1,
                result.attempt2,
                result.attempt3,
                result.attempt4,
                result.attempt5,
                result.mean,
                result.average,
                result.best,
                wca.wcaid,
                wca.nonwca
        from `unofficial_competitors_result` result
        join `unofficial_competitors_round` round on result.competitor_round=round.id
        join `unofficial_competitors` competitor on competitor.id = round.competitor
        join `unofficial_events_rounds` rounds on rounds.id = round.round
        join `unofficial_events` event on event.id = rounds.event
        join `unofficial_events_dict` event_dict on event_dict.id=event.event_dict 
        join `unofficial_competitions` competition on competition.id=event.competition
        LEFT OUTER JOIN unofficial_fc_wca wca on wca.FCID = competitor.FCID
        where lower('$competition_id') in (lower(competition.secret), lower(competition.rankedID), '')
                and '$event_id' in (CONCAT(event_dict.code,'_',rounds.round),'')
        order by event_dict.order, rounds.round, result.place
            ");
    $results_key = [];
    foreach ($results as $result) {
        $result_key = new \stdClass();
        $result_key->id = $result->id + 0;
        $result_key->registration_id = $result->registration_id;
        $result_key->competition_id = $competition_id;
        $result_key->event_id = "{$result->event}_{$result->round}";
        $result_key->round = $result->round + 0;
        $result_key->pos = $result->pos + 0;
        $result_key->name = trim($result->name);
        $result_key->fc_id = $result->FCID;
        if ($result->nonwca) {
            $result_key->wca_id = false;
        } elseif (!$result->wcaid) {
            $result_key->wca_id = null;
        } else {
            $result_key->wca_id = $result->wcaid;
        }
        $result_key->attempts = [
            attempt_centiseconds($result->attempt1),
            attempt_centiseconds($result->attempt2),
            attempt_centiseconds($result->attempt3),
            attempt_centiseconds($result->attempt4),
            attempt_centiseconds($result->attempt5),
        ];

        $result_key->best = attempt_centiseconds($result->best);
        $result_key->average = 0;
        if ($result->average) {
            $result_key->average = attempt_centiseconds($result->average);
        }
        if ($result->mean) {
            $result_key->average = attempt_centiseconds($result->mean);
        }
        $results_key[] = $result_key;
    }

    if ($event_id and sizeof($results_key)) {
        $results_next_round = results($competition_id, explode("_", $event_id)[0] . '_' . (explode("_", $event_id)[1] + 1));
        foreach ($results_key as $r => $result_key) {
            foreach ($results_next_round as $result_next_round) {
                if ($result_key->registration_id == $result_next_round->registration_id) {
                    $results_key[$r]->next_round = true;
                }
            }
        }
    }

    return $results_key;
}
