<?php
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');
$ID=$_POST['ID'];

if($ID==0){
    CheckingRoleAdmin();    
    DataBaseClass::Query("Select * from Competitor where Name not like '%&%' and (WCAID='' or Country is null) order by ID desc");
    $competitors=DataBaseClass::getRows();
    
    foreach($competitors as $competitor){
        $contentPerson=file_get_contents("https://www.worldcubeassociation.org/search?q=".str_replace(" ","%20",$competitor['Name']));
        $contentPerson=str_replace(array("<strong>","</strong>"),"",$contentPerson);
        preg_match_all('/\/persons\/([\s\S]*)">([\s\S]*)[\(|<][\s\S]*<td>([\s\S]*)<\/td>/mU',$contentPerson,$matches);
        foreach($matches[2] as $n=>$match){
            if(trim($match)==trim($competitor['Name'])){
                $WCAID=trim($matches[1][$n]);
                $Country=trim($matches[3][$n]);
                DataBaseClass::Query("Update `Competitor` set WCAID='$WCAID', Country='$Country' where ID='".$competitor['ID']."'");
            }
        }
    }
 
    SetMessage();    

    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}

CheckingRoleDelegate($ID);


$WCA=DataBaseClass::SelectTableRow('Competition',"ID=$ID")['Competition_WCA'];

$content=file_get_contents("https://www.worldcubeassociation.org/competitions/".$WCA."/registrations");

preg_match_all('/<td class="name">([\s\S]*)<\/td>[\s\S]*<td class="country">(.*)<\/td>/mU',$content,$matches);

$names=array();
$WCAIDs=array();
$Countries=array();
foreach($matches[2] as $Country){
    $Countries[]=$Country;
}
foreach($matches[1] as $name){
    $name=trim($name);
    if(strpos($name,'/persons/')!==FAlSE){
        $WCAIDs[]=substr($name,strpos($name,'/persons/')+9,10);
        $name=substr($name,strpos($name,'/persons/')+21);
        
    }else{
        $WCAIDs[]="";
    }
    
    preg_match('/(.*)[<|(]/U',$name,$find);
    if(isset($find[1])){
        $names[]=trim($find[1]);
    }else{
        $names[]=$name;
    }    
}
DataBaseClass::Query("select coalesce(max(localID),0) localID from CompetitorWCA where Competition='$ID'");
$maxLocalID=DataBaseClass::getRow()['localID'];
 
for($i=0;$i<sizeof($names);$i++){
   $competitorWCA=DataBaseClass::SelectTableRow('CompetitorWCA',"Name='".$names[$i]."' and Competition='$ID'");
   if($competitorWCA['CompetitorWCA_ID']){
       DataBaseClass::Query("Update CompetitorWCA set WCAID='".$WCAIDs[$i]."',Country='".$Countries[$i]."' where ID='".$competitorWCA['CompetitorWCA_ID']."'");
   }else{
      $maxLocalID++;
      DataBaseClass::Query("Insert into CompetitorWCA (Name,WCAID,Country,Competition,LocalID) "
      . "values ('".$names[$i]."','".$WCAIDs[$i]."','".$Countries[$i]."','$ID',$maxLocalID)");
       
   }    
}

SetMessage();    

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
