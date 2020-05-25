<?php
$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('Competitor','Secret');
    CheckPostNotEmpty('Competitor','Secret');
    CheckPostIsNumeric('Competitor');
    
    $Competitor_ID= $_POST['Competitor'];
    $Secret= DataBaseClass::Escape($_POST['Secret']);
    
    DataBaseClass::Query("Select * from `Meeting` where  Secret='$Secret'");
    $meeting=DataBaseClass::getRow();
    if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand()  or CheckMeetingOrganizer($meeting['ID']))){
        DataBaseClass::Query("Delete from `MeetingCompetitorDiscipline` where MeetingCompetitor=$Competitor_ID and Place is null");
        DataBaseClass::Query("Delete from `MeetingCompetitor` where ID=$Competitor_ID and Meeting=".$meeting['ID']." 
             and ID not in (Select MeetingCompetitor from MeetingCompetitorDiscipline )");
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  