<?php
$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('Competitors','Secret');
    CheckPostNotEmpty('Competitors','Secret');

    
    
    $Secret= DataBaseClass::Escape($_POST['Secret']);
    $Competitors= $_POST['Competitors'];
    
    DataBaseClass::Query("Select * from `Meeting` where  Secret='$Secret'");
    $meeting=DataBaseClass::getRow();
    if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand()  or CheckMeetingOrganizer($meeting['ID']))){
            foreach($Competitors as $competitorID=>$tmp){
                DataBaseClass::Query("Select MC.* from `MeetingCompetitor` MC join Meeting M on M.ID=MC.Meeting "
                        . " where  MC.ID='$competitorID' and M.Competitor=".$meeting['Competitor']);
                
                $Name=DataBaseClass::GetRow()['Name'];
                if($Name){
                    DataBaseClass::Query("Select MC.ID from `MeetingCompetitor` MC join Meeting M on M.ID=MC.Meeting "
                        . " where  MC.Name='$Name' and M.ID=".$meeting['ID']);
                    if(!DataBaseClass::GetRow()['ID']){
                            DataBaseClass::Query("Insert into  `MeetingCompetitor` (Meeting,Name) values (".$meeting['ID'].",'".$Name."')");        
                    }
                }
            } 
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  