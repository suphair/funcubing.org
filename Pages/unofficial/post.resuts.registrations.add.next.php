<?php

$competitors = filter_input(INPUT_POST, 'competitors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$code = db::escape(request(3));
$round = db::escape(request(4));
$event_dict = $comp_data->event_dict->by_code[$code]->id ?? FALSE;
$competitors_prev = unofficial\getCompetitorsByEventdictRound($event_dict, $round - 1);

if ($code and is_numeric($round)) {
    foreach ($competitors as $competitor => $flag) {
        if (isset($competitors_prev[$competitor]) and $flag == 'on') {
            db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor,round) "
                    . " SELECT $competitor, unofficial_events_rounds.id  "
                    . " FROM unofficial_events_rounds"
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict ON unofficial_events.event_dict = unofficial_events_dict.id"
                    . " WHERE unofficial_events.competition = $comp->id "
                    . " AND unofficial_events_rounds.round = $round "
                    . " AND unofficial_events_dict.code = '$code' ");
        }
        if ($flag == 'off') {
            db::exec("DELETE IGNORE unofficial_competitors_round "
                    . " FROM unofficial_events_rounds "
                    . " JOIN unofficial_competitors_round on unofficial_competitors_round.round = unofficial_events_rounds.id"
                    . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict ON unofficial_events.event_dict = unofficial_events_dict.id"
                    . " WHERE unofficial_events.competition = $comp->id "
                    . " AND unofficial_events_rounds.round = $round "
                    . " AND unofficial_events_dict.code = '$code' "
                    . " AND unofficial_competitors_round.competitor = $competitor ");
        }
    }
}