<?php

namespace api;

function get_competitors($id = false) {
    $competitors = competitors($id);

    $filters = [
        'name' => ['type' => 'substring'],
        'is_ranked' => ['type' => 'boolean', 'array' => 'competitions', 'value' => 'is_ranked', 'is_thin' => false],
    ];

    filter($competitors, $filters);
    $json = array_values($competitors);
    return $json;
}
