<?php
$me=wcaoauth::me();
if ($me->name ?? FALSE ) {
    include 'block.logout.php';
} else {
    include 'block.login.php';
} 
