<?php

namespace api;

function get_results($id) {
    $results = results($id);
    $json = array_values($results);
    return $json;
}
