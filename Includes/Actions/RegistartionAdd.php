<?php

CheckPostIsset('Competition','Name');
CheckPostNotEmpty('Competition');
CheckPostIsNumeric('Competition');

$Competition=$_POST['Competition'];
CheckDelegateCompetition($Competition);

$Name=$_POST['Name'];
$name_search= strtolower(DataBaseClass::Escape(Short_Name($Name)));

DataBaseClass::Query("Select * from Competitor where LOWER(WCAID)='$name_search' or "
        . " ( ( LOWER(Name) like '".$name_search."%(%' or  LOWER(Name)='$name_search' ))");

$competitor=DataBaseClass::getRow();
if(isset($competitor['ID'])){   
    
    DataBaseClass::Query("Select * from Registration where Competitor='".$competitor['ID']."' and Competition='".$Competition."'");
    if(!isset(DataBaseClass::getRow()['ID'])){
        DataBaseClass::Query("Insert into Registration (Competitor,Competition) values (".$competitor['ID'].",$Competition)");
        SetMessageName("RegistartionAdd", "Register(1) ".print_r($competitor,true));
    }else{
        SetMessageName("RegistartionAdd", "Register(2) ".print_r($competitor,true));
    }    
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();
}  

$name_search=urlencode($name_search);
$user_content = file_get_contents("https://www.worldcubeassociation.org/api/v0/search/users/?q=$name_search", false); 
$user=json_decode($user_content,true);

if(sizeof($user['result']) and (
            strtolower($user['result'][0]['wca_id'])==strtolower($name_search) 
            or strtolower(short_name($user['result'][0]['name']))==strtolower(short_name($Name))
            )
){
    $name=$user['result'][0]['name'];
    $wcaid=$user['result'][0]['wca_id'];
    $country=$user['result'][0]['country_iso2'];
    $wid=$user['result'][0]['id'];
    
    DataBaseClass::Query("Select * from Competitor where (WCAID='$wcaid' and '$wcaid'<>'') or (WID='$wid')");
    $competitor=DataBaseClass::getRow();
    if(isset($competitor['ID'])){ 
        $ID=$competitor['ID'];
        DataBaseClass::Query("Update Competitor set Name='$name', Country='$country' where ID='$ID'");  
        DataBaseClass::Query("Select * from Competitor where ID='$ID'");
        $competitor=DataBaseClass::getRow();
        DataBaseClass::Query("Select * from Registration where Competitor='".$competitor['ID']."' and Competition='".$Competition."'");
        if(!isset(DataBaseClass::getRow()['ID'])){
            DataBaseClass::Query("Insert into Registration (Competitor,Competition) values (".$competitor['ID'].",$Competition)");
            SetMessageName("RegistartionAdd", "Register(3) ".print_r($competitor,true));
        }else{
            SetMessageName("RegistartionAdd", "Register(4) ".print_r($competitor,true));
        }
    }else{
        DataBaseClass::Query("Insert Into Competitor (WCAID,WID,Country,Name) values ('$wcaid','$wid','$country','$name')");        
        $ID=DataBaseClass::getID();
        DataBaseClass::Query("Select * from Competitor where ID='$ID'");
        $competitor=DataBaseClass::getRow();
        DataBaseClass::Query("Insert into Registration (Competitor,Competition) values (".$competitor['ID'].",$Competition)");
        SetMessageName("RegistartionAdd", "Register(5) ".print_r($competitor,true));
    }
}else{
    DataBaseClass::Query("Insert Into Competitor (WCAID,WID,Country,Name) values ('',Null,'','$Name')");    
    $competitorID=DataBaseClass::getID();
    DataBaseClass::Query("Select * from Competitor where ID=$competitorID");
    $competitor=DataBaseClass::getRow();
    DataBaseClass::Query("Insert into Registration (Competitor,Competition) values (".$competitor['ID'].",$Competition)");
    SetMessageName("RegistartionAdd", "Register(6) ".print_r($competitor,true));
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();


