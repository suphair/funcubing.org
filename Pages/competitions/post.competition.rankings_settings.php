<?php

$ranked = db::escape(filter_input(INPUT_POST, 'ranked')) ? 1 : 0;
$rankedID = db::escape(filter_input(INPUT_POST, 'rankedID'));
$rankedJudgeSenior = db::escape(filter_input(INPUT_POST, 'rankedJudgeSenior'));
$rankedJudgeJunior = db::escape(filter_input(INPUT_POST, 'rankedJudgeJunior'));

db::exec("  UPDATE  unofficial_competitions
            SET 
                ranked = $ranked,
                rankedID = '$rankedID',
                rankedJudgeSenior = '$rankedJudgeSenior',
                rankedJudgeJunior = '$rankedJudgeJunior'
            WHERE id = {$comp->id} ");

if (!$rankedID) {
    db::exec("UPDATE  unofficial_competitions SET  rankedID = null WHERE id = {$comp->id} ");
}