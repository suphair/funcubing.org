<?php

$action = filter_input(INPUT_GET, 'action');

if ($action == 'login') {
    include 'action.login.php';
}

if ($action == 'logout') {
    include 'action.logout.php';
}
