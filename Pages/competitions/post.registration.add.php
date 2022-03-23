<?php

$name = db::escape(filter_input(INPUT_POST, 'name'));
$events = filter_input(INPUT_POST, 'events', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!$events) {
    $events = [];
}
$session = db::escape(session_id());
if ($name) {
    db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name, session) VALUES ($comp->id,'$name','$session')");
    $competitor_id = db::row("SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND name = '$name' and session = '$session'")->id ?? FALSE;

    if ($competitor_id) {
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
    } else {
        postSet('error', "Competitor [$name] already exists");
    }
}
?>
