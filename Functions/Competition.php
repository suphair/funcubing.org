<?php

function date_range($start, $end = '') {
    if (!$end)
        $end = $start;
    if (sizeof(explode("-", $start)) != 3 or sizeof(explode("-", $end)) != 3) {
        return '-';
    }

    list($ys, $ms, $ds) = explode("-", $start);
    list($ye, $me, $de) = explode("-", $end);

    $Month = array(
        "01" => "Jan",
        "02" => "Feb",
        "03" => "Mar",
        "04" => "Apr",
        "05" => "May",
        "06" => "Jun",
        "07" => "Jul",
        "08" => "Aug",
        "09" => "Sep",
        "10" => "Oct",
        "11" => "Nov",
        "12" => "Dec"
    );


    if ($ys != $ye) {
        return "{$Month[$ms]} $ds, $ys - {$Month[$me]} $de, $ye";
    } else {
        if ($ms != $me) {
            return "{$Month[$ms]} $ds - {$Month[$me]} $de, $ys";
        } else {
            if ($ds != $de) {
                return "{$Month[$ms]} $ds - $de, $ys";
            } else {
                return "{$Month[$ms]} $ds, $ys";
            }
        }
    }


    //$ss="{$Month[$ms]} $ds"
    //return "$ss, $ys";
}

function Competitor_Interval($Competition_Date) {
    $str = substr($Competition_Date, -4) . '-';
    $Month = array(
        "Jan" => "01",
        "Feb" => "02",
        "Mar" => "03",
        "Apr" => "04",
        "May" => "05",
        "Jun" => "06",
        "Jul" => "07",
        "Aug" => "08",
        "Sep" => "09",
        "Oct" => "10",
        "Nov" => "11",
        "Dec" => "12"
    );
    $str .= $Month[substr($Competition_Date, 0, 3)] . '-';
    $str .= substr('0' . trim(substr($Competition_Date, 4, 2)), -2);
    $date = date_create($str);

    $interval = date_diff($date, date_create(date('Y-m-d')))->format('%R%a');

    return $interval->format('%R%a');
}

function Competitor_Date_Start($Competition_Date) {
    $str = substr($Competition_Date, -4) . '-';
    $Month = array(
        "Jan" => "01",
        "Feb" => "02",
        "Mar" => "03",
        "Apr" => "04",
        "May" => "05",
        "Jun" => "06",
        "Jul" => "07",
        "Aug" => "08",
        "Sep" => "09",
        "Oct" => "10",
        "Nov" => "11",
        "Dec" => "12"
    );
    $str .= $Month[substr($Competition_Date, 0, 3)] . '-';
    $str .= substr('0' . trim(substr($Competition_Date, 4, 2)), -2);

    return $str;
}

function UpdateLocalID($competition) {

    DataBaseClass::Query("select E1.ID,count(*)-1 LocalID from  Event E1 join Event E2 on E2.ID<=E1.ID and E1.Competition=E2.Competition
    where E1.Competition=$competition
    group by E1.ID");

    $LocalIDs = DataBaseClass::getRows();


    foreach ($LocalIDs as $row) {
        DataBaseClass::Query("update Event set LocalID='" . $row['LocalID'] . "' where ID='" . $row['ID'] . "'");
    }
}

function getCompetitionRegistration($wca) {

    $persons = Suphair \ Wca \ Api::
            getCompetitionRegistrations(
                    $wca, 'getCompetitionRegistration', [], false);

    $registrations = [];
    foreach ($persons as $person) {
        $registrations[$person->user_id] = $person->event_ids;
    }

    return $registrations;
}
