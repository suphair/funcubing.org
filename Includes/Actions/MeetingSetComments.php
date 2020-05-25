<?php


$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('Comments','Secret');
    CheckPostNotEmpty('Secret');

    $Secret= DataBaseClass::Escape($_POST['Secret']);

    DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
    $meeting=DataBaseClass::getRow();
    if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand())){
        foreach($_POST['Comments'] as $id=>$comment){
             DataBaseClass::Query("Update  `MeetingDiscipline` set Comment='".DataBaseClass::Escape($comment)."' where ID='".DataBaseClass::Escape($id)."' and Meeting=".$meeting['ID']);    
        }
    }    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  