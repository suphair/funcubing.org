<?php

$competitor_round = db::escape(filter_input(INPUT_POST, 'competitor_round', FILTER_VALIDATE_INT));

$attempts = db::escape(filter_input(INPUT_POST, 'attempts'));
$code = db::escape(request(3));
$round = db::escape(request(4));
$attempt_arr = filter_input(INPUT_POST, 'attempt', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($code and is_numeric($round)) {
    if (db::row("SELECT 1 FROM unofficial_competitors_round"
                    . " JOIN unofficial_events_rounds on unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " WHERE unofficial_competitors_round.id = $competitor_round "
                    . " AND unofficial_events_rounds.round = $round"
                    . " AND unofficial_events_dict.code = '$code'")) {

        if (!$attempts) {
            db::exec("DELETE FROM unofficial_competitors_result WHERE competitor_round = $competitor_round");
        } else {
            db::exec("INSERT IGNORE INTO unofficial_competitors_result (competitor_round) VALUES ($competitor_round)");
            foreach ($attempt_arr as $a => $attempt) {
                $attempt = str_replace(['0:0', '0:'], '', $attempt);

                if (is_numeric($a) and in_array($a, range(1, 5))) {
                    db::exec("UPDATE unofficial_competitors_result "
                            . "SET attempt$a = '$attempt'"
                            . "WHERE competitor_round = $competitor_round");
                }
                if (!is_numeric($a) and in_array($a, ['best', 'average', 'mean'])) {
                    db::exec("UPDATE unofficial_competitors_result "
                            . "SET $a = '$attempt'"
                            . "WHERE competitor_round = $competitor_round");
                }
            }

            db::exec("UPDATE unofficial_competitors_result "
                    . "SET attempts = '$attempts'"
                    . "WHERE competitor_round = $competitor_round");

            $order = 0;
            $order += (10000000 * unofficial\attempt_to_int($attempt_arr['average'] ?? 0));
            $order += (10000000 * unofficial\attempt_to_int($attempt_arr['mean'] ?? 0));
            $order += unofficial\attempt_to_int($attempt_arr['best'] ?? 0);
            db::exec("UPDATE unofficial_competitors_result "
                    . "SET `order` = '$order'"
                    . "WHERE competitor_round = $competitor_round");
        }


        $results = db::rows("SELECT unofficial_competitors_result.`order`,"
                        . " unofficial_competitors_result.`best`, "
                        . " unofficial_competitors_result.competitor_round "
                        . " FROM unofficial_competitors_round"
                        . " JOIN unofficial_events_rounds on unofficial_events_rounds.id = unofficial_competitors_round.round"
                        . " JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
                        . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
                        . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                        . " WHERE unofficial_events.competition = $comp->id "
                        . " AND unofficial_events_rounds.round = $round"
                        . " AND unofficial_events_dict.code = '$code'"
                        . " ORDER BY `order`");

        $order_current = 0;
        $place_current = 0;

        foreach ($results as $result) {
            if ($result->order > $order_current) {
                $order_current = $result->order;
                $place_current++;
            }
            $place_query = $result->best == 'dnf' ? max($place_current, 4) : $place_current;
            db::exec("UPDATE unofficial_competitors_result "
                    . "SET `place` = '$place_query'  "
                    . "WHERE competitor_round = $result->competitor_round");
        }
    }
}