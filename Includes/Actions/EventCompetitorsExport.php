<?php

if(!isset($_GET['ID']) or !is_numeric($_GET['ID'])){
    exit();
}

$ID=$_GET['ID'];
  
DataBaseClass::Query("Select ID from  Event where ID='$ID'"); 

if(DataBaseClass::rowsCount()==0){
    exit();    
}

DataBaseClass::Query(" Select C.Name,C.WCAID,CE.CardID from Competitor C "
        . " join CompetitorEvent CE on CE.Competitor=C.ID where CE.Event='$ID' order by Name");

$competitors=DataBaseClass::getRows();

foreach($competitors as $competitor){
    echo $competitor['Name'].";".$competitor['WCAID'].";".$competitor['CardID']."<br>";
}
exit();