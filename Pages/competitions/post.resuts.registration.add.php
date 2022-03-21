<?php

$name = strip_tags(db::escape(filter_input(INPUT_POST, 'name', FILTER_DEFAULT)));
$code = db::escape(request(3));
$round = db::escape(request(4));

if ($code and is_numeric($round)) {
    db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name) VALUES ($comp->id, '$name') ");

    $competitor = db::row(" SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND name = '$name'")->id ?? FALSE;
    if ($competitor) {
        db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor,round) "
                . " SELECT $competitor, unofficial_events_rounds.id  "
                . " FROM unofficial_events_rounds"
                . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                . " JOIN unofficial_events_dict ON unofficial_events.event_dict = unofficial_events_dict.id"
                . " WHERE unofficial_events.competition = $comp->id "
                . " AND unofficial_events_rounds.round = $round "
                . " AND unofficial_events_dict.code = '$code' ");
        postSet('query', db::id());
    }
}