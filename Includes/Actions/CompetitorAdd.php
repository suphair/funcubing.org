<?php

CheckPostIsset('WCAID','Event');
CheckPostNotEmpty('WCAID','Event');
CheckPostIsNumeric('Event');

$WCAID= DataBaseClass::Escape($_POST['WCAID']);
$Event=$_POST['Event'];

if(!GetDelegateData()){
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();  
}
$Name='';


DataBaseClass::FromTable("Event","ID='".$Event."'");
DataBaseClass::Join_current("DisciplineFormat");
DataBaseClass::Join_current("Discipline");
$competitors=DataBaseClass::QueryGenerate(false)['Discipline_Competitors'];

DataBaseClass::FromTable('Competitor',"WCAID='".$WCAID."'");

if(DataBaseClass::QueryGenerate(false)){
    
    SetMessageName("CompetitorAddError",$WCAID." already exists");    
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();  
}

$result=@file_get_contents(GetIni('WCA_API','person')."/$WCAID");
$person=json_decode($result);
if(!$person){
    SetMessageName("CompetitorAddError",$WCAID." not found");    
    header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
    exit();  
}

$name=$person->person->name;
$wcaid=$person->person->wca_id;
$country=$person->person->country_iso2;

DataBaseClass::FromTable("Competitor","Name='".Short_Name($name)."'");  

if(DataBaseClass::rowsCount()){
    $competitorID=DataBaseClass::QueryGenerate()['Competitor_ID'];
    DataBaseClass::Query("Update Competitor set WCAID='$wcaid',Country='$country' where Name='$name'");    
}else{
    DataBaseClass::Query("Insert Into Competitor (WCAID,Country,Name) values ('$wcaid','$country','$name')");    
    $competitorID=DataBaseClass::getID();
} 

SetMessageName("CompetitorAddMessage",$name.' '.$wcaid." Added");


if($competitors==1){
    CommandAdd(0,$Event,$competitorID);   
}


SetMessage();    
header('Location: '.$_SERVER['HTTP_REFERER'].'#CompetitorEventAdd');
exit();  
