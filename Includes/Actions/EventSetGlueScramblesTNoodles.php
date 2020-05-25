<?php
CheckGetIsset('ID');
CheckGetIsNumeric('ID');
$ID=$_GET['ID'];

CheckingRoleDelegateEvent($ID);

Databaseclass::FromTable('Event', "ID='$ID'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat','Format');
Databaseclass::Join('Event','Competition');
$data=Databaseclass::QueryGenerate(false);

if(!$data['Discipline_GlueScrambles'] or !$data['Discipline_TNoodles']){
    exit();
} ?>
<head>
    <title>Glue the scrambles</title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>
<h3>Glue the scrambles</h3>
<h2><?= $data['Competition_Name']?> / <?= $data['Discipline_Name']?></h2>
<?php 

    $scrs=$data['Event_Groups']*($data['Format_Attemption']+1);
    
    if($data['Discipline_Code']=='Scrambling'){
        $scrs=$data['Event_Groups']*2;
    }
    
    $Pages_event=ceil($scrs/(5+2));

    ?>
    <h3><?= $scrs ?> scrambles for <?= $Pages_event==1?'page':"$Pages_event pages" ?> </h3>
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
    <form name="EventSetGlueScramblesTNoodlesPDF" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/EventSetGlueScramblesTNoodlesPDF" ?>">           
        <div class="fileinputs">
            <input type="file" accept="application/pdf" class="file" name="file" multiple="true" onchange="document.forms['EventSetGlueScramblesTNoodlesPDF'].submit();"/>
            <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
            <div class="fakefile" id="fkf">
                <button class="form_change">PDF</button> 
            </div>
        </div>
    </form>

    </center>
<?php 
exit();