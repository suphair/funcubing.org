<?php

$wcaid = strtoupper(db::escape(filter_input(INPUT_POST, 'wcaid')));
$name = ucfirst(db::escape(filter_input(INPUT_POST, 'name')));

db::exec("DELETE FROM `unofficial_rename` WHERE upper(wcaid)=upper('$wcaid')");
db::exec("INSERT INTO `unofficial_rename` (wcaid, name,description) VALUES ('$wcaid','$name','wca_rename')");

db::exec("UPDATE dict_competitors SET name='$name' WHERE wcaid='$wcaid'");
