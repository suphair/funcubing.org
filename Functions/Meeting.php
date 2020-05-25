<?php

function MeetingUpdatePlace($Meeting){
    DataBaseClass::FromTable("MeetingCompetitorDiscipline","MeetingDiscipline=".$Meeting);
    DataBaseClass::Join_current("MeetingDiscipline");
    DataBaseClass::Join_current("MeetingFormat");
    $Format=DataBaseClass::QueryGenerate(false)['MeetingFormat_Format'];
    DataBaseClass::OrderClear("MeetingCompetitorDiscipline","MilisecondsOrder");
    
    $resuts=DataBaseClass::QueryGenerate();
    $places=array();
    $p=0;
    $mili=0;
    foreach($resuts as $resut){
        if(!$resut['MeetingCompetitorDiscipline_Attempts']){
            $places[$resut['MeetingCompetitorDiscipline_ID']]='null';
        }else{
            if($mili!=$resut['MeetingCompetitorDiscipline_MilisecondsOrder']){
                $mili=$resut['MeetingCompetitorDiscipline_MilisecondsOrder'];
                $p++;
            }
            $places[$resut['MeetingCompetitorDiscipline_ID']]=$p;
        }
        
    }
    foreach($places as $MeetingCompetitorDiscipline_ID=>$place){
        DataBaseClass::Query("Update MeetingCompetitorDiscipline set Place=$place where ID=$MeetingCompetitorDiscipline_ID");
    }    
}

function CheckMeetingGrand(){
        return ($Competitor=GetCompetitorData() and $Competitor->id==6834);
}

function CheckMeetingOrganizer($Meeting_ID){
    $competitor= GetCompetitorData();
    if(!$competitor or !$competitor->wca_id) return false;
    DataBaseClass::Query("Select * from MeetingOrganizer where Meeting=$Meeting_ID and WCAID='".$competitor->wca_id."'");
    return is_array(DataBaseClass::getRow());
}