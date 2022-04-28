
<?php

$wcaid = db::escape(filter_input(INPUT_POST, 'wcaid'));
$nameRU = db::escape(filter_input(INPUT_POST, 'nameRU'));

if ($wcaid) {
    if ($nameRU) {
        db::exec("UPDATE dict_competitors
        SET nameRU = '$nameRU'
        WHERE wcaid='$wcaid'");
    } else {
        db::exec("UPDATE dict_competitors
        SET nameRU = null
        WHERE wcaid='$wcaid'");
    }
}
?>