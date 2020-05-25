<?php
if(!isset($_POST['ID']) or !is_numeric($_POST['ID'])){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}
$EventID=$_POST['ID'];

DataBaseClass::FromTable('Event',"ID='$EventID'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('Event','Competition');
$event=DataBaseClass::QueryGenerate(false);
if(count($event)==0){   
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}
$WCA=$event['Competition_WCA'];

$Competitor=GetCompetitorData();

if(!$Competitor){
    SetMessageName("RegistrationError", "WCA not signed in");
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

DataBaseClass::FromTable("Competitor","WID='".$Competitor->id."'");
$competitorID=DataBaseClass::QueryGenerate(false)['Competitor_ID'];

DataBaseClass::Join_current("CommandCompetitor");
DataBaseClass::Join_current("Command");
DataBaseClass::Where("Command","Event='$EventID'");
DataBaseClass::QueryGenerate();
if(DataBaseClass::rowsCount()){ 
   header('Location: '.$_SERVER['HTTP_REFERER']);
   exit();  
}


$result = file_get_contents(GetIni('WCA_API','competition')."/".$event['Competition_WCA']."/registrations", false); 
$registrations=json_decode($result);
$find=false;
foreach($registrations as $registration){
    if($registration->user_id==$Competitor->id){
      $find=true;  
    }
}

if(!$find){
    SetMessageName("RegistrationError", $Competitor->name." has no registrations on WCA");
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();    
}

    DataBaseClass::Query("Select count(*) Count from `Command` where Event='".$event['Event_ID']."' and ".$event['Discipline_Competitors']."=vCompetitors");
    if(DataBaseClass::getRow()['Count']<$event['Event_Competitors']){
        
        if(isset($_POST['Secret']) and $_POST['Secret']){  
            $Secret= strtoupper(trim(DataBaseClass::Escape($_POST['Secret'])));
            DataBaseClass::FromTable('Command',"Event='$EventID'");
            DataBaseClass::Where_current("Secret='$Secret'");

            $commandID=DataBaseClass::QueryGenerate(false)['Command_ID'];


            if(!$commandID){
                SetMessageName("CompetitionRegistrationKey", "Wrong team key");
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit(); 
            }
            $Command=CommandAdd($commandID,$event['Event_ID'],$competitorID);   

            DataBaseClass::FromTable("Command","ID=".$Command);
            DataBaseClass::Join_current("Event");
            DataBaseClass::Join_current("Competition");
            DataBaseClass::Join("Event","DisciplineFormat");
            DataBaseClass::Join_current("Discipline");
            $data=DataBaseClass::QueryGenerate(false);

            AddLog("CompetitionRegistration","Join team",$data["Command_vName"].' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);

        }else{
                $Command=CommandAdd(0,$event['Event_ID'],$competitorID);   

                DataBaseClass::FromTable("Event","ID=".$event['Event_ID']);
                DataBaseClass::Join_current("Competition");
                DataBaseClass::Join("Event","DisciplineFormat");
                DataBaseClass::Join_current("Discipline");
                $data=DataBaseClass::QueryGenerate(false);

                AddLog("CompetitionRegistration",$data["Discipline_Competitors"]>1?"Create team":"Create",$Competitor->name.' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);
        }
    }else{
       SetMessageName("CompetitionRegistrationKey", "limit is reached");
       header('Location: '.$_SERVER['HTTP_REFERER']);
       exit();  
    }     

header('Location: '.$_SERVER['HTTP_REFERER']);
exit(); 
