<?php
CheckPostIsset('ID','Description');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
$Description= DataBaseClass::Escape($_POST['Description']);
CheckDelegateCompetition($ID);
$upload_files=[];
foreach($_FILES['FILES']['tmp_name'] as $f=>$file){
    if($_FILES['FILES']['name'][$f]){
        $filename=$ID."_".random_string(10).$_FILES['FILES']['name'][$f];
        move_uploaded_file($file, "Image/MosaicBuilding/$filename");
        $upload_files[]=$filename;
    }
}

DataBaseClass::Query("Insert into MosaicBuilding (Competition, Description) values ($ID,'$Description')");
$MosaicBuildingID= DataBaseClass::getID();

foreach($upload_files as $upload_file){
    DataBaseClass::Query("Insert into MosaicBuildingImage (MosaicBuilding, Filename) values ($MosaicBuildingID,'$upload_file')");    
}

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

