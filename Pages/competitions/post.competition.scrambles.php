<?php

$competition = db::escape(filter_input(INPUT_POST, 'competition'));
$json = db::escape(filter_input(INPUT_POST, 'json'));

db::exec(" DELETE FROM scrambles WHERE competition = $competition");
if ($json) {
    db::exec(" INSERT INTO scrambles (competition, json) VALUES ($competition, '$json')");
}