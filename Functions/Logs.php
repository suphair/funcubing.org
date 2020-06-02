<?php
Function AddLog($Object,$Action,$Details){
    if(GetCompetitorData()){
        $CompetitorID=GetCompetitorData()->id;
    }else{
        $CompetitorID=0;
    }
    $Object= DataBaseClass::Escape($Object);
    $Action= DataBaseClass::Escape($Action);
    $Details= DataBaseClass::Escape($Details);
    DataBaseClass::Query("Insert into Logs (Competitor,Object,Action,Details,IP) values"
            . " ($CompetitorID,'$Object','$Action','$Details','".$_SERVER['REMOTE_ADDR']."') ");
    
}

Function AddLogCronStart($cronId, $cronName){
    DataBaseClass::Query("
        INSERT INTO LogsCron 
            (
                cronId,
                cronName
            ) 
        VALUES
            (
                $cronId,
                '$cronName'
            ) 
    ");
    return DataBaseClass::getID();
}

function AddLogCronEnd($cronId,$details){
    DataBaseClass::Query("
        UPDATE LogsCron
        SET
            details = '$details'
        WHERE
            id = $cronId
    ");
}