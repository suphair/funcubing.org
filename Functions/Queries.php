<?php
function getDisciplines($all=true){
    DataBaseClass::Query("Select * from `Discipline` ".(!$all?"":" where Status='Active'")." order by `Code`");
    return DataBaseClass::getRows();
}

function getCompetitions(){
    DataBaseClass::Query("Select * from `Competition` order by `ID` desc");
    return DataBaseClass::getRows();
}

function getCompetitionsDelegate($ID){
    DataBaseClass::Query("Select * from `Competition` where Delegate='$ID' order by `ID` desc");
    return DataBaseClass::getRows();
}


function getStatusCompetition($ID){
        DataBaseClass::Query("Select count(distinct CE.ID) Count from `CompetitorEvent` CE "
                . " join Event E on E.ID=CE.Event "
                . " join Attempt A on A.CompetitorEvent=CE.ID "
                . " where E.Competition='$ID' And A.Special='Best' ");
        return (DataBaseClass::getRow()['Count']>0); 
}

