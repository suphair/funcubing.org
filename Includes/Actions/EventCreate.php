<?php

CheckPostIsset('Competition','DisciplineFormat','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors');
CheckPostNotEmpty('Competition','DisciplineFormat','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors');
CheckPostIsNumeric('Competition','DisciplineFormat','CutoffMinute','CutoffSecond','LimitMinute','LimitSecond','Competitors');
$Competition=$_POST['Competition'];
$DisciplineFormat=$_POST['DisciplineFormat'];
$CutoffMinute=$_POST['CutoffMinute'];
$CutoffSecond=$_POST['CutoffSecond'];
$LimitMinute=$_POST['LimitMinute'];
$LimitSecond=$_POST['LimitSecond'];
$Competitors=$_POST['Competitors'];
$Groups=$_POST['Groups'];

$Cumulative=isset($_POST['Cumulative'])?1:0;

CheckingRoleDelegate($Competition);


DataBaseClass::FromTable("DisciplineFormat","ID=".$DisciplineFormat);
$Discipline=DataBaseClass::QueryGenerate(false)["DisciplineFormat_Discipline"];

DataBaseClass::FromTable("Discipline","ID=".$Discipline);
DataBaseClass::Join_current("DisciplineFormat");
DataBaseClass::Join_current("Event");
DataBaseClass::Where_current("Competition=".$Competition);
DataBaseClass::Select("count(distinct E.ID) count");

$round=DataBaseClass::QueryGenerate(false)['count']+1;

if($round>=5){
    SetMessage("Exists");
}                



DataBaseClass::Query("Select E.ID from `Event` E "
        . "join DisciplineFormat DF on E.DisciplineFormat=DF.ID where"
        . " E.`Competition`='$Competition' and DF.Discipline='$Discipline' and E.Round=$round");
        
if(DataBaseClass::rowsCount()>0){
    SetMessage("Exists");
    $EventID=DataBaseClass::getRow()['ID'];
}else{
    DataBaseClass::Query("Insert into  `Event` (`Competition`,`DisciplineFormat`,`CutoffMinute`,`CutoffSecond`,`LimitMinute`,`LimitSecond`,`Competitors`,`Groups`,`Secret`,`Round`,`Cumulative`)"
            . " VALUES('$Competition','$DisciplineFormat','$CutoffMinute','$CutoffSecond','$LimitMinute','$LimitSecond','$Competitors','$Groups','". random_string(16)."','$round','$Cumulative')");
    EventRoundView($Competition);
    SetMessage();
    $EventID=DataBaseClass::getID();
}

UpdateLocalID($Competition);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
