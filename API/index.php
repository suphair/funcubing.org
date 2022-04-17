<?php

header('Content-Type: application/json; charset=utf-8');
$method = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));

$json = ['error' => 'method not found'];
http_response_code(404);
#$request_1 ??= false;

if ($method == 'get' and $request_1 == 'competitions') {
    $json = api\get_competitions();
}

if ($method == 'get' and $request_1 == 'me') {
    $json = api\get_me();
}

if ($method == 'get' and!$request_1) {
    $json = [
        [
            'url' => 'api/competitions',
            'method' => 'get'
        ],
        [
            'url' => 'api/competitions/$id',
            'method' => 'get'
        ],
        [
            'url' => 'api/me',
            'method' => 'get'
        ]
    ];
}

echo json_encode($json,
        JSON_PRETTY_PRINT +
        JSON_UNESCAPED_SLASHES +
        JSON_UNESCAPED_UNICODE);


exit();
