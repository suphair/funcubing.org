<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
CheckDelegateCompetition($ID);

if(!$_FILES['uploadfile']['error']){
    DataBaseClass::Query("Select WCA from `Competition` where ID='$ID'");
    $WCA=DataBaseClass::getRow()['WCA'];
    $filename= "./Image/Competition/$WCA.jpg";  
    if($_FILES['uploadfile']['type'] == 'image/jpeg'){     
        copy($_FILES['uploadfile']['tmp_name'],$filename);
    }
} 
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
