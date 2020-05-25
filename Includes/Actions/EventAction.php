<?php

if($_POST['EventButton']=='Create'){
    include 'EventCreate.php';
}

if($_POST['EventButton']=='Change'){
    include 'EventChange.php';
}

if($_POST['EventButton']=='Delete'){
    include 'EventDelete.php';
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  