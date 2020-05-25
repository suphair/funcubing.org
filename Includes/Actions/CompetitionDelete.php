<?php
CheckingRoleAdmin();
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];

DataBaseClass::Query("Delete from `CompetitionReport` where `Competition`='$ID'");
DataBaseClass::Query("Delete from `CompetitionDelegate` where `Competition`='$ID'");
DataBaseClass::Query("Delete from `Competition` where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
