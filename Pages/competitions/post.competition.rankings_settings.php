<?php

$ranked = db::escape(filter_input(INPUT_POST, 'ranked')) ? 1 : 0;
$approved = db::escape(filter_input(INPUT_POST, 'approved')) ? 1 : 0;
$rankedID = db::escape(filter_input(INPUT_POST, 'rankedID'));
$judges = filter_input(INPUT_POST, 'judges', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$judges_role = filter_input(INPUT_POST, 'judges_role', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
db::exec("  UPDATE unofficial_competitions
            SET 
                ranked = $ranked,
                rankedApproved = $approved,
                rankedID = '$rankedID'
            WHERE id = {$comp->id} ");


db::exec(" DELETE FROM  unofficial_competition_judges where competition_id={$comp->id}");
foreach ($judges as $j => $judge) {
    if ($judge and $judges_role[$j] ?? false) {
        $judge = db::escape($judge);
        if (is_numeric($judges_role[$j])) {
            db::exec(" INSERT IGNORE INTO unofficial_competition_judges (judge,competition_id,dict_judge_role) values('$judge',{$comp->id},{$judges_role[$j]})");
        }
    }
}

if (!$ranked or!$rankedID) {
    db::exec("UPDATE unofficial_competitions SET rankedID = null, rankedApproved=0, ranked = 0 WHERE id = {$comp->id} ");
    db::exec("DELETE from unofficial_competition_judges WHERE competition_id = {$comp->id} ");
}

sendMail(
        config::get('Admin', 'email'), "FunCubing: Rating Competition"
        , print_r($_POST, true)
);
