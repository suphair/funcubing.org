<?php

$name = db::escape(filter_input(INPUT_POST, 'name'));
$competitor_id = db::escape(filter_input(INPUT_POST, 'competitor', FILTER_VALIDATE_INT));
$events = filter_input(INPUT_POST, 'events', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!$events) {
    $events = [];
}
$session = db::escape(session_id());
$error = false;
if (!$error and!$name) {
    $error = true;
    postSet('error', "Competitor name is mandatory");
}

if (!$error and!sizeof($events)) {
    $error = true;
    postSet('error', "No event has been chosen");
}

if (!$error and$competitor_id and!db::row("SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND id = $competitor_id and session = '$session'")) {
    $error = true;
    postSet('error', "Registration [$competitor_id] not found in your session");
}

if (!$error and db::row("SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND name = '$name' AND id != $competitor_id")) {
    $error = true;
    postSet('error', "Competitor [$name] already exists");
}

if (!$error and $competitor_id) {
    db::exec("UPDATE unofficial_competitors set name = '$name' WHERE ID = $competitor_id");
}

if (!$error and!$competitor_id) {
    db::exec("INSERT INTO unofficial_competitors (competition, name, session) VALUES ($comp->id,'$name','$session')");
    $competitor_id = db::id();
}

if (!$error) {
    unofficial\updateCompetitionCard($comp->id);
    foreach ($events as $event_id => $flag) {
        $round = db::row("SELECT unofficial_events_rounds.id FROM unofficial_events_rounds "
                        . " JOIN unofficial_events on unofficial_events_rounds.event = unofficial_events.id"
                        . " WHERE unofficial_events_rounds.event = $event_id "
                        . " AND unofficial_events.competition = $comp->id "
                        . " AND unofficial_events_rounds.round = 1")->id ?? FALSE;

        if ($round and $flag == 'on') {
            db::exec("INSERT IGNORE INTO unofficial_competitors_round "
                    . " (competitor, round) VALUES ($competitor_id, $round)");
        }
        if ($round and $flag == 'off') {
            db::exec("DELETE IGNORE FROM unofficial_competitors_round "
                    . " WHERE competitor = $competitor_id AND round = $round ");
        }
    }
}
?>
