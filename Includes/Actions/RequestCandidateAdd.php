<?php

CheckPostIsset('ID','Fields');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
$Fields=array();
foreach($_POST['Fields'] as $field=>$value){
    $Fields[DataBaseClass::Escape($field)]=DataBaseClass::Escape($value);
}
$competitor=GetCompetitorData();
DataBaseClass::FromTable("Competitor","WID=". $competitor->id);
$competitor_row=DataBaseClass::QueryGenerate(false);
if(!$competitor_row['Competitor_ID'] or $ID!=$competitor->id){
    SetMessageName('RequestCandidateAdd','Wrong Competitor ID');
    HeaderExit();  
}

$Competitor_ID=$competitor_row['Competitor_ID'];

DataBaseClass::FromTable("RequestCandidate","Competitor=".$Competitor_ID);
$new=false;

$Request_ID=DataBaseClass::QueryGenerate(false)['RequestCandidate_ID'];
if(!$Request_ID){
    DataBaseClass::Query("Insert Into RequestCandidate (Competitor,Status) values ($Competitor_ID,0)");
    $Request_ID=DataBaseClass::getID();
    $new=true;
}else{
    DataBaseClass::Query("Delete from  RequestCandidateField where RequestCandidate=$Request_ID");
}

    DataBaseClass::Query("Update  RequestCandidate set Datetime=current_timestamp where ID=$Request_ID");

foreach($Fields as $field=>$value){
        DataBaseClass::Query("Insert Into RequestCandidateField (RequestCandidate,Field,Value) values ($Request_ID,'".DataBaseClass::Escape($field)."','".DataBaseClass::Escape($value)."')");
}


if($new){
    SendMail(getini('Seniors','email'), 'New request for join to judges',
            '<pre>'.($competitor->url).'<br>'.$competitor->name.'<hr>'.print_r($Fields,true).'</pre><br>https://funcubing.org/RequestCandidate');
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  