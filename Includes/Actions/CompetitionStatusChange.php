<?php
CheckPostIsset('ID','Status');
CheckPostNotEmpty('ID','Status');
CheckPostIsNumeric('ID','Status');

$ID=$_POST['ID'];
$Status=$_POST['Status'];
CheckingRoleDelegate($ID);

DataBaseClass::Query("Update `Competition` set Status='$Status' where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
