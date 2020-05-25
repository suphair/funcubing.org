<?php
if(CheckAdmin()){
    CheckPostIsset('Comment','Name','Country');
    
    CheckPostNotEmpty('Name');
    
    $Name=$_POST['Name'];
    $Comment= DataBaseClass::Escape($_POST['Comment']);
    $Country= DataBaseClass::Escape($_POST['Country']);
    
    if($Country){
        DataBaseClass::Query("Select * from `BlockText` where Name='$Name' and Country='$Country'");
        if(DataBaseClass::rowsCount()){
            DataBaseClass::Query("Update `BlockText` set Value='$Comment' where Name='$Name' and Country='$Country'");
        }else{
            DataBaseClass::Query("Insert into `BlockText` (Name, Value,Country) values ('$Name','$Comment','$Country')");
        }
    }else{  
        DataBaseClass::Query("Select * from `BlockText` where Name='$Name'");
        if(DataBaseClass::rowsCount()){
            DataBaseClass::Query("Update `BlockText` set Value='$Comment' where Name='$Name'");
        }else{
            DataBaseClass::Query("Insert into `BlockText` (Name, Value) values ('$Name','$Comment')");
        }
    }
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  