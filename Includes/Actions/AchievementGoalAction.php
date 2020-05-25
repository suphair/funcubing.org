<?php


CheckPostIsset('Action','GroupID','AchievementID','AchievementGoalID');
CheckPostNotEmpty('Action','GroupID','AchievementGoalID');
CheckPostIsnumeric('GroupID','AchievementID','AchievementGoalID');

if(!CheckAchievementGrand()){
   header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}



$Action=$_POST['Action'];
$GroupID=$_POST['GroupID'];
$AchievementID=$_POST['AchievementID'];
$AchievementGoalID=$_POST['AchievementGoalID'];

if(isset($_POST['Event'])){
    $EventCode=DataBaseClass::Escape($_POST['Event']);
    DataBaseClass::Query("Select count(*) count from AchievementEvent where EventCode='$EventCode'");
    $eventExists=DataBaseClass::getRow()['count']>0;
}

if(isset($_POST['Condition'])){
   
    $ConditionIsJson=isJson($_POST['Condition']);
    $Condition= DataBaseClass::Escape($_POST['Condition']);
    if(!$Condition){
        $ConditionIsJson=true;
    }
    if(strpos($Condition,"{")===false and $Condition){
       $ms=explode(":",$Condition);
       if(sizeof($ms)==1){
           $m=0;
           $ss=explode(".",$ms[0]);    
       }else{
           $m=$ms[0];
           $ss=explode(".",$ms[1]);    
       }
       $Condition='{"value":"'.($m*60*100+$ss[0]*100+$ss[1]).'"}';
       $ConditionIsJson=true;
    }
    
    if(!$ConditionIsJson){
        SetMessageName('AchievementGoalConditionError_'.$AchievementGoalID, $_POST['Condition']);
    }
}

if($Action=='+' and $AchievementGoalID==0 
        and isset($_POST['Result']) and in_array($_POST['Result'],['average','single'])
        and isset($_POST['Event']) and $eventExists){
    $Result=$_POST['Result'];
    DataBaseClass::Query("INSERT INTO AchievementGoal (`Result`,`Event`,`Achievement`) values ('$Result','$EventCode',$AchievementID)");
    $AchievementGoalID= DataBaseClass::getID();
}


if($Action=='>' 
        and isset($_POST['Result']) and in_array($_POST['Result'],['average','single'])
        and isset($_POST['Event']) and $eventExists
        and isset($_POST['Condition']) and $ConditionIsJson){
    $Result=$_POST['Result'];
    DataBaseClass::Query("Update AchievementGoal set `Result`='$Result',`Event`='$EventCode',`Condition`='$Condition'  where ID=$AchievementGoalID and `Achievement`=$AchievementID");
}

if($Action=='-'){
    DataBaseClass::Query("Delete from AchievementGoal where ID=$AchievementGoalID and `Achievement`=$AchievementID");
}

SetMessageName('AchievementGoalID', $AchievementGoalID);
SetMessageName('AchievementGroupID', $GroupID);
SetMessageName('AchievementID', $AchievementID);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

