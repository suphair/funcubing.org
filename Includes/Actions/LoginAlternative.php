<?php
CheckPostIsset('Secret','Secret');
CheckPostNotEmpty('Secret');

$Secret= DataBaseClass::Escape($_POST['Secret']);

DataBaseClass::Query("Select C.ID Local_ID, D.Name, D.WCA_ID, D.WID,C.Country from  Delegate D "
        . " join Competitor C on C.WID=D.WID "
        . " where Status='Active' and Secret<>'' and Secret='$Secret'");
$Delegate=DataBaseClass::getRow();

if(is_array($Delegate)){
    
    
    $competitor = (object)[
        'local_id'=>$Delegate['Local_ID'],
        'name'=>$Delegate['Name'],
        'wca_id'=>$Delegate['WCA_ID'],
        'id'=>$Delegate['WID'],
        'country_iso2'=>$Delegate['Country']
    ];
    $_SESSION['Competitor']=$competitor;
    AddLog('Alternative','Login',$Delegate['Name']);
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  