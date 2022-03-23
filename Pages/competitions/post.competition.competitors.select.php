<?php

$competitors = filter_input(INPUT_POST, 'competitors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if(!$competitors){
    $competitors=[];
}
    
foreach ($competitors as $competitor) {
    $name = strip_tags(db::escape($competitor));
    if ($name) {
        db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name) VALUES ($comp->id,'$name')");
        unofficial\updateCompetitionCard($comp->id);
    }
}
