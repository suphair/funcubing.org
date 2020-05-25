<?php
CheckPostIsset('ID','Secret');
CheckPostNotEmpty('ID','Secret');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];

CheckingScoreTakerCompetitor($ID,$Secret);

DataBaseClass::Query("Delete from `Attempt` where Command='$ID' ");

DataBaseClass::FromTable("Command","ID=$ID");

if(DataBaseClass::QueryGenerate(false)['Command_Onsite']){
    
    DataBaseClass::FromTable("Command","ID=".$ID);
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current("Competition");
    DataBaseClass::Join("Event","DisciplineFormat");
    DataBaseClass::Join_current("Discipline");
    $data=DataBaseClass::QueryGenerate(false);
    
    AddLog("CompetitionRegistration","Delete/ScoreTaker",$data["Command_vName"].' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);
    
    
    DataBaseClass::Query("Delete from `CommandCompetitor` where Command='$ID' ");
    DataBaseClass::Query("Delete from `Command` where ID='$ID' ");
}else{
    DataBaseClass::Query("Update `Command` set Decline=1,Place=0,Warnings=null where ID='$ID' ");
}
    
DataBaseClass::Query("Select E.ID "
        . " from `Discipline` D "
        . " join `DisciplineFormat` DF on DF.Discipline=D.ID "
        . " join `Event` E on E.DisciplineFormat=DF.ID "
        . " join `Command` Com on Com.Event=E.ID "
        . "where Com.ID='$ID'");

$event=DataBaseClass::getRow()['ID'];

Update_Place($event);

SetMessage(""); 
header('Location: '.$_SERVER['HTTP_REFERER']);

if(isset($_POST['EventLocalID'])){
    SetMessageName('EventLocalID',$_POST['EventLocalID']);
}
exit();  