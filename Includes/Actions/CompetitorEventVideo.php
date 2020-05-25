<?php

CheckPostIsset('ID','Video');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Video= DataBaseClass::Escape($_POST['Video']);


DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');
$Command=DataBaseClass::QueryGenerate(false);

CheckingRoleDelegate($Command['Event_Competition'],false);  
DataBaseClass::Query("Update Command set Video='$Video' where ID=$ID");


SetMessage();    
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  
