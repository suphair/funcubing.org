<?php
CheckPostIsset('Secret','Competitor','SecretRegistration');
CheckPostNotEmpty('Secret','Competitor','SecretRegistration');
CheckPostIsNumeric('Competitor');
$Secret= DataBaseClass::Escape($_POST['Secret']);
$SecretRegistration= DataBaseClass::Escape($_POST['SecretRegistration']);
$Competitor=$_POST['Competitor'];

DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret' and SecretRegistration='$SecretRegistration'");
$meeting=DataBaseClass::getRow();

if(is_array($meeting)){
    DataBaseClass::Query("Select * from `MeetingCompetitor` where ID=$Competitor and Meeting=".$meeting['ID']);    
    $competitor=DataBaseClass::getRow();
    if(is_array($competitor)){   
        
        DataBaseClass::Query("Select * from `MeetingCompetitorDiscipline` where MeetingCompetitor='$Competitor' and Place is not null");        
        $disciplines=DataBaseClass::getRow();
        if(!is_array($disciplines)){       
            DataBaseClass::Query("Delete from `MeetingCompetitorDiscipline` where MeetingCompetitor='$Competitor'");            
            DataBaseClass::Query("Delete from `MeetingCompetitor` where ID='$Competitor'");            
            
        }   
    }
    
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();
