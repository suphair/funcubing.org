<?php

CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];;

CheckingRoleDelegateEvent($ID);

DataBaseClass::Query("select Com.ID, Com.vName, Com.Decline, count(A.ID) Attempt  "
        . " from Command Com"
        . " left outer join Attempt A on A.Command=Com.ID"
        . " where Com.Event=".$ID.""
        . " group by Com.ID"
        );

$deleter=true;
$deleter_IDs=array();
foreach(DataBaseClass::getRows() as $row){
    if(!$row['Attempt'] and !$row['Decline'] ){
        $deleter=false;  
    }else{
        if($row['Decline']){ 
            $deleter_IDs[]=$row['ID'];
        }
    }
}

if($deleter){
    foreach($deleter_IDs as $deleter_ID){
        DataBaseClass::Query("Delete from CommandCompetitor where Command=".$deleter_ID);
        DataBaseClass::Query("Delete from Command where ID=".$deleter_ID);
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();