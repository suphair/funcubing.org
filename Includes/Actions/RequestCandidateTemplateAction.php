<?php

CheckingRoleAdmin();

CheckPostIsset('Action');
CheckPostNotEmpty('Action');
$Action=$_POST['Action'];

if($Action=='Изменить'){
    CheckPostIsset('ID','Name','Type','Language');
    CheckPostNotEmpty('ID','Name','Type','Language');
    CheckPostIsNumeric('ID');
    
    $ID=$_POST['ID'];
    $Name=$_POST['Name'];
    $Type=$_POST['Type'];
    $Language= strtoupper($_POST['Language']);
    DataBaseClass::Query("Update RequestCandidateTemplate set Language='$Language', Name='$Name',Type='$Type'  where ID=$ID");    
}

if($Action=='Удалить'){
    CheckPostIsset('ID');
    CheckPostNotEmpty('ID');
    CheckPostIsNumeric('ID');
    
    $ID=$_POST['ID'];
    DataBaseClass::Query("Delete from RequestCandidateTemplate where ID=$ID");     
}


if($Action=='Добавить'){
    CheckPostIsset('Name','Type','Language');
    CheckPostNotEmpty('Name','Type','Language');
    
    $Name=$_POST['Name'];
    $Type=$_POST['Type'];
    DataBaseClass::Query("Insert into RequestCandidateTemplate (Name, Type,Language) values ('$Name','$Type','$Language')");    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
