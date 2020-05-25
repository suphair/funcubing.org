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

