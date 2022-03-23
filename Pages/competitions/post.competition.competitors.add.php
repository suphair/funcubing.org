<?php

$competitors = explode(",", str_replace("\n", ",", filter_input(INPUT_POST, 'competitors')));

foreach ($competitors as $competitor) {
    $names = [];
    $events = [];
    $competitor = str_replace(chr(13), "", $competitor);
    $words = explode(" ", $competitor);
    foreach ($words as $n => $word) {
        if (!$word) {
            continue;
        }
        $event = $comp_data->event_dict->by_code[$word]->id ?? FALSE;
        if ($event) {
            $events[] = $event;
        } else {
            $names[] = $word;
        }
    }

    $events = array_unique($events);
    $name = strip_tags(db::escape(implode(' ', $names)));


    if ($name) {
        db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name) VALUES ($comp->id,'$name')");
        unofficial\updateCompetitionCard($comp->id);
    }

    $competitor_id = db::row("SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND name = '$name'")->id ?? FALSE;
    foreach ($events as $event_dict) {
        $round = db::row("SELECT unofficial_events_rounds.id "
                        . " FROM unofficial_events  "
                        . " JOIN unofficial_events_rounds on unofficial_events_rounds.event = unofficial_events.id "
                        . " WHERE unofficial_events_rounds.round = 1 "
                        . " AND unofficial_events.event_dict = $event_dict"
                        . " AND unofficial_events.competition = $comp->id");
        if ($round->id ?? FALSE and $competitor_id ?? FALSE) {
            db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor, round) VALUES ($competitor_id,$round->id)");
        }
    }
}