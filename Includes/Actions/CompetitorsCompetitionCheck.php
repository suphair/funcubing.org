<?php

$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$ID=$request[2];

CheckingRoleDelegate($ID,false);
DataBaseClass::FromTable("Competition","ID=$ID");
$Competition= DataBaseClass::QueryGenerate(false);

$result = file_get_contents(GetIni('WCA_API','competition')."/".$Competition['Competition_WCA']."/registrations", false); 
$registrations=json_decode($result);
if($registrations){    
    $registrations_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/competitions/".$Competition['Competition_WCA']."/registrations", false); 
    $registrations=json_decode($registrations_content);
    
    DataBaseClass::Query("Update CommandCompetitor set CheckStatus=0 where Command in("
            . " Select Com.ID from Command Com join Event E on E.ID=Com.Event where E.Competition='$ID')");
    foreach($registrations as $registration){
        
        DataBaseClass::FromTable("Competitor","WID=".$registration->user_id);
        $competitor=DataBaseClass::QueryGenerate(false);
        if(isset($competitor['Competitor_ID'])){   
            DataBaseClass::Query("Update CommandCompetitor set CheckStatus=1 "
                    . " where Competitor =".$competitor['Competitor_ID']);
        }
    } 
    DataBaseClass::Query("Update Competition set CheckDateTime=current_timestamp where ID='$ID'");
   
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  