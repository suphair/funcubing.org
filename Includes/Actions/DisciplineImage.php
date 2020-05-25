<?php

CheckingRoleAdmin();
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];


if(!$_FILES['uploadfile']['error']){
    DataBaseClass::Query("Select Code from `Discipline` where ID='$ID'");
    $Code=DataBaseClass::getRow()['Code'];
    $filename= "./Image/Discipline/$Code.jpg";  
    if($_FILES['uploadfile']['type'] == 'image/jpeg'){     
        copy($_FILES['uploadfile']['tmp_name'],$filename);
    }
} 
SetMessage($_FILES['uploadfile']['error']);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
