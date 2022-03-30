<?php
$competitors = filter_input(INPUT_POST, 'competitors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$code = db::escape(request(3));
$round = db::escape(request(4));
if(is_array($competitors)){

if ($code and is_numeric($round)) {
    foreach (array_keys($competitors) as $competitor) {
        db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor,round) "
                . " SELECT $competitor, unofficial_events_rounds.id  "
                . " FROM unofficial_events_rounds"
                . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                . " JOIN unofficial_events_dict ON unofficial_events.event_dict = unofficial_events_dict.id"
                . " WHERE unofficial_events.competition = $comp->id "
                . " AND unofficial_events_rounds.round = $round "
                . " AND unofficial_events_dict.code = '$code' ");
    }
}
}