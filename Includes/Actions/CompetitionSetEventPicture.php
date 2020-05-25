<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];

CheckDelegateCompetition($ID);
DataBaseClass::Query("Update `Competition` set EventPicture='".(isset($_POST['EventPicture'])+0)."' where `ID`='$ID'");
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
