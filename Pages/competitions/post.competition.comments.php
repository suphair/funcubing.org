<?php

$comments = filter_input(INPUT_POST, 'comments', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$cutoffs = filter_input(INPUT_POST, 'cutoff', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$time_limits = filter_input(INPUT_POST, 'time_limit', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$time_limits_cumulative = filter_input(INPUT_POST, 'time_limit_cumulative', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$next_round_values = filter_input(INPUT_POST, 'next_round_value', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$next_round_procents = filter_input(INPUT_POST, 'next_round_procent', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

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
        db::exec("UPDATE unofficial_events_rounds SET time_limit = '$time_limit' WHERE event = $eventId AND round = $round");
    }
}

foreach ($time_limits_cumulative ?? [] as $event_dict => $time_limits_cumulative_round) {
    if (!($events_dict[$event_dict] ?? FALSE)) {
        continue;
    }
    $eventId = db::row("SELECT id FROM unofficial_events WHERE competition = $comp->id AND event_dict = $event_dict ")->id ?? FALSE;
    if (!$eventId) {
        continue;
    }
    foreach ($time_limits_cumulative_round as $round => $time_limit_cumulative) {
        db::exec("UPDATE unofficial_events_rounds SET time_limit_cumulative = '$time_limit_cumulative' WHERE event = $eventId AND round = $round");
    }
}

foreach ($next_round_values ?? [] as $event_dict => $next_value_round) {
    if (!($events_dict[$event_dict] ?? FALSE)) {
        continue;
    }
    $eventId = db::row("SELECT id FROM unofficial_events WHERE competition = $comp->id AND event_dict = $event_dict ")->id ?? FALSE;
    if (!$eventId) {
        continue;
    }
    foreach ($next_value_round as $round => $next_value) {
        if (isset($next_round_procents[$event_dict][$round]) and $next_value) {
            $next_procent_bit = 1;
        } else {
            $next_procent_bit = 0;
        }

        db::exec("UPDATE unofficial_events_rounds SET next_round_value = '$next_value', next_round_procent = $next_procent_bit WHERE event = $eventId AND round = $round");
    }
}