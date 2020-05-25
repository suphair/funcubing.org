<?php
include_once  'initIncluders.php';
if(isset($_POST['color']) and in_array($_POST['color'],['W','B'])){
    Mosaic::setColor($_POST['color']);
}

header('Location:index.php');
exit;