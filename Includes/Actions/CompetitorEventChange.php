<?php
CheckPostIsset('ID','Name');
CheckPostNotEmpty('ID','Name');
CheckPostIsNumeric('ID');
$CompetitorEvent_ID=DataBaseClass::Escape($_POST['ID']);
$Competitor_Name=DataBaseClass::Escape($_POST['Name']);

if(strpos($Competitor_Name,"&")!==FALSE){
    $name_tmp="";
    foreach(explode("&",$Competitor_Name) as $name){ 
        $name_tmp.=trim($name)." & ";
    }
    $Competitor_Name= substr($name_tmp, 0,-3);
}



DataBaseClass::FromTable('CompetitorEvent',"ID='".$CompetitorEvent_ID."'");
DataBaseClass::Join_current('Competitor');
DataBaseClass::Join('CompetitorEvent','Event');

$CompetitorEvent=DataBaseClass::QueryGenerate(false);
CheckingRoleDelegateEvent($CompetitorEvent['Event_ID']);

$CompetitorNewID=CompetitorGet($Competitor_Name);

DataBaseClass::Query("Update `CompetitorEvent` set Competitor='$CompetitorNewID',CardID=0 where ID='$CompetitorEvent_ID' ");  
CompetitorEventAdd($Competitor_Name,$CompetitorEvent['Event_ID']);

CompetitorDelete($CompetitorEvent['Competitor_Name']);

if(strpos($Competitor_Name,"&")!==FALSE){
    foreach(explode("&",$Competitor_Name) as $name){ 
        CompetitorGet(trim($name));
    }
}

if(strpos($CompetitorEvent['Competitor_Name'],"&")!==FALSE){
    foreach(explode("&",$CompetitorEvent['Competitor_Name']) as $name){ 
        CompetitorDelete(trim($name));
    }
}
                            

SetMessage(); 
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  