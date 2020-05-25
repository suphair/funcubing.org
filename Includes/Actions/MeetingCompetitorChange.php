<?php

$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('Competitor','Secret','Name','Registration');
    CheckPostNotEmpty('Competitor','Secret','Name');
    CheckPostIsNumeric('Competitor');
    
    $Competitor_ID= $_POST['Competitor'];
    $Registrations= $_POST['Registration'];
    $Secret= DataBaseClass::Escape($_POST['Secret']);
    $Name= trim(DataBaseClass::Escape(mb_convert_case(mb_strtolower(preg_replace("/\s{2,}/"," ",$_POST['Name'])), MB_CASE_TITLE, "UTF-8")));
    $Name=str_replace("\n","",$Name);
    DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
    $meeting=DataBaseClass::getRow();
    if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand()  or CheckMeetingOrganizer($meeting['ID']))){
        DataBaseClass::Query("Update `MeetingCompetitor` set name='$Name' where ID=$Competitor_ID and Meeting=".$meeting['ID']);
        
        foreach($Registrations as $MeetingDiscipline_ID=>$value){
            if(is_numeric($MeetingDiscipline_ID)){
                
                if($value=='on'){
                    DataBaseClass::Query("Select * from `MeetingCompetitorDiscipline` where MeetingCompetitor=$Competitor_ID and MeetingDiscipline=$MeetingDiscipline_ID");
                    $reg=DataBaseClass::getRow();
                    if(!is_array($reg)){
                        DataBaseClass::Query("Insert into  `MeetingCompetitorDiscipline` (MeetingCompetitor,MeetingDiscipline) values ($Competitor_ID,$MeetingDiscipline_ID)");    
                    }
                }
                if($value=='off'){
                    DataBaseClass::Query("Delete from `MeetingCompetitorDiscipline` where Place is null and  MeetingCompetitor=$Competitor_ID and MeetingDiscipline=$MeetingDiscipline_ID");
                }
                
                
            }
        }
        
    }    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  