<?php

$delegateData= GetDelegateData();
if(!$delegateData or $delegateData['Delegate_Candidate'] or $delegateData['Delegate_Status']!='Active'){
    HeaderExit();  
}


if($delegateData['Delegate_Admin']){
    CheckPostIsset('WCA','Delegate');
    CheckPostNotEmpty('WCA','Delegate');
    CheckPostIsNumeric('Delegate');
    $WCA=$_POST['WCA'];
    $Delegate=$_POST['Delegate'];
}else{
    CheckPostIsset('WCA');
    CheckPostNotEmpty('WCA');
    $WCA=$_POST['WCA'];
    $Delegate=$delegateData['Delegate_ID'];
}



$result=@file_get_contents(GetIni('WCA_API','competition')."/$WCA");
$registrations=json_decode($result);
if(!$registrations){
    SetMessageName('CompetitionCreate','WCA not load '.$WCA);
    HeaderExit();  
}


DataBaseClass::Query("Select ID from `Competition` where `WCA`='$WCA'");
//$DateStart=Competitor_Date_Start($Date);
$Name= DataBaseClass::Escape($registrations->name);
$City=DataBaseClass::Escape($registrations->city);
$Country=DataBaseClass::Escape($registrations->country_iso2);
$StartDate=DataBaseClass::Escape($registrations->start_date);
$EndDate=DataBaseClass::Escape($registrations->end_date);
$WebSite=DataBaseClass::Escape($registrations->website);
if(DataBaseClass::rowsCount()>0){
    $ID=DataBaseClass::getRow()['ID'];
    DataBaseClass::Query("Update `Competition` set "
            . "`Name`='$Name',"
            . "`StartDate`='$StartDate',"
            . "`EndDate`='$EndDate',"
            . "`City`='$City',"
            . "`Country`='$Country',"
            . "`WebSite`='$WebSite'"
            . " where `WCA`='$WCA'");
    SetMessage("CompetitionCreate: Update $WCA");
}else{
    DataBaseClass::Query("Insert into `Competition`"
            . " (`WCA`, `Name`, `StartDate`, `EndDate`,`City`,`Country`,`WebSite`,`Status`,`Registration`,`Onsite`) "
            . "values ('$WCA','$Name','$StartDate','$EndDate','$City','$Country','$WebSite',0,0,0)");
    $ID=DataBaseClass::getID();   
    DataBaseClass::Query("Insert into `CompetitionDelegate` (Competition,Delegate) values ($ID,$Delegate)");
    
    SetMessage("CompetitionCreate $WCA ".print_r($_POST,true));
    
    AddLog("Competition","Create",$delegateData['Delegate_Name'].' / '.$WCA);
}

header('Location: '.PageIndex().'Competition/'.$WCA);
exit();  
