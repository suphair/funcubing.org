<?php

if (!isset($_POST['FriendWCAID']) or ! $_POST['FriendWCAID']) {
    SetMessageName("AddFriendError", "WCA ID empty");
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

CheckPostIsset('FriendWCAID', 'CompetitorWCAID');
CheckPostNotEmpty('FriendWCAID', 'CompetitorWCAID');

$competitorWCAID = GetCompetitorData()->wca_id;
if ($competitorWCAID != $_POST['CompetitorWCAID']) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
$FriendWCAID = strtoupper(DataBaseClass::Escape($_POST['FriendWCAID']));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/persons/" . $FriendWCAID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($status != 200) {
    SetMessageName("AddFriendError", "Person with WCA ID $FriendWCAID not found");
    SetMessageName("AddFriend_FriendWCAID", $FriendWCAID);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
$wcaid = json_decode($data)->person->wca_id;

if ($wcaid == $competitorWCAID) {
    SetMessageName("AddFriendError", "It is your WCA ID [$FriendWCAID]");
    SetMessageName("AddFriend_FriendWCAID", $FriendWCAID);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/users/" . $FriendWCAID);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($status != 200) {
    SetMessageName("AddFriendError", "User with WCA ID $FriendWCAID not found");
    SetMessageName("AddFriend_FriendWCAID", $FriendWCAID);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

DataBaseClass::Query("Select * from Friend where FriendWCAID='$wcaid' and CompetitorWCAID='$competitorWCAID'");
if (!sizeof(DataBaseClass::getRow())) {
    DataBaseClass::Query("Insert into Friend (FriendWCAID,CompetitorWCAID) values ('$wcaid','$competitorWCAID')");
    SetMessageName("AddFriendMessage", "$FriendWCAID added to friends");
} else {
    SetMessageName("AddFriendMessage", "$FriendWCAID already your friend");
    SetMessageName("AddFriend_FriendWCAID", $FriendWCAID);
}

SetMessage();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();



