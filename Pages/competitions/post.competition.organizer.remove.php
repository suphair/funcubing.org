<?php

$wcaid = db::escape(filter_input(INPUT_POST, 'wcaid'));
db::exec("DELETE FROM `unofficial_organizers` WHERE competition = '$comp->id' AND wcaid = UPPER('$wcaid')");
