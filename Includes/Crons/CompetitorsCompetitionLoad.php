<?php

DataBaseClass::FromTable("Competition","StartDate>now()");
$Competitions=DataBaseClass::QueryGenerate();
foreach($Competitions as $Competition){
    $start=date('H:i:s');
    $ID=$Competition['Competition_ID'];
    $registrations_data = file_get_contents(GetIni('WCA_API','competition')."/".$Competition['Competition_WCA']."/registrations", false); 
    $registrations=json_decode($registrations_data);
    if($registrations){    
        foreach($registrations as $registration){
            DataBaseClass::FromTable('Competitor',"WID='".$registration->user_id."'");
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(isset($Competitor['Competitor_ID'])){
                $Competitor_ID=$Competitor['Competitor_ID'];
            }else{
                $user_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/users/".$registration->user_id, false);   
                $user=json_decode($user_content);
                DataBaseClass::Query("Insert into Competitor (Name,Country,WID,WCAID)"
                        . " values('". DataBaseClass::Escape($user->user->name)."',"
                        . "'".$user->user->country_iso2."',"
                        . "'".$user->user->id."',"
                        . "'".$user->user->wca_id."')");
                $Competitor_ID=DataBaseClass::getID();
            }
            DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
        }
    }



    $result_data = file_get_contents(GetIni('WCA_API','competition')."/".$Competition['Competition_WCA']."/competitors", false); 
    $results=json_decode($result_data);
    if($results){    
        foreach($results as $result){
            DataBaseClass::FromTable('Competitor',"WCAID='".$result->wca_id."'");
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(isset($Competitor['Competitor_ID'])){
                $Competitor_ID=$Competitor['Competitor_ID'];
            }else{
                DataBaseClass::Query("Insert into Competitor (Name,Country,WID,WCAID)"
                        . " values('". DataBaseClass::Escape($result->name)."',"
                        . "'".$result->country_iso2."',"
                        . " null ,"
                        . "'".$result->wca_id."')");
                $Competitor_ID=DataBaseClass::getID();
            }

            DataBaseClass::Query("REPLACE into Registration (Competitor,Competition) values ($Competitor_ID,$ID)");
        }
    }

   if(sizeof($results)){
        $str=sizeof($registrations)." / ".sizeof($results);
   }else{
        $str=sizeof($registrations);
   }

   $end=date('H:i:s');
   
    DataBaseClass::Query("Update Competition set LoadDateTime=concat(current_timestamp,' &#9642; '"
                . ",'$str'"
                . ") where ID='$ID'");
    
    AddLog('CompetitorsCompetition', 'CronReload',$Competition['Competition_Name'].' ('.$str.') '.$start.' - '.$end.' ');
}
exit();  