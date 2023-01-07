<?php

$competitors = filter_input(INPUT_POST, 'competitor', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($competitors) {
    db2::set($get(2));
    foreach ($competitors as $FCID => $competitor) {
        unofficial\set_wca($FCID, $competitor['wcaid'] ?? false, ($competitor['nonwca'] ?? false) ? 1 : 0);
    }
}
?>