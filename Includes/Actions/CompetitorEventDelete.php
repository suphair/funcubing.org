<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];


DataBaseClass::FromTable('Command');
DataBaseClass::Where_current("ID='$ID'");
DataBaseClass::Join_current('Event');
DataBaseClass::Join('Command','CommandCompetitor');
DataBaseClass::Join_current('Competitor');
$Competitor=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join('Command','Attempt');
$attempt=DataBaseClass::QueryGenerate();
if(sizeof($attempt)){
    SetMessageName("CompetitorEventAddError","Attempts have already been made");
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();     
}

CheckingRoleDelegate($Competitor['Event_Competition'],false);  


DataBaseClass::FromTable("Command","ID=".$ID);
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current("Competition");
DataBaseClass::Join("Event","DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$data=DataBaseClass::QueryGenerate(false);
AddLog("CompetitionRegistration","Delete/Judge",$data["Command_vName"].' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);



DataBaseClass::Query("Delete from `CommandCompetitor` where Command='$ID' ");
DataBaseClass::Query("Delete from `Command` where ID='$ID' ");


SetMessage(); 
    
SetMessageName("CompetitorEventAddMessage",$Competitor['Command_vName']." Deleted");
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  