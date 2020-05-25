<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];;

CheckingRoleDelegateEvent($ID);

$commands=count(DataBaseClass::SelectTableRows('Command',"Event=$ID"));
if(!$commands){
    DataBaseClass::Query("Delete from  `Scramble` where `Event`=$ID");
    DataBaseClass::Query("Delete from  `Event` where `ID`=$ID");
}

SetMessage();
    
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  