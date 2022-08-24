<?php

namespace api;

function get_events($comp_id, $event_id = false) {
    $events = events($comp_id, $event_id);
    $json = array_values($events);
    return $json;
}

function get_event($comp_id, $event_id) {
    return get_events($comp_id, $event_id)[0] ?? null;
}
