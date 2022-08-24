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
/*
if (filter_input(INPUT_POST, 'button', FILTER_DEFAULT) == 'FCID' and unofficial\admin()) {

    $registrations = filter_input(INPUT_POST, 'registrations', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (!is_array($registrations)) {
        $registrations = [];
    }

    foreach ($registrations as $competitorId => $registration) {
        if ($registration['FCID'] ?? false) {
            $FCID = strip_tags($registration['FCID']);
            if (strlen($FCID) == 2) {
                $name = db::row("select name from `unofficial_competitors` WHERE id = $competitorId")->name ?? false;
                $FCIDs = db::rows("select FCID from `unofficial_competitors` where FCID like '$FCID%' and name='$name'");
                if (sizeof($FCIDs) >= 1 and $FCIDs[0]->FCID ?? false) {
                    $FCID = $FCIDs[0]->FCID;
                } else {
                    $FCID = db::row("select CONCAT(left(max(FCID),2),right(CONCAT('00',right(max(FCID),2)+1),2))  FCID from `unofficial_competitors` where FCID like '$FCID%'")->FCID ?? "{$FCID}01";
                }
                db::exec("UPDATE IGNORE unofficial_competitors SET FCID = '$FCID' WHERE id = $competitorId ");
            }
            if (strlen($FCID) == 4) {
                db::exec("UPDATE IGNORE unofficial_competitors SET FCID = '$FCID' WHERE id = $competitorId ");
            }
            if (!$FCID) {
                db::exec("UPDATE IGNORE unofficial_competitors SET FCID = null WHERE id = $competitorId ");
            }
        }
    }
}
 */