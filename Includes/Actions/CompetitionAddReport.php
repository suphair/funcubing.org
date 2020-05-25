<?php
CheckPostIsset('ID','Delegate','Report');
CheckPostNotEmpty('ID','Delegate');
CheckPostIsnumeric('ID','Delegate');

$ID=$_POST['ID'];
$Delegate=$_POST['Delegate'];
$Report= DataBaseClass::Escape($_POST['Report']);

SaveValue("Report.$ID.$Delegate.".time(),$Report);


CheckDelegateCompetition($ID);
if(!CheckAdmin() and $Delegate!= GetDelegateData()['Delegate_ID']){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit(); 
}


DataBaseClass::Query("Select ID from CompetitionReport  where Competition=$ID and Delegate=$Delegate");
$Report_ID=DataBaseClass::getRow()['ID'];
if($Report_ID){
    DataBaseClass::Query("Update `CompetitionReport` set Report='$Report' where Competition=$ID and Delegate=$Delegate");
}else{
    DataBaseClass::Query("insert into  `CompetitionReport` (Report,Competition,Delegate) values ('$Report',$ID,$Delegate)");
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

