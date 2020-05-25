<?php
$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$ID=$request[2];

CheckingRoleDelegateEvent($ID,false);

DataBaseClass::Query("Update `Event` set `Secret`='". random_string(16)."' where ID='$ID' ");

SetMessageName("EventGenerateScoreTakerMessage", "Link updated");
SetMessage();
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
