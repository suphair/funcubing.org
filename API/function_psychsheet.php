<?php

namespace api;

function psychsheet($id, $event_code) {

    if (!$event_code) {
        die("Event not set");
    }
    if ($event_code === '333mbf') {
        die("3x3x3 Multi-Blind is not supported");
    }

    $best_wca = \db::rows("SELECT wca_id, single, average FROM wca_best WHERE event='$event_code'");
    $best_fc = \db::rows("SELECT fc_id, wca_id, single, average FROM fc_best WHERE event='$event_code'");
    $best_wca_id = [];
    foreach ($best_wca as $b) {
        $best_wca_id[$b->wca_id] = (object) ['single' => $b->single, 'average' => $b->average];
    }
    $best_fc_id = [];
    foreach ($best_fc as $b) {
        $best_fc_id[$b->fc_id] = (object) ['single' => $b->single, 'average' => $b->average];
    }

    $registrations = get_registrations($id);
    $competitors = [];
    foreach ($registrations as $registration) {
        if (in_array($event_code, $registration->event_ids)) {
            $average = 0;
            $single = 0;
            if ($registration->fc_id ?? false) {
                $fc_single = $best_fc_id[$registration->fc_id]->single ?? 0;
                $fc_average = $best_fc_id[$registration->fc_id]->average ?? 0;
                $single = $fc_single;
                $average = $fc_average;

                if ($best_wca_id[$registration->wca_id] ?? false) {

                    $wca_average = $best_wca_id[$registration->wca_id]->average ?? 0;
                    $wca_single = $best_wca_id[$registration->wca_id]->single ?? 0;
                    if ($wca_average > 0 and ($wca_average < $fc_average or!$fc_average)) {
                        $average = $wca_average;
                    }
                    if ($wca_single > 0 and ($wca_single < $fc_single or!$fc_single)) {
                        $single = $wca_single;
                    }
                }
            }

            $competitor = (object) [
                        'fc_id' => $registration->fc_id ?? null,
                        'wca_id' => $registration->wca_id ?? null,
                        'name' => $registration->name,
                        'single' => $single + 0,
                        'average' => $average + 0,
                        'fc' => null,
                        'wca' => null
            ];
            if ($competitor->fc_id and $fc_single ?? false) {
                $competitor->fc = (object) [
                            'single' => ($fc_single ?? 0) + 0,
                            'average' => ($fc_average ?? 0) + 0];
            }
            if ($competitor->wca_id and $wca_single ?? false) {
                $competitor->wca = (object) [
                            'single' => ($wca_single ?? 0) + 0,
                            'average' => ($wca_average ?? 0) + 0];
            }

            $competitors[] = $competitor;
        }
    }

    function sort_best_average($a, $b) {
        if ($a->average and!$b->average) {
            return false;
        }
        if (!$a->average and $b->average) {
            return true;
        }
        if ($a->average == $b->average) {
            if ($a->single and!$b->single) {
                return false;
            }
            if (!$a->single and $b->single) {
                return true;
            }
            return $a->single > $b->single;
        }
        return $a->average > $b->average;
    }

    function sort_best_single($a, $b) {
        if ($a->single and!$b->single) {
            return false;
        }
        if (!$a->single and $b->single) {
            return true;
        }
        if ($a->single == $b->single) {
            if ($a->average and!$b->average) {
                return false;
            }
            if (!$a->average and $b->average) {
                return true;
            }
            return $a->average > $b->average;
        }
        return $a->single > $b->single;
    }

    if (in_array($event_code, ['333bf', '333fm', '444bf', '555bf', '666', '777'])) {
        $format_best = true;
    } else {
        $format_best = false;
    }

    usort($competitors,
            $format_best ? 'api\sort_best_single' : 'api\sort_best_average');

    return $competitors;
}
