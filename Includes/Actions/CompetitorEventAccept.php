<?php
CheckPostIsset('ID','Secret');
CheckPostNotEmpty('ID','Secret');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];

CheckingScoreTakerCompetitor($ID,$Secret);

DataBaseClass::Query("Update `Command` set Decline=0 where ID='$ID' ");
    
DataBaseClass::Query("Select E.ID "
        . " from `Discipline` D "
        . " join `DisciplineFormat` DF on DF.Discipline=D.ID "
        . " join `Event` E on E.DisciplineFormat=DF.ID "
        . " join `Command` Com on Com.Event=E.ID "
        . "where Com.ID='$ID'");

$event=DataBaseClass::getRow()['ID'];

Update_Place($event);

SetMessage(); 
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  