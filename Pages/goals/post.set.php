<?php

$comp = db::escape(request(1));
$data = filter_input(INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!$comp
        OR!is_array($data)
        OR!db::row("SELECT * FROM goals_competitions "
                . "WHERE wca = '$comp' AND NOT resultsLoad")) {
    goto skip;
}

foreach ($data as $keyEvent => $dataEvent) {
    foreach ($dataEvent as $format => $dataFormat) {
        $goal = $dataFormat['goal'] ?? FALSE;
        $record = $dataFormat['record'] ?? FALSE;
        $event = db::row("SELECT code FROM goals_events "
                        . "WHERE code = '" . db::escape($keyEvent) . "'");
        if ($goal === FALSE
                OR!in_array($format, ['single', 'average'])
                OR!($event->code ?? FALSE)
        ) {
            goto skip;
        }

        if ($event->code == '333fm'
                AND $format == 'average'
                AND strlen($goal) == 3) {
            $goal = substr($goal, 1, 2) . ".00";
        }
        $progress = goals\progress($record, $goal);

        $row = db::row("SELECT id FROM goals "
                        . " WHERE competition = '$comp' "
                        . " AND event = '{$event->code}' "
                        . " AND competitor = {$me->id} "
                        . " AND format = '$format'");

        $id = $row->id ?? FALSE;
        if ($id and $goal == '') {
            db::exec("DELETE FROM goals WHERE id='$id'");
        }
        if (!$id and $goal != '') {
            db::exec("INSERT INTO goals (competition, event, competitor, format, goal, record, progress) "
                    . "VALUES ('$comp', '{$event->code}', {$me->id}, '$format', '$goal', '$record', '$progress') ");
        }
        if ($id and $goal != '') {
            db::exec("UPDATE goals "
                    . " SET goal = '$goal', "
                    . " record = '$record', "
                    . " progress = '$progress' "
                    . " WHERE id = '$id' ");
        }

        db::exec("REPLACE INTO goals_competitors "
                . " SET eventCode = '{$event->code}', "
                . " competitionWca = '$comp', "
                . " competitorWid = '{$me->id}', "
                . " timestamp = now() ");
    }
}
skip:
