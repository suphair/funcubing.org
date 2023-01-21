<?php

require_once 'function.mbf.php';

$competitor_round = db::escape(filter_input(INPUT_POST, 'competitor_round', FILTER_VALIDATE_INT));

$number = $_POST['number'];
$solved = $_POST['solved'];
$time = $_POST['time'];
$is_dnf = $_POST['is_dnf'] ?? false;

$code = db::escape(request(3));
$round = db::escape(request(4));

if ($code and is_numeric($round)) {
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
        $number_empty = true;
        for ($n = 1; $n <= sizeof($number); $n++) {
            if ($is_dnf[$n] ?? false) {
                $number[$n] = 1;
                $solved[$n] = 0;
                $time[$n] = false;
            }
            if ($number[$n] ?? 0 > 0) {
                $number_empty = false;
            }
        }
        if ($number_empty == true) {
            db::exec("DELETE FROM unofficial_competitors_result WHERE competitor_round = $competitor_round");
        } else {
            $attempts = [];
            db::exec("INSERT IGNORE INTO unofficial_competitors_result (competitor_round) VALUES ($competitor_round)");
            for ($n = 1; $n <= sizeof($number); $n++) {
                if (is_numeric($number[$n]) and is_numeric($solved[$n]) and $number[$n] > 0) {
                    $time_int = false;
                    if (strpos($time[$n], ':') === false) {
                        $t = $time[$n];
                        $t = preg_replace("/[^,.0-9]/", '', $t);
                        $t = substr("000000$t", strlen($t), 6);
                        $t = substr($t, 0, 2) . ':' . substr($t, 2, 2) . ':' . substr($t, 4, 2);
                        $time[$n] = $t;
                    }
                    if ($time[$n]) {
                        $time_int = MBF\time_to_int($time[$n]);
                    }
                    if (!$time_int) {
                        $time_int = min(60 * 60, $number[$n] * 10 * 60);
                    }
                    $wcaResult[$n] = MBF\wcaResult($number[$n], $solved[$n], $time_int);
                    $printResult = MBF\printResult($number[$n], $solved[$n], $time_int);
                    $attempts[] = $number[$n] . ' ' . $solved[$n] . ' ' . $time_int;
                    db::exec("UPDATE unofficial_competitors_result "
                            . "SET attempt$n = '$printResult'"
                            . "WHERE competitor_round = $competitor_round");
                }
            }
            $wcan = 1;
            $wcar = $wcaResult[1];
            for ($n = 1; $n <= sizeof($number); $n++) {
                if ($wcaResult[$n] ?? false and $wcar > $wcaResult[$n]) {
                    $wcan = $n;
                    $wcar = $wcaResult[$n];
                }
            }
            db::exec("UPDATE unofficial_competitors_result "
                    . "SET attempts = '" . implode(";", $attempts) . "',"
                    . " best = attempt$wcan, `order`='$wcar' "
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