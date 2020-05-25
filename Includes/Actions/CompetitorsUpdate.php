<?php
$start=date("d.m.Y H:i:s");


DataBaseClass::Query("Select C.Name,C.ID from Competitor C where (C.WID is null  or C.WCAID='') and C.ID not in (Select Competitor from CommandCompetitor)");
foreach(DataBaseClass::getRows() as $Wrongs){
    DataBaseClass::Query("Delete from Registration where Competitor=".$Wrongs['ID']);
    DataBaseClass::Query("Delete from Competitor where ID=".$Wrongs['ID']);
}



DataBaseClass::Query(""
        . "Select C.ID,C.WID from Competitor C  where  C.WID is not null and WCAID=''");
foreach(DataBaseClass::getRows() as $Row){
   $ID=$Row['ID'];

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/users/".$Row['WID']);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $data = curl_exec($ch);
   $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
   if($status==200){
       $user=json_decode($data);
       DataBaseClass::Query("Update Competitor set "
      . " Name='". DataBaseClass::Escape($user->user->name)."'"
      . " ,Country='".$user->user->country_iso2."'"
      ." ,WCAID='".$user->user->wca_id."'"
      . " where WID=$ID");

       DataBaseClass::FromTable("Competitor","WID=$ID");
       DataBaseClass::Join_current("CommandCompetitor");
       DataBaseClass::Join_current("Command");
       foreach(DataBaseClass::QueryGenerate() as $command){
           CommandUpdate('',$command['Command_ID']);
       }
   }
   curl_close($ch);
}




DataBaseClass::Query("Select C.ID,C.WCAID from Competitor C  where  C.WID is null and WCAID!=''");

foreach(DataBaseClass::getRows() as $Row){
   $ID=$Row['ID'];

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/users/".$Row['WCAID']);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $data = curl_exec($ch);
   $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
   if($status==200){
       $user=json_decode($data);
       DataBaseClass::Query("Update Competitor set "
      . " Name='". DataBaseClass::Escape($user->user->name)."'"
      . " ,Country='".$user->user->country_iso2."'"
      . " ,WID='".$user->user->id."'"
      . " where ID='$ID'");

       DataBaseClass::FromTable("Competitor","ID='$ID'");
       DataBaseClass::Join_current("CommandCompetitor");
       DataBaseClass::Join_current("Command");
       foreach(DataBaseClass::QueryGenerate() as $command){
           CommandUpdate('',$command['Command_ID']);
       }
   }
   curl_close($ch);
}

DataBaseClass::Query("Select C.ID,C.Name from Competitor C  where  C.WID is null and WCAID='' order by ID");

foreach(DataBaseClass::getRows() as $Row){
   $ID=$Row['ID'];

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/search/users/?q=".urlencode($Row['Name']));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $data = curl_exec($ch);
   $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
   if($status==200){
       $user=json_decode($data);
       if(isset($user->result[0])){
            if(short_name($user->result[0]->name)==short_name($Row['Name'])){
                DataBaseClass::Query("Update Competitor set "
               . " Name='". DataBaseClass::Escape($user->result[0]->name)."'"
               . " ,Country='".$user->result[0]->country_iso2."'"
               . " ,WID='".$user->result[0]->id."'"
               . " ,WCAID='".$user->result[0]->wca_id."'"
               . " where ID='$ID'");

                DataBaseClass::FromTable("Competitor","ID='$ID'");
                DataBaseClass::Join_current("CommandCompetitor");
                DataBaseClass::Join_current("Command");
                foreach(DataBaseClass::QueryGenerate() as $command){
                    CommandUpdate('',$command['Command_ID']);
                }
            }
       }
   }
   curl_close($ch);
}

    

DataBaseClass::Query("
select count(*),WID from Competitor
where WID is not null
group by WID
having count(*)>1
");

foreach(DataBaseClass::getRows() as $Double){
    $WID=$Double['WID'];
    DataBaseClass::Query("
        select ID from Competitor
        where WID = '$WID'
    ");
    $Competitors=DataBaseClass::getRows();
    
    $ID=$Competitors[0]['ID']; 
    foreach($Competitors as $Competitor){
        if($ID!=$Competitor['ID']){
            DataBaseClass::Query("Update CommandCompetitor set Competitor=$ID where Competitor=".$Competitor['ID']);
            DataBaseClass::Query("Delete from Competitor where ID=".$Competitor['ID']);
        }
        
    }
}

$end=date("d.m.Y H:i:s");

SaveValue('CompetitorsUpdate',$start." - ".$end);

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  