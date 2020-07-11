<?php

$code = db::escape(request(3));
$round = db::escape(request(4));

if ($code and is_numeric($round)) {
    db::exec("DELETE IGNORE unofficial_competitors_round "
            . " FROM unofficial_competitors_round"
            . " JOIN unofficial_events_rounds ON unofficial_events_rounds.id = unofficial_competitors_round.round "
            . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
            . " JOIN unofficial_events_dict ON unofficial_events.event_dict = unofficial_events_dict.id"
            . " WHERE unofficial_events.competition = $comp->id "
            . " AND unofficial_events_rounds.round = $round "
            . " AND unofficial_events_dict.code = '$code' ");
}
