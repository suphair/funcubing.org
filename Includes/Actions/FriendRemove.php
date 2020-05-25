<?php
CheckPostIsset('FriendWCAID','CompetitorWCAID');
CheckPostNotEmpty('FriendWCAID','CompetitorWCAID');

$competitorWCAID=GetCompetitorData()->wca_id;
if($competitorWCAID!=$_POST['CompetitorWCAID']){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}

$FriendWCAID=DataBaseClass::Escape($_POST['FriendWCAID']);
$CompetitorWCAID=DataBaseClass::Escape($_POST['CompetitorWCAID']);

DataBaseClass::Query("Delete from Friend where FriendWCAID='$FriendWCAID' and CompetitorWCAID='$CompetitorWCAID'");

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 

