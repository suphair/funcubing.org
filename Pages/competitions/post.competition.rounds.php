<?php

$events = filter_input(INPUT_POST, 'events', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
unset($rounds_dict[0]);
foreach ($events as $event_dict => $event) {
    if (!($events_dict[$event_dict] ?? FALSE)) {
        continue;
    }
    $formatId = $event['format'] ?? -1;
    if (!isset($formats_dict[$formatId])) {
        $formatId = array_keys($formats_dict)[0];
    }

    $name = db::escape($event['name'] ?? $events_dict[$event_dict]->name);
    $rounds = $rounds_dict[$event['rounds'] ?? -1]->id ?? 0;
    $result = $results_dict[$event['result'] ?? -1]->id ?? 0;

    if ($rounds) {
        db::exec("INSERT IGNORE INTO unofficial_events (competition, event_dict) VALUES ($comp->id, $event_dict)");
    }

    $eventId = db::row("SELECT id FROM unofficial_events WHERE competition = $comp->id AND event_dict = $event_dict ")->id ?? FALSE;
    
    if ($eventId) {
        if ($events_dict[$event_dict]->special) {
            db::exec("UPDATE unofficial_events SET result_dict = $result, name = '$name' WHERE id = $eventId");
        }
        foreach ($rounds_dict as $round_dict) {
            if ($round_dict->id <= $rounds) {
                db::exec("INSERT IGNORE INTO unofficial_events_rounds (event, round) VALUES ($eventId, $round_dict->id)");
            } else {
                db::exec("DELETE IGNORE FROM unofficial_events_rounds WHERE event = $eventId AND round = $round_dict->id");
            }
        }

        $rounds=db::row("SELECT max(round) rounds FROM unofficial_events_rounds WHERE event = $eventId")->rounds ?? 0;        
        
        if (!$rounds) {
            db::exec("DELETE IGNORE FROM unofficial_events WHERE id = $eventId");
        }else{
            db::exec("UPDATE unofficial_events SET format_dict = $formatId, rounds = $rounds WHERE id = $eventId");
        }
    }
}
