<?php

function CommandUpdateCompetitor($Competitor){
    DataBaseClass::FromTable("CommandCompetitor","Competitor=$Competitor");
    DataBaseClass::Join_current("Command");
    
    foreach(DataBaseClass::QueryGenerate() as $command){
        CommandUpdate('',$command['Command_ID']);
    }
    
    
    
}

function CommandUpdate($Event='',$Command=''){
    DataBaseClass::FromTable("Command");
    if($Command){
        DataBaseClass::Where("Command","ID=$Command");
    }
    if($Event){
        DataBaseClass::Where("Command","Event=$Event");
    }
    
    $commands=array();
    foreach(DataBaseClass::QueryGenerate() as $com){
        $commands[$com['Command_ID']]=1;
    }
    
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Competitor");
    DataBaseClass::OrderClear("Competitor","Name");
    
    
    $dateUpdate=array();
    
    $rows=DataBaseClass::QueryGenerate();

    
    foreach($rows as $row){
        unset($commands[$row['Command_ID']]);
        
        if(!isset($dateUpdate[$row['Command_ID']])){
           $dateUpdate[$row['Command_ID']]['Competitors']=1;
           $dateUpdate[$row['Command_ID']]['Country']=$row['Competitor_Country'];
           $dateUpdate[$row['Command_ID']]['Name']=Short_Name($row['Competitor_Name']);
           $dateUpdate[$row['Command_ID']]['ID']=$row['Competitor_ID'];
           //if($row['Competitor_WCAID']){
           //    $dateUpdate[$row['Command_ID']]['Name'].=' '.substr($row['Competitor_WCAID'],2,2).'/'.substr($row['Competitor_WCAID'],8,2);
           //}
           
        }else{
           $dateUpdate[$row['Command_ID']]['Competitors']++;
           if($dateUpdate[$row['Command_ID']]['Country']!=$row['Competitor_Country']){
                $dateUpdate[$row['Command_ID']]['Country']="";    
           }
           $dateUpdate[$row['Command_ID']]['Name'].=', '.Short_Name($row['Competitor_Name']);
           $dateUpdate[$row['Command_ID']]['ID'].=', '.$row['Competitor_ID'];
           //if($row['Competitor_WCAID']){
           //    $dateUpdate[$row['Command_ID']]['Name'].=' '.substr($row['Competitor_WCAID'],2,2).'/'.substr($row['Competitor_WCAID'],8,2);
           //}
        }
    }
    
    foreach($commands as $commandID=>$tmp){
        DataBaseClass::Query("Delete from Command where ID=".$commandID);
    }
    
   foreach($dateUpdate as $ID=>$data){
       DataBaseClass::Query("Update Command set "
               . " vCompetitors='".$data['Competitors']."',"
               . " vCountry='".$data['Country']."',"
               . " vName='". DataBaseClass::Escape($data['Name'])."',"
               . " vCompetitorIDs='". DataBaseClass::Escape($data['ID'])."'"
               . " where ID=$ID");
   } 
   
}