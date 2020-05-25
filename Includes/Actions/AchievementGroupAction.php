<?php

CheckPostIsset('Action','GroupID');
CheckPostNotEmpty('Action');
CheckPostIsnumeric('GroupID');

if(!CheckAchievementGrand()){
   header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

$Action=$_POST['Action'];
$GroupID=$_POST['GroupID'];
    
if($Action=='+++' and $GroupID==0 and isset($_POST['GroupName']) and $_POST['GroupName']){
    $GroupName= DataBaseClass::Escape($_POST['GroupName']);
    DataBaseClass::Query("INSERT INTO AchievementGroup (GroupName) values ('$GroupName')");
    $GroupID= DataBaseClass::getID();
}

if($Action=='>>>' and isset($_POST['GroupName']) and $_POST['GroupName']){
    $GroupName= DataBaseClass::Escape($_POST['GroupName']);
    DataBaseClass::Query("Update AchievementGroup set GroupName='$GroupName' where ID=$GroupID ");
}

if($Action=='---'){
    DataBaseClass::Query("Delete from AchievementGroup where ID=$GroupID");
}

SetMessageName('AchievementGroupID', $GroupID);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

