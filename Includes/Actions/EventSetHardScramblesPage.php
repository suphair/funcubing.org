<?php
CheckGetIsset('ID');
CheckGetIsNumeric('ID');
$ID=$_GET['ID'];

CheckingRoleDelegateEvent($ID);


DataBaseClass::Query("Select S.Timestamp, S.Scramble,S.Group,S.Attempt from `Scramble` S where S.`Event`='$ID' order by S.Group, S.Attempt");
$scrambles=array();
$scrambles_row=array();
foreach(DataBaseClass::getRows() as $row){
    $scrambles[$row['Group']][$row['Attempt']]=array('Scramble'=>$row['Scramble'],'Timestamp'=>$row['Timestamp']);
    $scrambles_row[]=$row['Scramble'];
}


Databaseclass::FromTable('Event', "ID='$ID'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat','Format');
Databaseclass::Join('Event','Competition');
$data=Databaseclass::QueryGenerate(false);

for($g=1;$g<=$data['Event_Groups'];$g++){
    for($a=1;$a<=$data['Format_Attemption']+2;$a++){
        if(!isset($scrambles[$g][$a])){
            $scrambles[$g][$a]=array('Scramble'=>'','Timestamp'=>'');
        }
    }
}
$scrs=$data['Event_Groups']*($data['Format_Attemption']+1);
$Pages_event=ceil($scrs/(5+2));

?>
<head>
    <title>Glue the scrambles</title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>
<h3>Glue the scrambles</h3>
<h2><?= $data['Competition_Name']?> / <?= $data['Discipline_Name']?></h2>
<h3><?= $scrs ?> scrambles for <?= $Pages_event==1?'page':"$Pages_event pages" ?></h3>
<center>
    
<?php
$event_requests=[];
foreach(explode(",",$data['Discipline_TNoodles']) as $event){
    if(in_array($event,['666','777'])){
        $event_requests[]="('eventID'-'".$event."'_'round'-'1'_'scrambleSetCount'-".$data['Discipline_TNoodlesMult']*$Pages_event."_'scrambleCount'-3_'extraScrambleCount'-1_'copies'-1)";
    }else{
        $event_requests[]="('eventID'-'".$event."'_'round'-'1'_'scrambleSetCount'-".$data['Discipline_TNoodlesMult']*$Pages_event."_'scrambleCount'-5_'extraScrambleCount'-2_'copies'-1)";
    }
}
$link="http://localhost:2014/scramble-legacy/#competitionName=".$data['Competition_WCA']."_".$data['Discipline_Code']."_1&rounds=i".
implode("_",$event_requests)        
."!&version=1.0"; ?>
0. Prepare TNoodle WCA Scrambler <a target="_blank" href="https://www.worldcubeassociation.org/regulations/scrambles/">Instructions</a>.<br><br>    
1. Generate scrambles in <a target="_blank" href="<?= $link ?>">TNoodle WCA Scrambler</a><br>{<?= $data['Discipline_TNoodles']?>} <?=  $data['Discipline_TNoodlesMult']>1?(' * '.$data['Discipline_TNoodlesMult']):'' ?><br><br>
2. Load file "Printing/* - All Scrambles.pdf"<br><br>
<form name="EventSetHardScrambleFile" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/EventSetHardScrambleFile" ?>">           
    <div class="fileinputs">
        <input type="file" accept="application/pdf" class="file" name="file" multiple="true" onchange="document.forms['EventSetHardScrambleFile'].submit();"/>
        <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
        <div class="fakefile" id="fkf">
            <button class="form_change">PDF</button> 
        </div>
    </div>
</form>

</center>
<?php
exit();