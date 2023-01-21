<?php

function db_set_wca() {
    $get = function($instance = false) {
        $keys = ['host', 'username', 'password', 'schema', 'port'];
        foreach ($keys as $key) {
            $values[$key] = config::get('DB' . $instance, $key);
        }
        return $values;
    };

    db2::set($get(2));
}

function reload_wca_best_full() {
    db::exec("DELETE FROM wca_best");
    reload_wca_best("");
}

function reload_wca_best_part() {
    reload_wca_best("and wcaid not in (select wca_id from wca_best)");
}

function reload_wca_best($where_ext) {
    db_set_wca();

    foreach (db::rows("SELECT wcaid FROM unofficial_fc_wca WHERE wcaid<>'' $where_ext") as $row) {
        $single_wca = db2::rows("select eventId, best from `RanksSingle` WHERE personId='$row->wcaid'");
        $average_wca = db2::rows("select eventId, best from `RanksAverage` WHERE personId='$row->wcaid'");

        $wca_best_list = [];
        foreach ($single_wca as $srow) {
            $event = $srow->eventId;
            $wca_best_list[$event]['single'] = $srow->best;
        }
        foreach ($average_wca as $arow) {
            $event = $arow->eventId;
            $wca_best_list[$event]['average'] = $arow->best;
        }

        foreach ($wca_best_list as $event_id => $event_best) {
            if ($event_id !== '333mbf') {
                if (isset($event_best['average'])) {
                    db::exec("INSERT INTO wca_best (wca_id, event, single, average ) "
                            . "VALUES ('$row->wcaid','$event_id',{$event_best['single']},{$event_best['average']})");
                } else {
                    db::exec("INSERT INTO wca_best (wca_id, event, single) "
                            . "VALUES ('$row->wcaid','$event_id',{$event_best['single']})");
                }
            }
        }
    }
}
