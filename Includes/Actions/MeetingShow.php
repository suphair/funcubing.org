<?php

if(CheckMeetingGrand()){
    CheckPostIsset('Meeting','Action');
    CheckPostNotEmpty('Meeting','Action');
    CheckPostIsNumeric('Meeting');
    $meeting=$_POST['Meeting'];
    if($_POST['Action']=='Show'){
        DataBaseClass::Query("Update `Meeting` set `Show`=1 where ID=$meeting");    
    }
    
    if($_POST['Action']=='Hide'){
        DataBaseClass::Query("Update `Meeting` set `Show`=0 where ID=$meeting");
    }
}    
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

