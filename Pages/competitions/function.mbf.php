<?php

namespace MBF;

function time_to_str($time) {
    if ($time == false) {
        return false;
    }
    $hour = floor($time / 3600);
    $minute = floor(($time - $hour * 3600) / 60);
    $second = $time - $hour * 3600 - $minute * 60;
    if ($hour) {
        return sprintf("%d:%02d:%02d", $hour, $minute, $second);
    }
    return sprintf("%d:%02d", $minute, $second);
}

function time_to_int($time) {
    $explode = explode(":", $time);
    if (sizeof($explode) == 3) {
        if (!is_numeric($explode[2]) or!is_numeric($explode[1]) or!is_numeric($explode[0])) {
            return false;
        }
        return $explode[0] * 3600 + $explode[1] * 60 + $explode[2];
    } elseif (sizeof($explode) == 2) {
        if (!is_numeric($explode[1]) or!is_numeric($explode[0])) {
            return false;
        }
        return $explode[0] * 60 + $explode[1];
    } else {
        if (!is_numeric($explode[0])) {
            return false;
        }
        return $explode[0];
    }
}

function wcaResult($number, $solved, $time) {
    $missed = max($number - $solved, 0);
    $difference = $solved - $missed;
    if ($difference <= 0) {
        return "0999999900";
    }
    return sprintf("0%02d%05d%02d", 99 - $difference, $time, $missed);
}

function printResult($number, $solved, $time) {
    $missed = $number - $solved;
    $difference = $solved - $missed;
    if ($difference <= 0) {
        return 'DNF';
    }
    return sprintf("%d/%d %s", $solved, $number, time_to_str($time));
}
?>