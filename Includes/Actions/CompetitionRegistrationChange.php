<?php

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];

CheckingRoleDelegate($ID);

if($_POST['submit']=='Registration enabled'){
    $Registration=1;
}

if($_POST['submit']=='Registration is disabled'){
    $Registration=0;
}

if(isset($Registration)){
    DataBaseClass::Query("Update `Competition` set Registration='".$Registration."' where `ID`='$ID'");
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
