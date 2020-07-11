<?php

$me = Suphair\Wca\Oauth::me();
$friend = db::escape(filter_input(INPUT_POST, 'friend'));
if ($me->wca_id ?? FALSE and $friend) {
    if (filter_input(INPUT_GET, 'Add') !== NULL) {
        include 'post.add.php';
    }
    if (filter_input(INPUT_GET, 'Remove') !== NULL) {
        include 'post.remove.php';
    }
}
