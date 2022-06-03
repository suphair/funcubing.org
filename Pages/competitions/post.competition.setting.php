<?php

$website = db::escape(filter_input(INPUT_POST, 'website'));
$name = db::escape(filter_input(INPUT_POST, 'name'));
$city = db::escape(filter_input(INPUT_POST, 'city'));
$details = db::escape(filter_input(INPUT_POST, 'details'));
$logo = db::escape(filter_input(INPUT_POST, 'logo'));
$secret = db::escape(filter_input(INPUT_POST, 'secret'));
$date = date('Y-m-d', strtotime(db::escape(filter_input(INPUT_POST, 'date'))));
$date_to = date('Y-m-d', strtotime(db::escape(filter_input(INPUT_POST, 'date_to'))));
$show = db::escape(filter_input(INPUT_POST, 'show')) ? 1 : 0;
$shareRegistration = filter_input(INPUT_POST, 'shareRegistration') ? 1 : 0;

$secretRegistration = 'null';
if (filter_input(INPUT_POST, 'registration') or $shareRegistration) {
    $secretRegistration = "'" . substr(md5($secret), 0, 10) . "'";
}

db::exec("  UPDATE  unofficial_competitions
            SET 
                name = '$name',
                city = '$city',
                details = '$details',
                logo = '$logo',
                date = '$date',
                date_to = " . ($date_to == '1970-01-01' ? 'null' : "'$date_to'" ) . ",
                secretRegistration = $secretRegistration,
                shareRegistration = $shareRegistration,
                website = '$website',
                `show` = $show
            WHERE id = {$comp->id} ");

