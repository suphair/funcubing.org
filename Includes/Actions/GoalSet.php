<?php

CheckPostIsset('Data', 'Competitor', 'Competition');
CheckPostNotEmpty('Competitor', 'Competition');
CheckPostIsNumeric('Competitor');

$competitorWID = GetCompetitorData()->id;
if (GetCompetitorData()->id != $_POST['Competitor']) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$competition = DataBaseClass::Escape($_POST['Competition']);
DataBaseClass::Query("Select * from GoalCompetition where WCA='$competition' and NOT Result");
if (!sizeof(DataBaseClass::getRow())) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

foreach ($_POST['Data'] as $event => $dataEvent) {
    foreach ($dataEvent as $format => $dataFormat) {
        if (isset($dataFormat['Goal']) and in_array($format, ['single', 'average'])) {
            if (!isset($dataFormat['Record'])) {
                $record = '';
            } else {
                $record = $dataFormat['Record'];
            }
            $goal = $dataFormat['Goal'];
            $event = DataBaseClass::Escape($event);
            DataBaseClass::FromTable('GoalDiscipline', "Code='$event'");
            $eventID = DataBaseClass::QueryGenerate(false)['GoalDiscipline_ID'];
            if ($eventID) {
                DataBaseClass::FromTable("Goal");
                DataBaseClass::Where("Competition='$competition'");
                DataBaseClass::Where("Discipline='$event'");
                DataBaseClass::Where("Competitor=$competitorWID");
                DataBaseClass::Where("Format='$format'");
                if ($event == '333fm' and $format == 'average' and strlen($goal) == 3) {
                    $goal = substr($goal, 1, 2) . ".00";
                }
                $record_int = GoalResultToInt($record);
                $goal_int = GoalResultToInt($goal);
                $progress = (!$goal_int or ! $record_int) ? '' : ((round(($record_int - $goal_int) / $record_int * 100, 1)) . '%');
                $ID = DataBaseClass::QueryGenerate(false)['Goal_ID'];
                if (!$ID) {
                    if ($goal != '') {
                        DataBaseClass::Query("Insert into Goal"
                                . " (Competition,Discipline,Competitor,Format,Goal,Record,Progress) values"
                                . " ('$competition','$event',$competitorWID,'$format','$goal','$record','$progress') ");
                    }
                } else {
                    if ($goal == '') {
                        DataBaseClass::Query("Delete from Goal where Competition='$competition' and Discipline='$event'"
                                . " and Competitor=$competitorWID and Format='$format'");
                    } else {
                        DataBaseClass::Query("Update Goal Set Goal='$goal',Record='$record',Progress='$progress' "
                                . " where Competition='$competition' and Discipline='$event'"
                                . " and Competitor=$competitorWID and Format='$format' ");
                    }
                }
                
                
                DataBaseClass::Query("
                       REPLACE INTO 
                            GoalCompetitor
                       SET
                            eventCode = '$event',
                            competitionWca = '$competition',
                            competitorWid = '$competitorWID',
                            timeStamp = now()
                ");
            }
        }
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
