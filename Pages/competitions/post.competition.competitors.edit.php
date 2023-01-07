<?php

if (filter_input(INPUT_POST, 'button', FILTER_DEFAULT) == 'registrations') {
    $registrations = filter_input(INPUT_POST, 'registrations', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!is_array($registrations)) {
        $registrations = [];
    }

    if (sizeof($registrations)) {
        $competitorId = array_keys($registrations)[0];
        $ranked = db::row("select cn.ranked from unofficial_competitions cn join unofficial_competitors cr on cr.competition=cn.id where cr.id = $competitorId ")->ranked ?? false;
    }
    foreach ($registrations as $competitorId => $registration) {
        unset($registration['FCID']);
        $non_resident = ($registration['non_resident'] ?? 'off') == 'on' ? 0 : 1;
        unset($registration['non_resident']);
        if ($registration['name'] ?? false) {
            $name = strip_tags($registration['name']);
            unset($registration['name']);
            db::exec("UPDATE IGNORE unofficial_competitors SET name = '$name',non_resident = $non_resident, FCID = null WHERE id = $competitorId and (name != '$name' or coalesce(non_resident,0) != $non_resident) ");
            if ($ranked and!$non_resident) {
                unofficial\set_fc_id($competitorId, $name);
            }
        }
        if (!is_array($registration)) {
            $registration = [];
        }

        foreach ($registration as $event_dict => $flag) {

            $round = db::row("SELECT unofficial_events_rounds.id FROM unofficial_events_rounds "
                            . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event "
                            . " WHERE unofficial_events.event_dict = $event_dict"
                            . " AND unofficial_events.competition = $comp->id "
                            . " AND round = 1")->id ?? FALSE;

            if ($round and $flag == 'on') {
                db::exec("INSERT IGNORE INTO unofficial_competitors_round "
                        . " (competitor, round) VALUES ($competitorId, $round)");
            }
            if ($round and $flag == 'off') {
                db::exec("DELETE IGNORE FROM unofficial_competitors_round "
                        . " WHERE competitor = $competitorId AND round = $round ");
            }
        }
    }

    $registrations_delete = filter_input(INPUT_POST, 'registrations_delete', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (!is_array($registrations_delete)) {
        $registrations_delete = [];
    }
    foreach (array_keys($registrations_delete) as $competitorId) {

        db::exec("DELETE IGNORE FROM unofficial_competitors_round "
                . " WHERE unofficial_competitors_round.competitor = $competitorId");


        db::exec("DELETE IGNORE FROM unofficial_competitors "
                . " WHERE id = $competitorId");
    }
}
