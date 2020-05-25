<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

CheckingRoleDelegate($ID);

DataBaseClass::Query("Update `Competition` set `Secret`='". random_string(16)."' where ID='$ID' ");

SetMessage();
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
