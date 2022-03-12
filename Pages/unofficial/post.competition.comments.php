<?php

$comments = filter_input(INPUT_POST, 'comments', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$cutoffs = filter_input(INPUT_POST, 'cutoff', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$time_limits = filter_input(INPUT_POST, 'time_limit', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$cumulatives = filter_input(INPUT_POST, 'cumulative', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

foreach ($comments ?? [] as $event_dict => $comments_round) {

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

foreach ($cutoffs ?? [] as $event_dict => $cutoffs_round) {
    if (!($events_dict[$event_dict] ?? FALSE)) {
        continue;
    }
    $eventId = db::row("SELECT id FROM unofficial_events WHERE competition = $comp->id AND event_dict = $event_dict ")->id ?? FALSE;
    if (!$eventId) {
        continue;
    }
    foreach ($cutoffs_round as $round => $cutoff) {
        db::exec("UPDATE unofficial_events_rounds SET cutoff = '$cutoff' WHERE event = $eventId AND round = $round");
    }
}

foreach ($time_limits ?? [] as $event_dict => $time_limits_round) {
    if (!($events_dict[$event_dict] ?? FALSE)) {
        continue;
    }
    $eventId = db::row("SELECT id FROM unofficial_events WHERE competition = $comp->id AND event_dict = $event_dict ")->id ?? FALSE;
    if (!$eventId) {
        continue;
    }
    foreach ($time_limits_round as $round => $time_limit) {
        if (isset($cumulatives[$event_dict][$round]) and $time_limit) {
            $cumulative_bit = 1;
        } else {
            $cumulative_bit = 0;
        }


        db::exec("UPDATE unofficial_events_rounds SET cumulative = $cumulative_bit, time_limit = '$time_limit' WHERE event = $eventId AND round = $round");
    }
}