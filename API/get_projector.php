<?php

namespace api;

function get_projector($id, $event_code,  $round) {
    $projector = projector($id,  $event_code,  $round);
    $json = $projector;
    return $json;
}
