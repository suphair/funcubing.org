<?php

function postSet($key, $value) {
    $request = implode('/', request(-1));
    $_SESSION[$request][$key] = $value;
}

function postGet($key) {
    $request = implode('/', request(-1));
    $value = $_SESSION[$request][$key] ?? FALSE;
    unset($_SESSION[$request][$key]);
    return $value;
}
