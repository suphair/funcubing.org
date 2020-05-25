<?php

CheckPostIsset('ID','Competitors');
CheckPostNotEmpty('ID','Competitors');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];
$Competitors=array();
foreach($_POST['Competitors'] as $competitior){
    if(!is_numeric($competitior)){
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();  
    }
    
    $Competitors[]=$competitior;
}

CheckingRoleDelegateEvent($ID,false);

DataBaseClass::FromTable("Event","ID=$ID");
DataBaseClass::Join_current("DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$date= DataBaseClass::QueryGenerate(false);
if(sizeof($Competitors)<>$date['Discipline_Competitors']){
    SetMessageName("CompetitorEventAddError","Required to select ".html_spellcount($date['Discipline_Competitors'],'competitor','competitors','competitors'));
    SetMessageName("CompetitorsEventAdd",$_POST['Competitors']);
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();  
}


$errors=array();
foreach($Competitors as $competitior){
    DataBaseClass::Query("Select C.* from Command Com "
    . " join CommandCompetitor CC on CC.Command=Com.ID"
    . " join Competitor C on CC.Competitor=C.ID"
    . " where Com.Event=$ID and C.ID=$competitior");
    $competitor=DataBaseClass::getRow();
    if(isset($competitor['ID'])){
        $errors[]=$competitor['Name'].' / '.$competitor['WCAID'];   
    }
}

if(sizeof($errors)){
    SetMessageName("CompetitorEventAddError","Competitor already registered: ".implode(",<br>",$errors));
    SetMessageName("CompetitorsEventAdd",$_POST['Competitors']);
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();   
}

$command=0;
foreach($Competitors as $competitior){
    $command=CommandAdd($command,$ID,$competitior);   
}

DataBaseClass::FromTable("Command","ID=".$command);
DataBaseClass::Join_current("Event");
DataBaseClass::Join_current("Competition");
DataBaseClass::Join("Event","DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$data=DataBaseClass::QueryGenerate(false);
AddLog("CompetitionRegistration","Create/Judge",$data["Command_vName"].' / '.$data["Competition_WCA"].' / '.$data["Discipline_Code"]);



SetMessageName("CompetitorEventAddMessage","Registered");

SetMessage();    
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  
