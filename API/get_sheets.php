<?php

namespace api;

function get_sheets($id) {
    $sheets = sheets($id);
    $json = $sheets;
    return $json;
}
