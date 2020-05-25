<?php


CheckPostIsset('Action','GroupID','AchievementID');
CheckPostNotEmpty('Action','GroupID');
CheckPostIsnumeric('GroupID','AchievementID');

if(!CheckAchievementGrand()){
   header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}

$Action=$_POST['Action'];
$GroupID=$_POST['GroupID'];
$AchievementID=$_POST['AchievementID'];

if(isset($_POST['Total'])){
    $TotalIsJson=isJson($_POST['Total']);
    $Total= DataBaseClass::Escape($_POST['Total']);
    if(!$TotalIsJson){
        SetMessageName('AchievementTotalError_'.$AchievementID, $_POST['Total']);
    }
}

    
if($Action=='++' and $AchievementID==0 and isset($_POST['Name']) and $_POST['Name']){
    $Name= DataBaseClass::Escape($_POST['Name']);
    DataBaseClass::Query("INSERT INTO Achievement (`Name`,`Group`,`Rank`) values ('$Name',$GroupID,1)");
    $AchievementID= DataBaseClass::getID();
}

if($Action=='>>' 
        and isset($_POST['Name']) and $_POST['Name'] 
        and isset($_POST['Total']) and $TotalIsJson 
        and isset($_POST['Rank']) and is_numeric($_POST['Rank'])){
    $Name= DataBaseClass::Escape($_POST['Name']);
    $Rank= $_POST['Rank'];
    DataBaseClass::Query("Update Achievement set `Name`='$Name',`Rank`=$Rank,`Total`='$Total'  where ID=$AchievementID and `Group`=$GroupID");
}

if($Action=='--'){
    DataBaseClass::Query("Delete from Achievement where ID=$AchievementID and `Group`=$GroupID");
}

SetMessageName('AchievementID', $AchievementID);

if($Action=='**'){
    DataBaseClass::Query("Select * from Achievement where ID=$AchievementID and `Group`=$GroupID");
    $Achievement=DataBaseClass::getRow();
    
    if($Achievement){
        DataBaseClass::Query("INSERT INTO Achievement (`Name`,`Group`,`Rank`,`Total`) values ('".$Achievement['Name']."_','".$Achievement['Group']."','".$Achievement['Rank']."','".$Achievement['Total']."')");
        $NewAchievementID=DataBaseClass::getID();
        
        DataBaseClass::Query("Select * from AchievementGoal where Achievement=$AchievementID");
        $Goals=DataBaseClass::getRows();
        foreach($Goals as $Goal){
            DataBaseClass::Query("INSERT INTO AchievementGoal (`Result`,`Event`,`Achievement`,`Condition`) values ('".$Goal['Result']."','".$Goal['Event']."',$NewAchievementID,'".$Goal['Condition']."')");
        }
        
    }
    
SetMessageName('AchievementID', $NewAchievementID);
}
    
SetMessageName('AchievementGroupID', $GroupID);


header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

