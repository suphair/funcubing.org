<?php
CheckPostIsset('ID','Groups');
CheckPostNotEmpty('ID','Groups');
CheckPostIsNumeric('ID','Groups');

$ID=$_POST['ID'];
CheckingRoleDelegateEvent($ID);
$Groups=$_POST['Groups'];

DataBaseClass::Query("Update `Event`set Groups='$Groups' where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
