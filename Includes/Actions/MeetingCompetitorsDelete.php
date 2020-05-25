<?php
$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('Secret');
    CheckPostNotEmpty('Secret');
    
    $Secret= DataBaseClass::Escape($_POST['Secret']);
    
    DataBaseClass::Query("Select * from `Meeting` where  Secret='$Secret'");
    $meeting=DataBaseClass::getRow();
    if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand())){
        DataBaseClass::Query("Delete from `MeetingCompetitorDiscipline` where Place is null and MeetingCompetitor in(select ID from  `MeetingCompetitor` where Meeting=".$meeting['ID'].")");
        DataBaseClass::Query("Delete from `MeetingCompetitor` where Meeting=".$meeting['ID']." and ID not in (Select MeetingCompetitor from MeetingCompetitorDiscipline ) ");
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  