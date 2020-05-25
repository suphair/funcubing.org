<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];

CheckingRoleDelegateEvent($ID);

DataBaseClass::FromTable('CompetitorEvent',"Event=$ID");
DataBaseClass::Join_current('Event');
DataBaseClass::OrderClear('CompetitorEvent', 'ID');
$CompetitorEvents = DataBaseClass::QueryGenerate();
$Groups=$CompetitorEvents[0]['Event_Groups'];
$n=0;
foreach($CompetitorEvents as $ce){
    DataBaseClass::Query("Update CompetitorEvent set `Group`='$n' where ID='".$ce['CompetitorEvent_ID']."' and `Event`='$ID'");
    $n++;
    if($n>=$Groups){
        $n=0;
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();