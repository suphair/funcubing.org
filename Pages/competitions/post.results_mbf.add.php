<?php

require_once 'function.mbf.php';

$competitor_round = db::escape(filter_input(INPUT_POST, 'competitor_round', FILTER_VALIDATE_INT));

$number = db::escape(filter_input(INPUT_POST, 'number', FILTER_VALIDATE_INT));
$solved = db::escape(filter_input(INPUT_POST, 'solved', FILTER_VALIDATE_INT));
$time = db::escape(filter_input(INPUT_POST, 'time'));

$code = db::escape(request(3));
$round = db::escape(request(4));

if ($code and is_numeric($round) and $solved!='') {
    $competitors_round = db::row("SELECT coalesce(results_dict_2.code,results_dict_1.code) code FROM unofficial_competitors_round"
                    . " JOIN unofficial_events_rounds on unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " JOIN unofficial_results_dict results_dict_1  on results_dict_1.id = unofficial_events_dict.result_dict"
                    . " LEFT OUTER JOIN unofficial_results_dict results_dict_2  on results_dict_2.id = unofficial_events.result_dict"
                    . " WHERE unofficial_competitors_round.id = $competitor_round "
                    . " AND unofficial_events_rounds.round = $round"
                    . " AND unofficial_events_dict.code = '$code'");
    if ($competitors_round) {
        if ($number == 0) {
            db::exec("DELETE FROM unofficial_competitors_result WHERE competitor_round = $competitor_round");
        } else {
            db::exec("INSERT IGNORE INTO unofficial_competitors_result (competitor_round) VALUES ($competitor_round)");
            $time_int = false;
            if ($time) {
                $time_int = MBF\time_to_int($time);
            }
            if (!$time_int) {
                $time_int = min(60 * 60, $number * 10 * 60);
            }
            $wcaResult = MBF\wcaResult($number, $solved, $time_int);
            $printResult = MBF\printResult($number, $solved, $time_int);
            db::exec("UPDATE unofficial_competitors_result "
                    . "SET attempt1 = '$printResult',  attempts='$number $solved $time_int', best ='$printResult', `order`='$wcaResult' "
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
            $place_query = $result->best == 'DNF' ? max($place_current, 4) : $place_current;
            db::exec("UPDATE unofficial_competitors_result "
                    . "SET `place` = '$place_query'  "
                    . "WHERE competitor_round = $result->competitor_round");
        }
    }
}