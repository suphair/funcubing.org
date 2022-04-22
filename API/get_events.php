<?php

namespace api;

function get_events($id) {
    $events = events($id);
    $json = array_values($events);
    return $json;
}
