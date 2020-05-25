<?php

CheckPostIsset('Name','ID','WCA_ID','Contact','Secret');
CheckPostNotEmpty('Name','ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];


if(!CheckAdmin() ){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}

$Name= DataBaseClass::Escape($_POST['Name']);
$Secret=DataBaseClass::Escape($_POST['Secret']);
$Contact=DataBaseClass::Escape($_POST['Contact']);
$WCA_ID=$_POST['WCA_ID'];
$Admin=isset($_POST['Admin'])?"1":"0";
$Candidate=isset($_POST['Candidate'])?"1":"0";

DataBaseClass::Query("Update `Delegate` set Name='$Name' , Contact='$Contact' ,Admin='$Admin',Candidate='$Candidate', WCA_ID='$WCA_ID',Secret='$Secret'  where ID='$ID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
