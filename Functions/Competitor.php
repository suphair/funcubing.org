<?php

function Group_Name($n) {
    $Group_Name = array(-1 => "", "A", "B", "C", "D", "E", "F");
    return $Group_Name[$n];
}

function Groups_Name($n) {
    $groups = array();
    for ($i = 0; $i < $n; $i++) {
        $groups[] = Group_Name($i);
    }
    return implode(", ", $groups) . ".";
}

function CommandDeleter() {
    DataBaseClass::Query("Delete from Command where ID not in (Select Command from CommandCompetitor)");
}

function competitorActual($wcaid, $wid, $name, $country) {
    
    if($wcaid){
       DataBaseClass::FromTable("Competitor", "WCAID='$wcaid'");
        DataBaseClass::QueryGenerate();
        if (DataBaseClass::rowsCount() == 1) {
            DataBaseClass::Query("Update Competitor set Name='$name', WID=$wid,Country='$country' where WCAID='$wcaid'");
        } else {
            DataBaseClass::Query("Insert Into Competitor (WCAID,WID,Country,Name) values ('$wcaid',$wid,'$country','$name')");
        }
    }
    
    if($wid){
        DataBaseClass::FromTable("Competitor", "WID='$wid'");
        DataBaseClass::QueryGenerate();
        if (DataBaseClass::rowsCount() == 1) {
            DataBaseClass::Query("Update Competitor set Name='$name', WCAID='$wcaid',Country='$country' where WID='$wid'");
        } else {
            DataBaseClass::Query("Insert Into Competitor (WCAID,WID,Country,Name) values ('$wcaid',$wid,'$country','$name')");
        }  
    }
    
    Competitors_RemoveDuplicates();
    
}

function Competitors_RemoveDuplicates(){
    DataBaseClass::Query("
        select count(*),WID from Competitor
        where WID is not null
        group by WID
        having count(*)>1
    ");

    foreach(DataBaseClass::getRows() as $Double){
        $WID = $Double['WID'];
        DataBaseClass::Query("
            select ID from Competitor
            where WID = '$WID'
            order by ID desc
        ");
        $Competitors = DataBaseClass::getRows();

        $ID=$Competitors[0]['ID']; 
        foreach($Competitors as $Competitor){
            if($ID!=$Competitor['ID']){
                DataBaseClass::Query("Update CommandCompetitor set Competitor=$ID where Competitor=".$Competitor['ID']);
                DataBaseClass::Query("Delete from Competitor where ID=".$Competitor['ID']);
            }

        }
    }
}