<?php

$me = wcaoauth::me();
if ($me->wca_id ?? FALSE) {
    if (filter_input(INPUT_GET, 'set') !== NULL) {
        include 'post.set.php';
    }
}
