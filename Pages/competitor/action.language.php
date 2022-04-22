<?php

$_SESSION['user_lang'] = $_GET['lang'] ?? false;
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
