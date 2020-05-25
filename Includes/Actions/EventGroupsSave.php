<?php
CheckPostIsset('ID','Command_ID');
CheckPostNotEmpty('ID','Command_ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
$Commands=$_POST['Command_ID'];

CheckingRoleDelegateEvent($ID,false);

foreach($Commands as $ce_id=>$g){
    if(is_numeric($ce_id) and is_numeric($g)){
        if($g>=-1){
            DataBaseClass::Query("Update Command set `Group`='$g' where ID='$ce_id' and `Event`='$ID'");
        }
    }   
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();