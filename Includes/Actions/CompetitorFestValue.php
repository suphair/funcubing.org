<?php

CheckPostIsset('ID','Secret','DisciplineLocalID');
CheckPostNotEmpty('ID','Secret','DisciplineLocalID');
CheckPostIsNumeric('ID','DisciplineLocalID');
$ID=$_POST['ID'];
$Secret=$_POST['Secret'];
$DisciplineLocalID=$_POST['DisciplineLocalID'];

CheckingScoreTakerCompetitorFest($ID,$Secret);


DataBaseClass::FromTable('Event',"Competition='$ID'");
DataBaseClass::Where('Event', "LocalID='$DisciplineLocalID'");
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join_current('Format');
$event=DataBaseClass::QueryGenerate(false);

SetMessageName('EventLocalID',$event['Event_LocalID']);

for($i=1;$i<=$event['Discipline_Competitors'];$i++){
    if(isset($_POST['CompetitorLocalID'.$i])){
        if(is_numeric($_POST['CompetitorLocalID'.$i])){
            DataBaseClass::FromTable('CompetitorWCA',"Competition='$ID'");
            DataBaseClass::Where('CompetitorWCA', "LocalID='".$_POST['CompetitorLocalID'.$i]."'");
            $competitors[]=DataBaseClass::QueryGenerate(false);
        }
    }
}


if(sizeof($competitors)!=$event['Discipline_Competitors'] or !sizeof($event)){
    SetMessageName('Score','Wrong');
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}
$competitor_names=array();
foreach($competitors as $competitor){
    $name=$competitor['CompetitorWCA_Name'];
    
    $row=DataBaseClass::SelectTableRow('Competitor', "Name='".$name."'");
    if($row['Competitor_ID']){
        DataBaseClass::Query("Update `Competitor` set `WCAID`='".$competitor['CompetitorWCA_WCAID']."',`Country`='".$competitor['CompetitorWCA_Country']."' where ID='".$row['Competitor_ID']."'");
    }else{    
        DataBaseClass::Query("Insert into `Competitor` (`Name`,`WCAID`,`Country`) values ('$name','".$competitor['CompetitorWCA_WCAID']."','".$competitor['CompetitorWCA_Country']."')");
    }
    $competitor_names[]=$competitor['CompetitorWCA_Name'];
    $competitor_countries[]=$competitor['CompetitorWCA_Country'];
}

if(sizeof($competitor_names)>1){
    $name_implode=implode(" & ",$competitor_names);
    $country_implode="";
    $competitor_countries=array_unique($competitor_countries);
    if(sizeof($competitor_countries)==1 and $competitor_countries[0]!=""){
        $country_implode=$competitor_countries[0]; 
    }  
    
    $row=DataBaseClass::SelectTableRow('Competitor', "Name='".$name_implode."'");
    if($row['Competitor_ID']){
        DataBaseClass::Query("Update `Competitor` set `Country`='$country_implode' where ID='".$row['Competitor_ID']."'");
    }else{    
        DataBaseClass::Query("Insert into `Competitor` (`Name`,`Country`) values ('$name_implode','$country_implode')");
    }
    

    $CompetitorEventID=CompetitorEventAdd($name_implode,$event['Event_ID']);        
}else{
    $CompetitorEventID=CompetitorEventAdd($competitor_names[0],$event['Event_ID'],$_POST['CompetitorLocalID1']);
}

$values=array();
for($i=1;$i<=$event['Format_Attemption'];$i++){
    CheckPostIsset("Attempt$i");
    $values[$i]=$_POST["Attempt$i"];
}

SetValueAttemtps($CompetitorEventID,$event['Format_Attemption'],$event['Format_Result'],$event['Format_ExtResult'],$values);

DataBaseClass::Query("Update CompetitorEvent set Decline=0 where ID='$CompetitorEventID'");

Update_Place($event['Event_ID']);

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
