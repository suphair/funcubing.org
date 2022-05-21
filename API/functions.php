<?php

namespace api;

function attempt_centiseconds($attempt) {
    $attempt_raw = $attempt;
    $attempt = str_replace(['(', ')'], ['', ''], $attempt);
    if (strtolower($attempt) == 'dnf') {
        return -1;
    }
    if (strtolower($attempt) == 'dns') {
        return -2;
    }
    if (!$attempt or $attempt == '-cutoff') {
        return 0;
    }
    $attempt = str_replace([':', '.'], '', $attempt);
    $attempt = substr('000000' . $attempt, -6, 6);

    $minute = substr($attempt, 0, 2);
    $second = substr($attempt, 2, 2);
    $centisecond = substr($attempt, 4, 2);
    if (!is_numeric($minute) or!is_numeric($second) or!is_numeric($centisecond)) {
        trigger_error($attempt_raw, E_USER_NOTICE);
    }
    return $minute * 60 * 100 + $second * 100 + $centisecond;
}
