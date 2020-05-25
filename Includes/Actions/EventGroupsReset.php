<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];

CheckingRoleDelegateEvent($ID,false);
DataBaseClass::Query("Update Command set `Group`=-1 where `Event`='$ID'");


header('Location: '.$_SERVER['HTTP_REFERER']);
exit();