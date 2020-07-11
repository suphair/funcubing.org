<?php

$comments = filter_input(INPUT_POST, 'comments', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

foreach ($comments as $event_dict => $comments_round) {

    if (!($events_dict[$event_dict] ?? FALSE)) {
        continue;
    }

    $eventId = db::row("SELECT id FROM unofficial_events WHERE competition = $comp->id AND event_dict = $event_dict ")->id ?? FALSE;
    if (!$eventId) {
        continue;
    }

    foreach ($comments_round as $round => $comment) {
        db::exec("UPDATE unofficial_events_rounds SET comment = '$comment' WHERE event = $eventId AND round = $round");
    }
}
