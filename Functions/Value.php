<?php

function SaveValue($name,$value){
    $name=DataBaseClass::Escape($name);
    $value=DataBaseClass::Escape($value);
    DataBaseClass::Query("Delete from Value where Name='$name'");
    DataBaseClass::Query("Insert into Value (Name,Value,Timestamp) values ('$name','$value',now())");
    
}

function GetValue($name,$timeFl=false){
    $name=DataBaseClass::Escape($name);
    if($timeFl){
        DataBaseClass::Query("Select Value from Value where Name='$name' and TIMESTAMPDIFF(MINUTE,Timestamp,now())<60");
    }else{
        DataBaseClass::Query("Select Value from Value where Name='$name'");
    }
    $value=false;
    $row=DataBaseClass::getRow();
    if(is_array($row)){
        $value=$row['Value'];
    }
    return $value;
}

