<?php

CheckPostIsset('ID','Registration','Status','Onsite');
CheckPostNotEmpty('ID','Registration','Status','Onsite');
CheckPostIsNumeric('ID','Registration','Status','Onsite');

$ID=$_POST['ID'];
$Registration=$_POST['Registration'];
$Status=$_POST['Status'];
$Onsite=$_POST['Onsite'];

CheckDelegateCompetition($ID);
DataBaseClass::Query("Update `Competition` set Registration='$Registration',Status='$Status',Onsite='$Onsite' where `ID`='$ID'");
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
