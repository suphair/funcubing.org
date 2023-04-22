<?php

namespace api;

function get_psychsheet($id, $event_code) {
    $psychsheet = psychsheet($id,  $event_code);
    $json = $psychsheet;
    return $json;
}
