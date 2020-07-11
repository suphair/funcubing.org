<?php

$person = wcaapi::getPerson($friend, __FILE__, [], false);
$user = wcaapi::getUser($friend, __FILE__, [], false);
postSet('friend', $friend);

if (!($person->person ?? FALSE)) {
    postSet('error', "Person with WCA ID [$friend] not found");
} elseif (!($user->user ?? FALSE)) {
    postSet('error', "User with WCA ID [$friend] not found");
} elseif ($friend == $me->wca_id) {
    postSet('error', "It is your WCA ID [$friend]");
} else {
    competitor\actual($person->person ?? FALSE);
    postSet('friend', FALSE);
    db::exec("INSERT IGNORE INTO friends SET friend = '$friend', user = '{$me->wca_id}'");
    if (!db::id()) {
        postSet('message', "[$friend] already your friend");
    } else {
        postSet('message', "[$friend] added to friends");
    }
}
