<?php    

CheckPostIsset('FriendWCAID','CompetitorWCAID');
CheckPostNotEmpty('FriendWCAID','CompetitorWCAID');

$competitorWCAID=GetCompetitorData()->wca_id;
if($competitorWCAID!=$_POST['CompetitorWCAID']){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}
$FriendWCAID=DataBaseClass::Escape($_POST['FriendWCAID']);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/persons/".$FriendWCAID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
$status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($status!=200){
    SetMessageName("AddFriend", "Wrong friend's WCAID");
    SetMessageName("AddFriend_FriendWCAID",$FriendWCAID);     
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}
$wcaid=json_decode($data)->person->wca_id;        
        
if($wcaid==$competitorWCAID){
    SetMessageName("AddFriend", "It is your WCAID");
    SetMessageName("AddFriend_FriendWCAID",$FriendWCAID);     
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/users/".$FriendWCAID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
$status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($status!=200){
    SetMessageName("AddFriend", "Wrong friend's WCAID");
    SetMessageName("AddFriend_FriendWCAID",$FriendWCAID);    
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

DataBaseClass::Query("Select * from Friend where FriendWCAID='$wcaid' and CompetitorWCAID='$competitorWCAID'");
if(!sizeof(DataBaseClass::getRow())){
    DataBaseClass::Query("Insert into Friend (FriendWCAID,CompetitorWCAID) values ('$wcaid','$competitorWCAID')");
}

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 



