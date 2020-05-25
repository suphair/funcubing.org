<?php
if(CheckAdmin()){
    
    CheckPostIsset('Instruction','Name');
    CheckPostNotEmpty('Instruction','Name');
    
    $Name=$_POST['Name'];
    $Instruction= DataBaseClass::Escape($_POST['Instruction']);
    
    DataBaseClass::Query("Delete from `BlockText` where Name='$Name'");
    
    DataBaseClass::Query("Insert into `BlockText` (Name, Value) values ('$Name','$Instruction')");
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  