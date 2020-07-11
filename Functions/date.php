<?php

function dateRange($start, $end = null) {
    $parse = function($date) {
        $time = strtotime($date);
        return [
            'year' => date("Y", $time),
            'month' => date("M", $time),
            'day' => date("j", $time)
        ];
    };

    $s = $parse($start);
    $e = $parse($end ?? $start);

    if ($s['year'] != $e['year']) {
        return "{$s['month']} {$s['day']}, {$s['year']} - {$e['month']} {$e['day']}, {$e['year']}";
    }
    if ($s['month'] != $e['month']) {
        return "{$s['month']} {$s['day']} - {$e['month']} {$e['day']}, {$s['year']}";
    }
    if ($s['day'] != $e['day']) {
        return "{$s['month']} {$s['day']} - {$e['day']}, {$s['year']}";
    }
    return "{$s['month']} {$s['day']}, {$s['year']}";
}
