<?php

$ranked = db::escape(filter_input(INPUT_POST, 'ranked')) ? 1 : 0;
$approved = db::escape(filter_input(INPUT_POST, 'approved')) ? 1 : 0;
$rankedID = db::escape(filter_input(INPUT_POST, 'rankedID'));
$rankedCompetitors = db::escape(filter_input(INPUT_POST, 'rankedCompetitors', FILTER_VALIDATE_INT));
$delegates = filter_input(INPUT_POST, 'delegates', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$delegates_role = filter_input(INPUT_POST, 'delegates_role', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$id = $competition->local_id;
if(!$rankedCompetitors){
    $rankedCompetitors=100;
}
db::exec("  UPDATE unofficial_competitions
            SET 
                ranked = $ranked,
                rankedApproved = $approved,
                rankedID = '$rankedID',
                rankedCompetitors = $rankedCompetitors
            WHERE id = $id ");


db::exec(" DELETE FROM  unofficial_competition_delegates where competition_id=$id");
foreach ($delegates as $j => $delegate) {
    if ($delegate and $delegates_role[$j] ?? false) {
        $delegate = db::escape($delegate);
        if (is_numeric($delegates_role[$j])) {
            db::exec(" INSERT IGNORE INTO unofficial_competition_delegates (delegate,competition_id,dict_delegate_role) values('$delegate',$id,{$delegates_role[$j]})");
        }
    }
}

if (!$ranked or!$rankedID) {
    db::exec("UPDATE unofficial_competitions SET rankedID = null, rankedApproved=0, ranked = 0, rankedCompetitors = null WHERE id = $id ");
    db::exec("DELETE from unofficial_competition_delegates WHERE competition_id = $id ");
}

if (!\config::isLocalhost()) {
    sendMail(
            config::get('Admin', 'email'), "FunCubing: Rating Competition"
            , print_r($_POST, true)
    );
}