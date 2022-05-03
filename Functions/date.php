<?php

function dateRange($start, $end = null, $month_full = false) {
    $parse = function($date, $month_full) {
        $time = strtotime($date);
        return [
    'year' => date("Y", $time),
    'month' =>
    $month_full ?
    t(
            date("F", $time),
            ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сенябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
            [date("n", $time)]
    ) : t(
            date("M", $time),
            ['', 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек']
            [date("n", $time)]
    ),
    'day' => date("j", $time)
        ];
    };

    $s = $parse($start, $month_full);
    $e = $parse($end ?? $start, $month_full);

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
