<?php
unset($_SESSION['delegate']);
unset($_SESSION['delegateID']);
unset($_SESSION['delegateName']);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();

