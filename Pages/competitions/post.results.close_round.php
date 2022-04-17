<?php

$secret = db::escape(request(1));
$code = db::escape(request(3));
$round = db::escape(request(4));

$event_round_this = db::row("        
    select er.id from `unofficial_events_rounds` er
        join `unofficial_events` e on e.id=er.event
        join `unofficial_events_dict` ed on ed.id=e.event_dict
        join `unofficial_competitions` c on c.id=e.competition
    where ed.code='$code' 
        and lower('$secret') = lower(coalesce(c.rankedID,c.secret)) 
        and er.round = $round"
        )->id ?? null;

if ($event_round_this) {

    $event_round_next = db::row("        
    select er.id from `unofficial_events_rounds` er
        join `unofficial_events` e on e.id=er.event
        join `unofficial_events_dict` ed on ed.id=e.event_dict
        join `unofficial_competitions` c on c.id=e.competition
    where ed.code='$code' 
        and lower('$secret') = lower(coalesce(c.rankedID,c.secret)) 
        and er.round = $round + 1"
            )->id ?? null;

    $event = unofficial\getEventByEventround($event_round_this);
    $competitors = unofficial\getCompetitorsByEventround($event_round_this, $event);

    $l_next_round_register = [];
    $l_next_round = [];
    $l_wo_result = [];
    foreach ($competitors as $competitor) {
        if ($competitor->next_round_register) {
            $l_next_round_register[] = $competitor;
        }
        if (!$competitor->place) {
            $l_wo_result[] = $competitor;
        }
        if ($competitor->next_round and!$event->final) {
            $l_next_round[] = $competitor;
        }
    }

    if (!sizeof($l_next_round_register) and sizeof($l_next_round) and $event_round_next) {
        foreach ($l_next_round as $next_round) {
            db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor,round) "
                    . " values($next_round->id, $event_round_next)");
        }
    }

    foreach ($l_wo_result as $wo_result) {
        $competitor_round = $wo_result->competitor_round;

        db::exec("DELETE IGNORE unofficial_competitors_round "
                . " FROM unofficial_competitors_round"
                . " LEFT JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id "
                . " WHERE unofficial_competitors_round.id = $competitor_round "
                . "     AND unofficial_competitors_result.competitor_round is null");
    }
}
?>