<link href="<?= PageIndex() ?>Styles/goals.css" rel="stylesheet">
<?php
$me = wcaoauth::me();

$upcomings = [];
if ($me->id ?? FALSE) {
    $user = wcaapi::getUserCompetitionsUpcoming($me->id, __FILE__ . ": " . __LINE__, FALSE);
    $upcoming_competitions = $user->upcoming_competitions ?? [];
    $upcomings = array_column((array) $upcoming_competitions, 'id');
    foreach ($upcoming_competitions as $upcoming_competition) {
        goals\updateCompetition($upcoming_competition);
    }
}

$wca = db::escape(request(1));
if ($wca) {
    $row = db::row("SELECT wca FROM goals_competitions WHERE UPPER(wca) = UPPER('$wca')");
    $comp = $row->wca ?? FALSE;
    if ($comp) {
        include 'competition.php';
    } else {
        include 'competition.notfound.php';
    }
} else {
    include 'list.php';
}
