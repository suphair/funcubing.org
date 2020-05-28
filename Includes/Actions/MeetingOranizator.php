<?php

$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('WCAID','Secret','Action');
    CheckPostNotEmpty('WCAID','Secret','Action');

    if($_POST['Action']=='Add'){
        $WCAID= strtoupper(DataBaseClass::Escape($_POST['WCAID']));
        $Secret= DataBaseClass::Escape($_POST['Secret']);
        
        DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
        $meeting=DataBaseClass::getRow();
        if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand())){
            DataBaseClass::Query("Select * from `MeetingOrganizer` where Meeting='".$meeting['ID']."' and WCAID='$WCAID'");
            if(!is_array(DataBaseClass::getRow())){
                DataBaseClass::Query("Insert into `MeetingOrganizer`(Meeting,WCAID) values ('".$meeting['ID']."','$WCAID')");    
            }
        }
    }
    
    if($_POST['Action']=='Delete'){
        $WCAID= strtoupper(DataBaseClass::Escape($_POST['WCAID']));
        $Secret= DataBaseClass::Escape($_POST['Secret']);
        
        DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
        $meeting=DataBaseClass::getRow();
        if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand())){
            DataBaseClass::Query("Delete from `MeetingOrganizer` where Meeting='".$meeting['ID']."' and WCAID='$WCAID'");
        }
    }
    
    DataBaseClass::Query("UPDATE Meeting SET Organizer  =
        (
        SELECT GROUP_CONCAT(WCAID)
        FROM `MeetingOrganizer`
        WHERE MeetingOrganizer.Meeting = Meeting.ID
        )");    
    
}    
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  