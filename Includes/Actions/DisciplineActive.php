<?php
CheckingRoleAdmin();
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
DataBaseClass::Query("Update `Discipline` "
        . "set Status='Active'"
        . " where `ID`='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
