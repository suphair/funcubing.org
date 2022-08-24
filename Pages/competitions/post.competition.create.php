<?php

$name = db::escape(filter_input(INPUT_POST, 'name'));
$date = date('Y-m-d', strtotime(db::escape(filter_input(INPUT_POST, 'date'))));
$secret = unofficial\generateSecret();

db::exec("INSERT INTO `unofficial_competitions` (name, date, competitor, secret) "
        . "VALUES ('$name','$date'," . $me->wid . ",'$secret')");

if (db::affected()) {
    header('Location: ' . PageIndex() . "competitions/$secret/setting");
    exit();
}

