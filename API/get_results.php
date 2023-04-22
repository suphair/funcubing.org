<?php

namespace api;

function get_scrambles($id) {
    $scrambles = scrambles($id);
    return $scrambles;
}
