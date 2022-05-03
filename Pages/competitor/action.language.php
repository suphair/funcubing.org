<?php

$_SESSION['user_lang'] = $_GET['lang'] ?? false;
if ($_SERVER['HTTP_REFERER'] ?? false) {
    $url = explode('?', $_SERVER['HTTP_REFERER'])[0];
    header('Location: ' . $url);
    exit();
}
