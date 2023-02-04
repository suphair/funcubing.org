<?php

function wrong_attempts() {
    \db::exec("delete from wrong_attempts");
    $competitions = \api\get_competitions();
    foreach ($competitions as $competition) {
        $return = [];
        $events = \api\get_events($competition->id);
        $results = \api\get_results($competition->id);
        $event_limits = [];
        $result_limits = [];
        foreach ($events as $event) {
            if (!$event->is_special) {
                $event_limits[$event->id] = (object) [
                            'cutoff' => $event->cutoff ? \api\attempt_centiseconds($event->cutoff) : null,
                            'time_limit' => $event->time_limit ? \api\attempt_centiseconds($event->time_limit) : null,
                            'time_limit_cumulative' => $event->time_limit_cumulative ? \api\attempt_centiseconds($event->time_limit_cumulative) : null,
                            'cutoff_attempts' => $event->cutoff_attempts ?? 0,
                            'attempts' => $event->attempts,
                ];
            }
        }
        foreach ($results as $result) {
            $result_limits[$result->event_id][$result->id] = (object) [
                        'name' => $result->name,
                        'fc_id' => $result->fc_id,
                        'attempts' => $result->attempts
            ];
        }

        $wrong_attempts = [];
        foreach ($result_limits as $event => $result_limit) {
            foreach ($result_limit as $id => $competitor) {
                $time_limit = $event_limits[$event]->time_limit ?? null;
                if ($time_limit) {
                    $wrong_limit = (object) [
                                'competition' => $competition->id,
                                'event' => explode('_', $event)[0],
                                'round' => explode('_', $event)[1],
                                'attempt1' => $competitor->attempts[0] ?? 0,
                                'attempt2' => $competitor->attempts[1] ?? 0,
                                'attempt3' => $competitor->attempts[2] ?? 0,
                                'attempt4' => $competitor->attempts[3] ?? 0,
                                'attempt5' => $competitor->attempts[4] ?? 0,
                                'wrong_attempts' => [],
                                'value' => $time_limit,
                                'type' => 'time_limit',
                                'name' => $competitor->name,
                                'fc_id' => $competitor->fc_id,
                                'attempts_sum' => 0,
                                'is_ranked' => $competition->is_ranked + 0,
                                'cutoff_attempts' => 0
                    ];
                    foreach ($competitor->attempts as $a => $attempt) {
                        if ($attempt >= $time_limit) {
                            $wrong_limit->wrong_attempts[$a] = 1;
                        }
                    }
                    if (sizeof($wrong_limit->wrong_attempts)) {
                        $wrong_attempts[] = $wrong_limit;
                    }
                }

                $time_limit_cumulative = $event_limits[$event]->time_limit_cumulative ?? null;
                if ($time_limit_cumulative) {
                    $wrong_limit_cumulative = (object) [
                                'competition' => $competition->id,
                                'event' => explode('_', $event)[0],
                                'round' => explode('_', $event)[1],
                                'attempt1' => $competitor->attempts[0] ?? 0,
                                'attempt2' => $competitor->attempts[1] ?? 0,
                                'attempt3' => $competitor->attempts[2] ?? 0,
                                'attempt4' => $competitor->attempts[3] ?? 0,
                                'attempt5' => $competitor->attempts[4] ?? 0,
                                'wrong_attempts' => [],
                                'value' => $time_limit_cumulative,
                                'type' => 'time_limit_cumulative',
                                'name' => $competitor->name,
                                'fc_id' => $competitor->fc_id,
                                'attempts_sum' => 0,
                                'is_ranked' => $competition->is_ranked + 0,
                                'cutoff_attempts' => 0
                    ];
                    $attempts_sum = 0;
                    foreach ($competitor->attempts as $a => $attempt) {
                        if ($attempt > 0) {
                            $attempts_sum += $attempt;
                            if ($attempts_sum >= $time_limit_cumulative) {
                                $wrong_limit_cumulative->wrong_attempts[$a] = 1;
                            }
                        }
                    }
                    if ($attempts_sum >= $time_limit_cumulative) {
                        $wrong_limit_cumulative->attempts_sum = $attempts_sum;
                    }
                    if ($wrong_limit_cumulative->attempts_sum > 0) {
                        $wrong_attempts[] = $wrong_limit_cumulative;
                    }
                }

                $cutoff = $event_limits[$event]->cutoff ?? null;
                $cutoff_attempts = $event_limits[$event]->cutoff_attempts ?? 0;
                if ($cutoff) {
                    $wrong_cutoff = (object) [
                                'competition' => $competition->id,
                                'event' => explode('_', $event)[0],
                                'round' => explode('_', $event)[1],
                                'attempt1' => $competitor->attempts[0] ?? 0,
                                'attempt2' => $competitor->attempts[1] ?? 0,
                                'attempt3' => $competitor->attempts[2] ?? 0,
                                'attempt4' => $competitor->attempts[3] ?? 0,
                                'attempt5' => $competitor->attempts[4] ?? 0,
                                'wrong_attempts' => [],
                                'value' => $cutoff,
                                'type' => 'cutoff',
                                'name' => $competitor->name,
                                'fc_id' => $competitor->fc_id,
                                'attempts_sum' => 0,
                                'is_ranked' => $competition->is_ranked + 0,
                                'cutoff_attempts' => $cutoff_attempts
                    ];
                    $break_cutoff = false;
                    for ($a = 0; $a < $cutoff_attempts; $a++) {
                        if ($competitor->attempts[$a] > 0 and $competitor->attempts[$a] < $cutoff) {
                            $break_cutoff = true;
                        }
                    }
                    if (!$break_cutoff) {
                        for ($a = $cutoff_attempts; $a < $event_limits[$event]->attempts; $a++) {
                            if ($competitor->attempts[$a] != 0) {
                                $wrong_cutoff->wrong_attempts[$a] = 1;
                            }
                        }
                        if (sizeof($wrong_cutoff->wrong_attempts)) {
                            $wrong_attempts[] = $wrong_cutoff;
                        }
                    }
                }
            }
        }

        foreach ($wrong_attempts as $row) {
            for ($i = 1; $i <= 5; $i++) {
                $row->{"attempt$i"} ??= 0;
                $row->{"is_wrong$i"} = ($row->wrong_attempts[$i - 1] ?? 0);
            }
            \db::exec("
                INSERT INTO wrong_attempts 
                (competition, event, round, name, fc_id, type,value,
                attempt1,attempt2,attempt3,attempt4,attempt5,
                attempts_sum,cutoff_attempts,
                is_wrong1,is_wrong2,is_wrong3,is_wrong4,is_wrong5,
                is_ranked
                )  VALUES
                ('$row->competition', '$row->event', $row->round, '$row->name', '$row->fc_id', '$row->type',$row->value,
                $row->attempt1,$row->attempt2,$row->attempt3,$row->attempt4,$row->attempt5,
                $row->attempts_sum,$row->cutoff_attempts,
                $row->is_wrong1,$row->is_wrong2,$row->is_wrong3,$row->is_wrong4,$row->is_wrong5,
                $row->is_ranked
                )
                ");
        }
    }
}
