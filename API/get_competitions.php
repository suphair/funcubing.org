<?php

namespace api;

function get_competitions($id = false) {
    $competitions = competitions($id);

    $filters = [
        'name' => ['type' => 'substring'],
        'organizer' => ['type' => 'equal', 'array' => 'organizers', 'value' => 'wca_id'],
        'delegate' => ['type' => 'equal', 'array' => 'delegates', 'value' => 'wca_id'],
        'from_date' => ['type' => 'date', 'compare' => 'later', 'key' => 'start_date'],
        'to_date' => ['type' => 'date', 'compare' => 'earlier', 'key' => 'end_date'],
        'is_ranked' => ['type' => 'boolean'],
        'is_publish' => ['type' => 'boolean'],
        'is_approved' => ['type' => 'boolean'],
    ];

    filter($competitions, $filters);
    $json = array_values($competitions);
    return $json;
}

function get_competition($id) {
    return get_competitions($id)[0] ?? null;
}
