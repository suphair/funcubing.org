<?php
if(!GetDelegateData()){
    HeaderExit();      
}

CheckPostIsset('WCA');
CheckPostNotEmpty('WCA');


$WCA=$_POST['WCA'];

$result=@file_get_contents(GetIni('WCA_API','competition')."/$WCA");
$registrations=json_decode($result);
if(!$registrations){
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
  
    SetMessage();
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  

