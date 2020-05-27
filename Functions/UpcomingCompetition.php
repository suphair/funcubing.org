<?php

function SortUpcomingCompetitionByDate($a, $b) {
    if (strtotime($a['competition']['start_date']) != strtotime($b['competition']['start_date'])) {
        return strtotime($a['competition']['start_date']) > strtotime($b['competition']['start_date']);
    } else {
        return strtotime($a['competition']['end_date']) > strtotime($b['competition']['end_date']);
    }
}

function GetUpcomingCompetition($WCAID) {
    $data = GetValue('users_' . $WCAID, true);
    if (!$data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/users/" . $WCAID . "?upcoming_competitions=true");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        SaveValue('users_' . $WCAID, $data);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $upcoming_competitions = [];
        if ($status == 200) {
            $upcoming_competitions = json_decode($data, true);
        }
    } else {
        $upcoming_competitions = json_decode($data, true);
    }
    return $upcoming_competitions;
}
