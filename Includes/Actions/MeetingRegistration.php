<?php

CheckPostIsset('Disciplines', 'Secret', 'Name', 'SecretRegistration');
CheckPostNotEmpty('Disciplines', 'Secret', 'Name', 'SecretRegistration');

if (isset($_POST['CreateRegistration'])) {
    $Secret = DataBaseClass::Escape($_POST['Secret']);
    $SecretRegistration = DataBaseClass::Escape($_POST['SecretRegistration']);
    $sission_id = session_id();

    DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret' and SecretRegistration='$SecretRegistration'");
    $meeting = DataBaseClass::getRow();

    $Name = DataBaseClass::Escape($_POST['Name']);
    $Disciplines = array();
    foreach ($_POST['Disciplines'] as $discipline => $tmp) {
        if (is_numeric($discipline)) {
            DataBaseClass::Query("Select ID from `MeetingDiscipline` where Round=1 and Meeting=" . $meeting['ID'] . " and ID=" . $discipline);
            if (DataBaseClass::getRow()['ID']) {
                $Disciplines[] = $discipline;
            }
        }
    }

    if (is_array($meeting)) {
        DataBaseClass::Query("Select * from `MeetingCompetitor` where Meeting='" . $meeting['ID'] . "' and Name='$Name' and Session='$sission_id'");
        $MeetingCompetitor_ID = DataBaseClass::getRow()['ID'];
        if (!$MeetingCompetitor_ID) {
            DataBaseClass::Query("Insert into `MeetingCompetitor` ( Meeting,Name,Session) VALUES('" . $meeting['ID'] . "','$Name','" . $sission_id . "')");
            $MeetingCompetitor_ID = DataBaseClass::getID();
        }

        $Discilines_delete = array();
        DataBaseClass::Query("Select MeetingDiscipline from MeetingCompetitorDiscipline where MeetingCompetitor= $MeetingCompetitor_ID and Place is null");
        foreach (DataBaseClass::getRows() as $discipline) {
            $Discilines_delete[$discipline['MeetingDiscipline']] = 1;
        }
        foreach ($Disciplines as $discipline) {

            DataBaseClass::Query("Select ID from MeetingCompetitorDiscipline where MeetingCompetitor= $MeetingCompetitor_ID and MeetingDiscipline=$discipline");
            if (!DataBaseClass::getRow()['ID']) {
                DataBaseClass::Query("Insert into `MeetingCompetitorDiscipline` ( MeetingCompetitor,MeetingDiscipline) VALUES('$MeetingCompetitor_ID','$discipline')");
            }
            unset($Discilines_delete[$discipline]);
        }

        foreach ($Discilines_delete as $discipline => $key) {
            DataBaseClass::Query("Delete from MeetingCompetitorDiscipline where MeetingCompetitor= $MeetingCompetitor_ID and MeetingDiscipline=$discipline");
        }
    }
}
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
