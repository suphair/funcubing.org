<?php

CheckPostIsset('ID','MosaicBuilding','Action');
CheckPostNotEmpty('ID','MosaicBuilding','Action');
CheckPostIsNumeric('ID','MosaicBuilding');

$ID=$_POST['ID'];
$MosaicBuilding=$_POST['MosaicBuilding'];
CheckDelegateCompetition($ID);

if($_POST['Action']=='Delete'){
    DataBaseClass::Query("Delete from MosaicBuildingImage where MosaicBuilding=$MosaicBuilding");
    DataBaseClass::Query("Delete from  MosaicBuilding where ID=$MosaicBuilding");    
}

if($_POST['Action']=='Save'){
    CheckPostIsset('Description');
    $Description= DataBaseClass::Escape($_POST['Description']);
    DataBaseClass::Query("Update MosaicBuilding set Description='$Description' where ID=$MosaicBuilding");
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

