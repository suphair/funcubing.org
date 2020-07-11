<?php

$me = Wcaoauth::me();
if ($me) {
    if (filter_input(INPUT_GET, 'Save') !== NULL) {
        include 'post.save.php';
    }
    if (filter_input(INPUT_GET, 'Test') !== NULL) {
        include 'post.test.php';
    }
    if (filter_input(INPUT_GET, 'Subscribe') !== NULL) {
        $status = 1;
        include 'post.status.php';
    }
    if (filter_input(INPUT_GET, 'Unsubscribe') !== NULL) {
        $status = 0;
        include 'post.status.php';
    }
}

