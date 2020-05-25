<?php

unset($_SESSION["GoalCompetitions"]);
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();

