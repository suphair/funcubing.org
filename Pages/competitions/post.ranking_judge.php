
<?php

$nameRU = db::escape(filter_input(INPUT_POST, 'nameRU'));
$rankEN = db::escape(filter_input(INPUT_POST, 'rankEN'));
$rankRU = db::escape(filter_input(INPUT_POST, 'rankRU'));
$is_archive = db::escape(filter_input(INPUT_POST, 'is_archive')) ? 1 : 0;
$wcaid = db::escape(filter_input(INPUT_POST, 'wcaid'));

if ($wcaid) {
    db::exec("UPDATE unofficial_judges
        SET is_archive = $is_archive, rank = '$rankEN', rankRU = '$rankRU'
        WHERE wcaid='$wcaid'");
}
if ($nameRU) {
    db::exec("UPDATE dict_competitors
        SET nameRU = '$nameRU'
        WHERE wcaid='$wcaid'");
} else {
    db::exec("UPDATE dict_competitors
        SET nameRU = null
        WHERE wcaid='$wcaid'");
}
?>