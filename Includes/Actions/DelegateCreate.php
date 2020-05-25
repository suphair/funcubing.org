<?php
if(CheckAdmin()){
    CheckPostIsset('WCAID');
    CheckPostNotEmpty('WCAID');

    $wca_id= strtoupper(DataBaseClass::Escape($_POST['WCAID']));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/persons/".$wca_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($status==200){
        $person=json_decode($data,true)['person'];
        $name=$person['name'];
        DataBaseClass::Query("Insert into `Delegate` (Name, WCA_ID, Status,Admin,Candidate) values ('$name','$wca_id','Active','0','1')");
        header('Location: '.PageIndex().'Delegate/'.$wca_id."/config");
        exit();  

    }    
}
SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
