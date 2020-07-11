<?php

$registrations = filter_input(INPUT_POST, 'registrations', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!is_array($registrations)) {
    $registrations = [];
}


foreach ($registrations as $competitorId => $registration) {
    $name = strip_tags($registration['name']) ?? FALSE;
    unset($registration['name']);
    if (!is_array($registration) or !$name) {
        $registration = [];
    }

    foreach ($registration as $event_dict => $flag) {
        
        db::exec("UPDATE IGNORE unofficial_competitors SET name = '$name' WHERE id = $competitorId ");

        $round = db::row("SELECT unofficial_events_rounds.id FROM unofficial_events_rounds "
                        . " JOIN unofficial_events ON unofficial_events.id = unofficial_events_rounds.event "
                        . " WHERE unofficial_events.event_dict = $event_dict"
                        . " AND unofficial_events.competition = $comp->id "
                        . " AND round = 1")->id ?? FALSE;

        if ($round and $flag == 'on') {
            db::exec("INSERT IGNORE INTO unofficial_competitors_round "
                    . " (competitor, round) VALUES ($competitorId, $round)");
        }
        if ($round and $flag == 'off') {
            db::exec("DELETE IGNORE FROM unofficial_competitors_round "
                    . " WHERE competitor = $competitorId AND round = $round ");
        }
    }
}

$registrations_delete = filter_input(INPUT_POST, 'registrations_delete', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!is_array($registrations_delete)) {
    $registrations_delete = [];
}
foreach (array_keys($registrations_delete) as $competitorId) {
    
    db::exec("DELETE IGNORE FROM unofficial_competitors_round "
        . " WHERE unofficial_competitors_round.competitor = $competitorId");

    
    db::exec("DELETE IGNORE FROM unofficial_competitors "
            . " WHERE id = $competitorId");
}

/*


$Competitor = GetCompetitorData();
if ($Competitor) {
    CheckPostIsset('Competitor', 'Secret', 'Name', 'Registration');
    CheckPostNotEmpty('Competitor', 'Secret', 'Name');
    CheckPostIsNumeric('Competitor');

    $Competitor_ID = $_POST['Competitor'];
    $Registrations = $_POST['Registration'];
    $Secret = DataBaseClass::Escape($_POST['Secret']);
    $Name = trim(DataBaseClass::Escape(mb_convert_case(mb_strtolower(preg_replace("/\s{2,}/", " ", $_POST['Name'])), MB_CASE_TITLE, "UTF-8")));
    $Name = str_replace("\n", "", $Name);
    DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
    $meeting = DataBaseClass::getRow();
    if (is_array($meeting) and ($meeting['Competitor'] == $Competitor->id or CheckMeetingGrand() or CheckMeetingOrganizer($meeting['ID']))) {
        DataBaseClass::Query("Update `MeetingCompetitor` set name='$Name' where ID=$Competitor_ID and Meeting=" . $meeting['ID']);

        foreach ($Registrations as $MeetingDiscipline_ID => $value) {
            if (is_numeric($MeetingDiscipline_ID)) {

                if ($value == 'on') {
                    DataBaseClass::Query("Select * from `MeetingCompetitorDiscipline` where MeetingCompetitor=$Competitor_ID and MeetingDiscipline=$MeetingDiscipline_ID");
                    $reg = DataBaseClass::getRow();
                    if (!is_array($reg)) {
                        DataBaseClass::Query("Insert into  `MeetingCompetitorDiscipline` (MeetingCompetitor,MeetingDiscipline) values ($Competitor_ID,$MeetingDiscipline_ID)");
                    }
                }
                if ($value == 'off') {
                    DataBaseClass::Query("Delete from `MeetingCompetitorDiscipline` where Place is null and  MeetingCompetitor=$Competitor_ID and MeetingDiscipline=$MeetingDiscipline_ID");
                }
            }
        }
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
*/