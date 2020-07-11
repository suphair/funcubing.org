<?php

function PageIndex() {
    return "//"
            . filter_input(INPUT_SERVER, 'HTTP_HOST')
            . PageLocal();
}

function PageLocal() {
    return str_replace('index.php', '', filter_input(INPUT_SERVER, 'PHP_SELF'));
}

function request($n = 0) {
    $prefix = str_replace("/index.php", "", filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
    $request_uri = explode('?', filter_input(INPUT_SERVER, 'REQUEST_URI'))[0];
    $request = explode('/', str_replace($prefix, '', $request_uri));
    foreach ($request as $key => $value) {
        if (!$value) {
            unset($request[$key]);
        }
    }
    $result = array_values($request);
    if($n == -1){
        return $result;    
    }
    
    return strtolower($result[$n] ?? false);
}
