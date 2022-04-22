<?php

namespace api;

function get_registrations($id) {
    $registrations = registrations($id);
    $json = array_values($registrations);
    return $json;
}
