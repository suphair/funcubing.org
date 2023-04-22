<?php

namespace api;

function get_results($id, $event_id = false) {
    $results = results($id, $event_id);
    $json = array_values($results);
    return $json;
}
