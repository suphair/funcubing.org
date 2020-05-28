<?php

SaveValue('MeetingConfig_' . time(), print_r($_POST, true));

$Competitor = GetCompetitorData();
if ($Competitor) {

    if ($_POST['Action'] == 'Change') {
        CheckPostIsset('Name', 'Details', 'Secret', 'Action', 'Website', 'Date', 'Show');
        CheckPostNotEmpty('Name', 'Secret', 'Action', 'Date');
        $Website = DataBaseClass::Escape($_POST['Website']);
        $Name = DataBaseClass::Escape($_POST['Name']);
        $Details = DataBaseClass::Escape($_POST['Details']);
        $Secret = DataBaseClass::Escape($_POST['Secret']);
        $Date = date('Y-m-d', strtotime($_POST['Date']));
        $Show = (int) $_POST['Show'];

        if (isset($_POST['Registraton']) or isset($_POST['ShareRegistration'])) {
            $SecretRegistration = "'" . substr(md5($Secret), 0, 10) . "'";
        } else {
            $SecretRegistration = 'null';
        }

        if (isset($_POST['ShareRegistration'])) {
            $ShareRegistration = 1;
        } else {
            $ShareRegistration = 0;
        }

        DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
        $meeting = DataBaseClass::getRow();
        if (is_array($meeting) and ( $meeting['Competitor'] == $Competitor->id or CheckMeetingGrand())) {
            DataBaseClass::Query("
                UPDATE  `Meeting`
                SET 
                    Name = '$Name',
                    Details = '$Details',
                    Date = '$Date',
                    SecretRegistration = $SecretRegistration,
                    ShareRegistration = $ShareRegistration,
                    Website = '$Website',
                    `Show` = $Show
                WHERE ID = {$meeting['ID']}
               ");
        }
    }

    if ($_POST['Action'] == 'Delete') {
        CheckPostIsset('Secret', 'Action');
        CheckPostNotEmpty('Secret', 'Action');
        $Secret = DataBaseClass::Escape($_POST['Secret']);

        DataBaseClass::Query("Select M.* from `Meeting` M"
                . " left outer join MeetingCompetitor MC on MC.Meeting = M.ID"
                . " left outer join MeetingDiscipline MD on MD.Meeting = M.ID"
                . " where  M.Secret='$Secret' and MC.ID is null and MD.ID is null");
        $meeting = DataBaseClass::getRow();

        if (is_array($meeting) and ( $meeting['Competitor'] == $Competitor->id or CheckMeetingGrand())) {
            DataBaseClass::Query("Delete from  `Meeting` where ID=" . $meeting['ID']);
        }
        header('Location: ' . PageIndex() . "?Meetings");
        exit();
    }
}
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();

