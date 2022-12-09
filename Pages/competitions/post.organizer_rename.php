<?php

$wcaid = db::escape(filter_input(INPUT_POST, 'wcaid'));
$nameRU = db::escape(filter_input(INPUT_POST, 'nameRU'));

if ($wcaid) {
    if ($nameRU) {
        if (!is_numeric($wcaid)) {
            db::exec("UPDATE dict_competitors
                SET nameRU = '$nameRU'
                WHERE wcaid='$wcaid'");
        }
        if (is_numeric($wcaid) and $wcaid > 0) {
            db::exec("UPDATE dict_competitors
                SET nameRU = '$nameRU'
                WHERE wid='$wcaid'");
        }
    } else {
        db::exec("UPDATE dict_competitors
        SET nameRU = null
        WHERE wcaid='$wcaid'");
    }
}
?>