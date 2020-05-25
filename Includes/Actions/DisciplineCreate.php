<?php

CheckingRoleAdmin();
CheckPostIsset('Name','Code');
CheckPostNotEmpty('Name','Code');

$Name=$_POST['Name'];
$Code= str_replace(" ","",$_POST['Code']);

DataBaseClass::Query("Insert into  `Discipline` ( Name,Code,Status) VALUES('$Name','$Code','Archive')");

SetMessage("Discipline create $Name");

$url=PageIndex()."Discipline/".$Code."/config";
    
header('Location: '.$url);
exit();  
