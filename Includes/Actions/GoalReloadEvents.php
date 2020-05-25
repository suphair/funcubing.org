<?php

unset($_SESSION["GoalEvents"]);
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();

