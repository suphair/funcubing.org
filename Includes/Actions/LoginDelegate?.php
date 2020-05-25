<?php
if(isset($_POST['Password']) and isset($_POST['Site'])){
    $site= DataBaseClass::Escape($_POST['Site']);
    $password= md5('funcubing'.DataBaseClass::Escape($_POST['Password']));
    
    DataBaseClass::Query("Select ID,Name From Delegate where Site='$site' and Password='$password'");
    if(DataBaseClass::rowsCount()==0){
        SetMessageName("LoginDelegate","Wrong delegate password");
        unset($_SESSION['delegate']);
        unset($_SESSION['delegateID']);
        unset($_SESSION['delegateName']);
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();  
    }else{
        $delegate=DataBaseClass::getRow();
        $_SESSION['delegate']=$password;
        $_SESSION['delegateID']=$delegate['ID'];
        $_SESSION['delegateName']=$delegate['Name'];
        header('Location: '.PageIndex()."Delegate");
        exit(); 
    }

}else{
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}
   