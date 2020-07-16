<?php

namespace friends;

function cron() {
    $friends = \db::rows("SELECT DISTINCT friend FROM friends");
    foreach ($friends as $friend) {
        $user = \wcaapi::getUserCompetitionsUpcoming($friend->friend, __FILE__ . ': ' . __LINE__, FALSE);
        \competitor\actual($user->user ?? FALSE);
    }
    return count($friends);
}
