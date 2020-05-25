<?php
if(isset($_GET['WID']) and is_numeric($_GET['WID'])){
    $WID=$_GET['WID'];
    $user_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/users/".$WID, false); 
    $user=json_decode($user_content);
    if($user){           
        DataBaseClass::Query("Update Competitor set "
       . " Name='". DataBaseClass::Escape($user->user->name)."'"
       . " ,Country='".$user->user->country_iso2."'"
       .($user->user->wca_id?" , WCAID='".$user->user->wca_id."'":"")
       . " where WID=$WID");
        
        DataBaseClass::FromTable("Competitor","WID=$WID");
        DataBaseClass::Join_current("CommandCompetitor");
        DataBaseClass::Join_current("Command");
        foreach(DataBaseClass::QueryGenerate() as $command){
            CommandUpdate('',$command['Command_ID']);
        }       
    }
}


if(isset($_GET['WCAID'])){
    $WCAID= DataBaseClass::Escape($_GET['WCAID']);
    $person_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/persons/".$WCAID, false); 
    $person=json_decode($person_content);
    if($person){           
        DataBaseClass::Query("Update Competitor set "
       . " Name='". DataBaseClass::Escape($person->person->name)."'"
       . " ,Country='".$person->person->country_iso2."'"
       . " where WCAID='$WCAID'");
        
        DataBaseClass::FromTable("Competitor","WCAID='$WCAID'");
        DataBaseClass::Join_current("CommandCompetitor");
        DataBaseClass::Join_current("Command");
        foreach(DataBaseClass::QueryGenerate() as $command){
            CommandUpdate('',$command['Command_ID']);
        }       
    }
}


if(isset($_GET['Country']) and isset($_GET['ID']) and is_numeric($_GET['ID'])){
$ID= $_GET['ID'];
$Country=DataBaseClass::Escape($_GET['Country']);
    DataBaseClass::Query("Update Competitor set "
   . "Country='".strtoupper($Country)."'"
   . " where ID='$ID'");

    DataBaseClass::FromTable("Competitor","ID='$ID'");
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Command");
    foreach(DataBaseClass::QueryGenerate() as $command){
        CommandUpdate('',$command['Command_ID']);
    }       
}

if(isset($_GET['Name']) and isset($_GET['ID']) and is_numeric($_GET['ID'])){
$ID= $_GET['ID'];
$Name=DataBaseClass::Escape($_GET['Name']);
    DataBaseClass::Query("Update Competitor set "
   . "Name='".mb_convert_case($Name, MB_CASE_TITLE, "UTF-8")."'"
   . " where ID='$ID'");

    DataBaseClass::FromTable("Competitor","ID='$ID'");
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Command");
    foreach(DataBaseClass::QueryGenerate() as $command){
        CommandUpdate('',$command['Command_ID']);
    }       
}

if(isset($_GET['UpdateByName']) and isset($_GET['ID']) and is_numeric($_GET['ID'])){
$ID= $_GET['ID'];

DataBaseClass::FromTable("Competitor","ID='$ID'");
$UpdateByName=DataBaseClass::QueryGenerate(false)['Competitor_Name'];

$person_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/search/users/?q=".urlencode($UpdateByName), false); 
    $person=json_decode($person_content);
    if($person and sizeof($person->result)==1 and Short_Name($person->result[0]->name)==Short_Name($UpdateByName)){           
        
        DataBaseClass::Query("Update Competitor set "
       . " WCAID='". $person->result[0]->wca_id."'"
       . " ,Country='".$person->result[0]->country_iso2."'"
       . " ,WID='".$person->result[0]->id."'"
       . " where ID='$ID'");
       
        DataBaseClass::FromTable("Competitor","ID='$ID'");
        DataBaseClass::Join_current("CommandCompetitor");
        DataBaseClass::Join_current("Command");
        foreach(DataBaseClass::QueryGenerate() as $command){
            CommandUpdate('',$command['Command_ID']);
        }       
    }
}



header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  