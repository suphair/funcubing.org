<?php
CheckPostIsset('ID','Comment');
CheckPostNotEmpty('ID');
CheckPostIsnumeric('ID');

$ID=$_POST['ID'];
$Comment= DataBaseClass::Escape($_POST['Comment']);

CheckingRoleDelegateEvent($ID);

DataBaseClass::Query("Update `Event` set Comment='$Comment'  where `ID`='$ID'");

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

