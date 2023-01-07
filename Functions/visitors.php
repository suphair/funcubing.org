<?php

function count_visitors() {

    $client = $_SERVER['HTTP_CLIENT_IP'] ?? null;
    $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    $remote = $_SERVER['REMOTE_ADDR'] ?? null;
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $request_uri = $_SERVER['REQUEST_URI'] ?? null;

    if (filter_var($client, FILTER_VALIDATE_IP))
        $ip = $client;
    elseif (filter_var($forward, FILTER_VALIDATE_IP))
        $ip = $forward;
    else
        $ip = $remote;

    $ip = db::escape($ip);
    $agent = str_replace("'", "", db::escape($agent));
    $request_uri = db::escape($request_uri);

    if (!preg_match('/Dalvik|bot|crawl|slurp|spider|mediapartners/i', $agent)) {
        db::exec("INSERT INTO visitors (ip,agent,request_uri) values ('$ip','$agent','$request_uri')");
    }
}

function get_count_visitors_day() {
    return
            db::row("select count(distinct ip) count 
        from `visitors` 
        where `timestampt` > NOW() - INTERVAL 1 DAY ")->count ?? 0;
}
