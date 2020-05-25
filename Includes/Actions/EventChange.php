<?php

CheckPostIsset('ID','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors','Groups','Format');
CheckPostNotEmpty('ID','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors','Groups','Format');
CheckPostIsNumeric('ID','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors','Groups','Format');
$ID=$_POST['ID'];
$CutoffMinute=$_POST['CutoffMinute'];
$CutoffSecond=$_POST['CutoffSecond'];
$LimitMinute=$_POST['LimitMinute'];
$LimitSecond=$_POST['LimitSecond'];
$Competitors=$_POST['Competitors'];
$Groups=$_POST['Groups'];
$Format=$_POST['Format'];
    
CheckingRoleDelegateEvent($ID);

$Cumulative=isset($_POST['Cumulative'])?1:0;
    
DataBaseClass::Query("Update `Event` set "
        . " `CutoffMinute`='$CutoffMinute',`CutoffSecond`='$CutoffSecond', "
        . " `LimitMinute`='$LimitMinute',`LimitSecond`='$LimitSecond', "
        . " `Competitors`='$Competitors', Groups='$Groups', "
        . " `Cumulative`='$Cumulative', "
        . " `DisciplineFormat`='$Format' "
        . " where ID='$ID' ");
SetMessage("");
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
