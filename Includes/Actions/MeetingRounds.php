<?php

$Competitor = GetCompetitorData();
if ($Competitor) {
    CheckPostIsset('DisciplineRound', 'DisciplineFormat', 'Secret');
    CheckPostNotEmpty('DisciplineRound', 'DisciplineFormat', 'Secret');


    $DisciplineRounds = $_POST['DisciplineRound'];
    $DisciplineFormat = $_POST['DisciplineFormat'];
    $Secret = DataBaseClass::Escape($_POST['Secret']);

    DataBaseClass::Query("Select * from `Meeting` where  Secret='$Secret'");
    $meeting = DataBaseClass::getRow();
    if (is_array($meeting) and ( $meeting['Competitor'] == $Competitor->id or CheckMeetingGrand())) {

        $disciplines = $_POST['DisciplineRound'];
        foreach ($disciplines as $discipline => $round) {

            DataBaseClass::FromTable('MeetingDisciplineList', 'ID=' . $discipline);
            $DisicplineList = DataBaseClass::QueryGenerate(false);
            if (isset($DisicplineList['MeetingDisciplineList_ID'])) {
                if (isset($_POST['Names'][$DisicplineList['MeetingDisciplineList_ID']])) {
                    $discipline_name = $_POST['Names'][$DisicplineList['MeetingDisciplineList_ID']];
                } else {
                    $discipline_name = $DisicplineList['MeetingDisciplineList_Name'];
                }
                
                if (isset($_POST['DisciplineType'][$DisicplineList['MeetingDisciplineList_ID']])) {
                    if($_POST['DisciplineType'][$DisicplineList['MeetingDisciplineList_ID']]=='amount'){
                        $amount=1;    
                    }
                }else{
                        $amount=0;
                }


                $format = $_POST['DisciplineFormat'][$discipline];
                if (isset($_POST['DisciplineFormat'][$discipline])) {
                    for ($i = 1; $i <= 3; $i++) {
                        if ($i > $round) {
                            DataBaseClass::Query("Select count(*) count from `MeetingDiscipline` MD join MeetingCompetitorDiscipline MCD on MCD.MeetingDiscipline=MD.ID where Meeting=" . $meeting['ID'] . " and MeetingDisciplineList='$discipline' and Round=$i");
                            if (!DataBaseClass::getRow()['count']) {
                                DataBaseClass::Query("Delete from `MeetingDiscipline` where Meeting=" . $meeting['ID'] . " and MeetingDisciplineList='$discipline' and Round=$i");
                            }
                        } else {
                            DataBaseClass::Query("Select count(*) count from `MeetingDiscipline` where Meeting=" . $meeting['ID'] . " and MeetingDisciplineList='$discipline' and Round=$i");
                            if (!DataBaseClass::getRow()['count']) {
                                DataBaseClass::Query("Insert into `MeetingDiscipline` (Meeting,MeetingDisciplineList,Name,Round,MeetingFormat,Amount) values (" . $meeting['ID'] . ",$discipline,'$discipline_name',$i,$format,$amount)");
                            } else {
                                DataBaseClass::Query("Update`MeetingDiscipline` set MeetingFormat=$format, Name='$discipline_name',Amount=$amount where Meeting=" . $meeting['ID'] . " and MeetingDisciplineList='$discipline' and Round=$i");
                            }
                        }
                    }
                }
            }
        }
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
