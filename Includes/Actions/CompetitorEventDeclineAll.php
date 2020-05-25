<?php
CheckPostIsset('ID','Secret');
CheckPostNotEmpty('ID','Secret');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];

CheckingScoreTakerEvent($ID,$Secret);

DataBaseClass::Query("Select Com.* from Command Com where Event='$ID' "
        . " and not exists (select * from Attempt A where A.Command=Com.ID and A.Attempt is not null) ");

foreach(DataBaseClass::getRows() as $command){
    
    if($command['Onsite']){
        DataBaseClass::Query("Delete from Attempt where Command=".$command['ID']." and Attempt is null");

        DataBaseClass::FromTable("Command","ID=".$command['ID']);
        DataBaseClass::Join_current("Event");
        DataBaseClass::Join_current("Competition");
        DataBaseClass::Join("Event","DisciplineFormat");
        DataBaseClass::Join_current("Discipline");
        $data=DataBaseClass::QueryGenerate(false);

        AddLog("CompetitionRegistration","Delete/ScoreTaker/All",$data["Command_vName"].' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);     
        DataBaseClass::Query("Delete from `CommandCompetitor` where Command=".$command['ID']);
        DataBaseClass::Query("Delete from Command where ID=".$command['ID']);
    }else{
        DataBaseClass::Query("Update `Command` Com set Decline=1,Place=0,Warnings=null where Com.ID='".$command['ID']."' "
        . " and not exists (select * from Attempt A where A.Command=Com.ID) ");
    }
    
    
}

Update_Place($ID);

SetMessage(""); 
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  