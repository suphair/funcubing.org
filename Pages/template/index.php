<?php

if (!config::get('Admin', 'wcaid')) {
    die(json_encode(['error' => 'Access denied']));
}

$dir = 'Templates';

include 'config.php';
include 'db.php';

die(json_encode(['message' => 'Templates generation is complete']));

