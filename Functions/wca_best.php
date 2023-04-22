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
    ini_set('memory_limit', '1024M');


    foreach (db2::rows("select id from Events WHERE id!='333mbf'") as $event) {
        $wca_best_list = [];

        foreach (db2::rows("select best,personId from `RanksSingle` where eventId='$event->id'") as $row) {
            $wca_best_list[$row->personId]['single'] = $row->best;
        }

        foreach (db2::rows("select best,personId from `RanksAverage`  where eventId='$event->id'") as $row) {
            $wca_best_list[$row->personId]['average'] = $row->best;
        }

        $values = [];
        foreach (db::rows("SELECT wcaid FROM unofficial_fc_wca WHERE wcaid<>'' $where_ext") as $row) {

            $event_best = $wca_best_list[$row->wcaid] ?? FALSE;
            if ($event_best) {
                if (isset($event_best['average'])) {
                    $values[] = "('$row->wcaid','$event->id',{$event_best['single']},{$event_best['average']})";
                } else {
                    $values[] = "('$row->wcaid','$event->id',{$event_best['single']},null)";
                }
            }
        }

        if (sizeof($values)) {
            db::exec("INSERT INTO wca_best (wca_id, event, single, average ) "
                    . "VALUES " . implode(",", $values));
        }
    }
}
