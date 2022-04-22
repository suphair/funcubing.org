<?php

namespace api;

function get_competitions($id = false) {
    $competitions = competitions($id);

    $filters = [
        'name' => ['type' => 'substring'],
        'organizer' => ['type' => 'equal', 'array' => 'organizers', 'value' => 'wca_id'],
        'judge' => ['type' => 'equal', 'array' => 'judges', 'value' => 'wca_id'],
        'from_date' => ['type' => 'date', 'compare' => 'later', 'key' => 'start_date'],
        'to_date' => ['type' => 'date', 'compare' => 'earlier', 'key' => 'end_date'],
        'is_ranked' => ['type' => 'boolean'],
        'is_publish' => ['type' => 'boolean'],
    ];

    filter($competitions, $filters);
    $json = array_values($competitions);
    return $json;
}
