<?php
CheckingRoleAdmin();
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
DataBaseClass::Query("Delete from `Discipline` where `ID`='$ID'");

SetMessage("Delegate Deleted $ID");

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
