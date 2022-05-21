<?php

function dateRange($start, $end = null, $month_full = false) {
    $parse = function($date) {
        $time = strtotime($date);
        return [
    'year' => date("Y", $time),
    'month' => t(date("M", $time),
            ['', 'янв.', 'фев.', 'марта', 'апр.', 'мая', 'июня', 'июля', 'авг.', 'сент.', 'окт.', 'нояб.', 'дек.']
            [date("n", $time)]
    ),
    'day' => date("j", $time),
    'month_full' => t(date("F", $time),
            ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря']
            [date("n", $time)]
    )
        ];
    };

    $s = $parse($start);
    $e = $parse($end ?? $start);
    if ($month_full) {
        $s['month'] = $s['month_full'];
        $e['month'] = $e['month_full'];
    }

    if ($s['year'] != $e['year']) {
        $template = t(
                "s_month s_day, s_year - e_month e_day, e_year",
                "s_day s_month, s_year - e_day e_month_full, e_year"
        );
    } elseif ($s['month'] != $e['month']) {
        $template = t(
                "s_month s_day - e_month e_day, s_year",
                "s_day s_month - e_day e_month, s_year"
        );
    } elseif ($s['day'] != $e['day']) {
        $template = t(
                "s_month_full s_day - e_day, s_year",
                "s_day - e_day s_month_full, s_year"
        );
    } else {
        $template = t(
                "s_month_full s_day, s_year",
                "s_day s_month_full, s_year"
        );
    }



    return(str_replace(
                    [
                        's_day', 's_month_full', 's_month', 's_year',
                        'e_day', 'e_month_full', 'e_month', 'e_year',
                    ]
                    ,
                    [
                        $s['day'], $s['month_full'], $s['month'], $s['year'],
                        $e['day'], $e['month_full'], $e['month'], $e['year']
                    ]
                    , $template));
    /*
      if ($s['year'] == date("Y") and $e['year'] == date("Y")) {
      if ($s['month'] != $e['month']) {
      return "{$s['day']} {$s['month_full']} - {$e['day']} {$e['month_full']}";
      }
      if ($s['day'] != $e['day']) {
      return "{$s['day']} - {$e['day']} {$s['month_full']}";
      }
      return "{$s['day']} {$s['month_full']} ";
      }

      if ($s['year'] != $e['year']) {
      return "{$s['month']} {$s['day']}, {$s['year']} - {$e['month']} {$e['day']}, {$e['year']}";
      }
      if ($s['month'] != $e['month']) {
      return "{$s['month']} {$s['day']} - {$e['month']} {$e['day']}, {$s['year']}";
      }
      if ($s['day'] != $e['day']) {
      return "{$s['month']} {$s['day']} - {$e['day']}, {$s['year']}";
      }
      return "{$s['month']} {$s['day']}, {$s['year']}"; */
}
