<?php

CheckPostIsset('RequestCandidate','RequestCandidateAction');
CheckPostNotEmpty('RequestCandidate','RequestCandidateAction');
CheckPostIsNumeric('RequestCandidate');
$RequestCandidate=$_POST['RequestCandidate'];
$RequestCandidateAction=$_POST['RequestCandidateAction'];


CheckingRoleAdmin();

DataBaseClass::FromTable("RequestCandidate","ID=$RequestCandidate");
DataBaseClass::Where_current("Status=0");
DataBaseClass::Join_current("Competitor");
$data=DataBaseClass::QueryGenerate(false);

if($RequestCandidateAction=='Отказать' and $data['RequestCandidate_ID']){
    DataBaseClass::Query("Update RequestCandidate set Status=-1 where ID=$RequestCandidate");    
}

if($RequestCandidateAction=='Принять' and $data['RequestCandidate_ID']){
    DataBaseClass::Query("Update RequestCandidate set Status=1 where ID=$RequestCandidate"); 
    
    DataBaseClass::Query("Insert into Delegate(`Name`,`WCA_ID`,`Admin`,`Status`,`Candidate`,`WID`) values"
            . " ('".$data['Competitor_Name']."','".$data['Competitor_WCAID']."',0,'Active',1,".$data['Competitor_WID'].")");    
    
}


header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
