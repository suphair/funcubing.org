<?php

require_once 'functions.php';
header('Content-Type: application/json; charset=utf-8');
$method = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));

$json = ['error' => 'method not found'];
http_response_code(404);
if ($method == 'get' and $request_1 == 'competitions') {
    $competition_id = $request_2;
    $sub_query = $request_3;
    $json = api\get_competitions($competition_id);
    if ($competition_id and sizeof($json) != 1) {
        $json = ['error' => "Competition with id $competition_id not found"];
    } else {
        if ($sub_query) {
            $json = ['error' => 'method not found'];
        }
        if ($sub_query == 'registrations') {
            $json = api\get_registrations($competition_id);
        }
        if ($sub_query == 'events') {
            $json = api\get_events($competition_id);
        }
        if ($sub_query == 'results') {
            $json = api\get_results($competition_id);
        }
    }
}

if ($method == 'get' and $request_1 == 'competitors') {
    $competitor_id = $request_2;
    $json = api\get_competitors($competitor_id);
}

if ($method == 'get' and $request_1 == 'me') {
    $json = api\get_me();
}

if ($method == 'get' and!$request_1) {
    $json = [
        [
            'url' => 'api/competitions',
            'method' => 'get',
            'query' => [
                'name',
                'organizer',
                'judge',
                'from_date',
                'to_date',
                'is_ranked',
                'is_publish',
                'is_approved'
            ]
        ],
        [
            'url' => 'api/competitions/$id',
            'method' => 'get'
        ],
        [
            'url' => 'api/competitions/$id/registrations',
            'method' => 'get'
        ],
        [
            'url' => 'api/competitions/$id/events',
            'method' => 'get'
        ],
        [
            'url' => 'api/competitions/$id/results',
            'method' => 'get'
        ],
        [
            'url' => 'api/competitors',
            'method' => 'get',
            'query' => [
                'is_ranked',
                'name',
                'fc_id',
                'wca_id'
            ]
        ],
        [
            'url' => 'api/competitors/$fc_id',
            'method' => 'get'
        ],
        [
            'url' => 'api/competitors/$competitor_id',
            'method' => 'get'
        ],
        [
            'url' => 'api/me',
            'method' => 'get'
        ]
    ];
}

if ($json != ['error' => 'method not found']) {
    http_response_code(200);
}

echo json_encode($json,
        JSON_PRETTY_PRINT +
        JSON_UNESCAPED_SLASHES +
        JSON_UNESCAPED_UNICODE);


exit();
