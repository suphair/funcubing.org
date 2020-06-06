<?php

function SortUpcomingCompetitionByDate($a, $b) {
    if (strtotime($a['competition']['start_date']) != strtotime($b['competition']['start_date'])) {
        return strtotime($a['competition']['start_date']) > strtotime($b['competition']['start_date']);
    } else {
        return strtotime($a['competition']['end_date']) > strtotime($b['competition']['end_date']);
    }
}

function GetUpcomingCompetition($wid) {
    return $resultsApi = Suphair \ Wca \ Api ::getUserCompetitionsUpcoming($wid, 'GetUpcomingCompetition');
}
