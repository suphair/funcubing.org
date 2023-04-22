<?php

$competitor_round = db::escape(filter_input(INPUT_POST, 'competitor_round', FILTER_VALIDATE_INT));

$attempts = db::escape(filter_input(INPUT_POST, 'attempts'));
$exclude = db::escape(filter_input(INPUT_POST, 'exclude'));
if (!$exclude or!is_numeric($exclude)) {
    $exclude = 0;
}
$code = db::escape(request(3));
$round = db::escape(request(4));
$attempt_arr = filter_input(INPUT_POST, 'attempt', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if ($code and is_numeric($round)) {
    $competitors_round = db::row("SELECT"
                    . " attempts, format,"
                    . " coalesce(results_dict_2.code,results_dict_1.code) code,"
                    . " unofficial_events.id unofficial_events_id"
                    . " FROM unofficial_competitors_round"
                    . " JOIN unofficial_events_rounds on unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_formats_dict on unofficial_events.format_dict = unofficial_formats_dict.id"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " JOIN unofficial_results_dict results_dict_1  on results_dict_1.id = unofficial_events_dict.result_dict"
                    . " LEFT OUTER JOIN unofficial_results_dict results_dict_2  on results_dict_2.id = unofficial_events.result_dict"
                    . " WHERE unofficial_competitors_round.id = $competitor_round "
                    . " AND unofficial_events_rounds.round = $round"
                    . " AND unofficial_events_dict.code = '$code'");
    if ($competitors_round) {

        if ($attempts == '') {
            db::exec("DELETE FROM unofficial_competitors_result WHERE competitor_round = $competitor_round");
        } else {
            db::exec("INSERT IGNORE INTO unofficial_competitors_result (competitor_round) VALUES ($competitor_round)");
            foreach ($attempt_arr as $a => $attempt) {
                if (strlen($attempt) == 8) {
                    $attempt = substr($attempt, 0, 5);
                }
                if (substr($attempt, 0, 1) == '0') {
                    $attempt = str_replace(['0:0', '0:'], '', $attempt);
                }

                if (is_numeric($a) and in_array($a, range(1, 5))) {
                    if (preg_match("/$a/", $exclude)) {
                        $attempt = "($attempt)";
                    }

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
            if ($competitors_round->code == 'amount_desc') {
                $order += 10000000 * 999999;
                $order += (10000000 * (999999 - unofficial\attempt_to_int($attempt_arr['average'] ?? 0)));
                $order += (10000000 * (999999 - unofficial\attempt_to_int($attempt_arr['mean'] ?? 0)));
                $order += 9999 - unofficial\attempt_to_int($attempt_arr['best'] ?? 0);
            } elseif ($competitors_round->code == 'amount_asc') {
                $order += (10000000 * unofficial\attempt_to_int_fm($attempt_arr['average'] ?? 0));
                $order += (10000000 * unofficial\attempt_to_int_fm($attempt_arr['mean'] ?? 0));
                $order += unofficial\attempt_to_int_fm($attempt_arr['best'] ?? 0);
            } elseif ($competitors_round->attempts == 3 or $competitors_round->format == 'best') {
                $order = 10000000 * unofficial\attempt_to_int($attempt_arr['best'] ?? 0);
                $order += unofficial\attempt_to_int($attempt_arr['mean'] ?? 0);
            } else {
                $order += (10000000 * unofficial\attempt_to_int($attempt_arr['average'] ?? 0));
                $order += (10000000 * unofficial\attempt_to_int($attempt_arr['mean'] ?? 0));
                $order += unofficial\attempt_to_int($attempt_arr['best'] ?? 0);
            }
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
        $place_cash = 0;

        foreach ($results as $result) {
            $place_cash++;
            if ($result->order == $order_current and $code == 'teambld') {
                $place_cash--;
            }
            if ($result->order > $order_current) {
                $order_current = $result->order;
                $place_current = $place_cash;
            }
            $place_query = $result->best == 'dnf' ? max($place_current, 4) : $place_current;
            db::exec("UPDATE unofficial_competitors_result "
                    . "SET `place` = '$place_query'  "
                    . "WHERE competitor_round = $result->competitor_round");
        }
        db::exec("UPDATE unofficial_events "
                . "SET `update_at` = current_timestamp  "
                . "WHERE id = $competitors_round->unofficial_events_id");
    }
}