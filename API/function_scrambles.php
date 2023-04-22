<?php

namespace api;

function scrambles($competition_id) {
    $json_scrambles = \db::row("select json 
        from scrambles 
        JOIN unofficial_competitions uc
        ON scrambles.competition = uc.id
        WHERE lower('$competition_id') in (lower(uc.secret), lower(uc.rankedID), '')") ?? null;
    if ($json_scrambles) {
        return json_decode($json_scrambles->json);
    } else {
        http_response_code(404);
        return ['error' => 'scrambles not found'];
        exit();
    }
}
