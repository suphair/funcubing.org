<?php
CheckPostIsset('ID','Name');
CheckPostNotEmpty('ID','Name');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
$Name=$_POST['Name'];

CheckingRoleDelegate($ID);

$competitorWCA=DataBaseClass::SelectTableRow('CompetitorWCA',"Name='$Name' and Competition='$ID'");
   if(!isset($competitorWCA['CompetitorWCA_ID'])){
       DataBaseClass::Query("select coalesce(max(localID),0) localID from CompetitorWCA where Competition='$ID'");
       $maxLocalID=DataBaseClass::getRow()['localID'];
       $maxLocalID++;
       DataBaseClass::Query("Insert into CompetitorWCA (Name,WCAID,Country,Competition,LocalID) "
       . "values ('$Name','','',$ID,$maxLocalID)");       
   }    

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
