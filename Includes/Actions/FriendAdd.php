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

$data = Suphair \ Wca \ Api::
        getPerson(
                $FriendWCAID, 'actions.FriendAdd', [], false);

if (!isset($data->person->wca_id)) {
    SetMessageName("AddFriendError", "Person with WCA ID $FriendWCAID not found");
    SetMessageName("AddFriend_FriendWCAID", $FriendWCAID);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
$wcaid = $data->person->wca_id;

if ($wcaid == $competitorWCAID) {
    SetMessageName("AddFriendError", "It is your WCA ID [$FriendWCAID]");
    SetMessageName("AddFriend_FriendWCAID", $FriendWCAID);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$data = Suphair \ Wca \ Api::
        getUser(
                $FriendWCAID, 'actions.FriendAdd', [], false);

if (!$data) {
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



