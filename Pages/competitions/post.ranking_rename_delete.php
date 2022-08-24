<?php

$id = db::escape(filter_input(INPUT_POST, 'id'));
$wcaid = db::escape(filter_input(INPUT_POST, 'wcaid'));
db::exec("DELETE FROM `unofficial_rename` WHERE id = $id and wcaid='$wcaid'");
