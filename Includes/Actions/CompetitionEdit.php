<?php
CheckingRoleAdmin();
CheckPostIsset('Competition','Delegates');
CheckPostNotEmpty('Competition');
CheckPostIsNumeric('Competition');

$delegates=[];
foreach($_POST['Delegates'] as $delegate){
    if(is_numeric($delegate)){
        $delegates[]=$delegate;
    }
}

$Competition=$_POST['Competition'];

DataBaseClass::FromTable("CompetitionDelegate","Competition=".$Competition);
$delegates_table=[];
foreach(DataBaseClass::QueryGenerate() as $delegate_table){
    $delegates_table[]=$delegate_table['CompetitionDelegate_Delegate'];
}

foreach($delegates_table as $delegate_table){
    if(!in_array($delegate_table,$delegates)){
        DataBaseClass::Query("Delete  from `CompetitionDelegate` where Delegate='$delegate_table'  and `Competition`='$Competition'");        
    }
}

foreach($delegates as $delegate){
    if(!in_array($delegate,$delegates_table)){
        DataBaseClass::Query("Insert into  `CompetitionDelegate` (Delegate,Competition) values ('$delegate','$Competition')");        
    }
}

if(!sizeof($delegates)){
    DataBaseClass::Query("Insert into  `CompetitionDelegate` (Delegate,Competition) values ('8','$Competition')");         
}else{
    DataBaseClass::Query("Delete  from `CompetitionDelegate` where Delegate='8'  and `Competition`='$Competition'");        
}




SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
