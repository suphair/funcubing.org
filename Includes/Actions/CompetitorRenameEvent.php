<?php
CheckPostIsset('Name','ID');
CheckPostNotEmpty('Name','ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Name=trim($_POST['Name']);

DataBaseClass::Query("Select ID from `Competitor` where `Name`='$Name'");
$data=DataBaseClass::getRow();
CheckingRoleDelegateEvent($data['Event']);

if(DataBaseClass::rowsCount()>0){
    $competitorID=DataBaseClass::getRow()['ID'];
}else{
    DataBaseClass::Query("Insert into `Competitor` (`Name`) values ('$Name')");    
    $competitorID=DataBaseClass::getID();
}


DataBaseClass::Query("Update `CompetitorEvent` set Competitor='.$competitorID.' where ID='$ID' ");   

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  


