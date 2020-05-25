<?php
CheckPostIsset('Name','Site','Password','ID');
CheckPostNotEmpty('Name','Site','ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

if($ID!=GetDelegate()){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}

$Name=$_POST['Name'];
$Site=$_POST['Site'];
$Password=$_POST['Password'];
if($Password==""){
    DataBaseClass::Query("Update `Delegate` set Name='$Name' , Site='$Site' where ID='$ID'");
}else{
    DataBaseClass::Query("Update `Delegate` set Name='$Name' , Site='$Site', Password='".md5('funcubing'.$Password)."' where ID='$ID'");    
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
