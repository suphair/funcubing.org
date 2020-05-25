<?php

if(!isset($_GET['ID']) or !is_numeric($_GET['ID'])){
    exit();
}

$ID=$_GET['ID'];
  

DataBaseClass::Query("Select C.WCA, C.Name Competition from  Competition C where C.ID='$ID'"); 

if(DataBaseClass::rowsCount()==0){
    exit();    
}
$competition=DataBaseClass::getrow();
$results=array();


DataBaseClass::Query("Select D.Code Code, D.Name Discipline, C.Name Competition, F.Result, F.Attemption, E.ID Event from `Event` E "
        . " join Competition C on C.ID=E.Competition"
        . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join Discipline D on D.ID=DF.Discipline"
        . " join Format F on F.ID=DF.Format where C.ID='$ID'"); 

$events=DataBaseClass::getRows();
foreach($events as $event){
    
    DataBaseClass::Query(" Select Com.vName, Com.ID from Command Com "
            . "  where Com.Event='".$event['Event']."' order by Place");
    $commands=DataBaseClass::getRows();

    foreach($commands as $command){
        
        DataBaseClass::Query(" Select A.Attempt, A.vOut from Attempt A "
                . " where A.Command='".$command['ID']."' order by A.Attempt");
        $attempts=DataBaseClass::getRows();
        foreach($attempts as $attempt){
            if(is_numeric($attempt['Attempt'])){
                $results[$competition['WCA']][$event['Code']][$command['vName']][]=$attempt['vOut'];
            }
        }
    }
}

echo json_encode($results);
exit();