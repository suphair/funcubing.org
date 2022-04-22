<?php

namespace api;

function attempt_centiseconds($attempt) {
    $attempt = str_replace(['(', ')'], ['', ''], $attempt);
    if ($attempt == 'DNF' or $attempt == 'dnf') {
        return -1;
    }
    if ($attempt == 'DNS' or $attempt == 'dns') {
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
    return $minute * 60 * 100 + $second * 100 + $centisecond;
}
