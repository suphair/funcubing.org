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

if(!$data['Discipline_GlueScrambles'] or !$data['Discipline_TNoodle']){
    exit();
} ?>
<head>
    <title>Glue the scrambles</title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>
<h3>Glue the scrambles</h3>
<h2><?= $data['Competition_Name']?> / <?= $data['Discipline_Name']?></h2>
    <?php if($data['Format_Attemption']==5){
        $ex=2;
    }else{
        $ex=1;
    } ?>


    <h3>Scrambles {<?= $data['Discipline_TNoodle'] ?>} for <?= $data['Event_Groups'] ?> groups (<?= $data['Format_Attemption']."+".$ex?>)</h3>
    <center>

    <?php
    $event_request="('eventID'-'".$data['Discipline_TNoodle']."'_'round'-'1'_'scrambleSetCount'-".$data['Event_Groups']."_'scrambleCount'-".$data['Format_Attemption']."_'extraScrambleCount'-".$ex."_'copies'-1)";
        
    $link="http://localhost:2014/scramble-legacy/#competitionName=".$data['Competition_WCA']."_".$data['Discipline_Code']."_1&rounds=i".
    $event_request        
    ."!&version=1.0"; ?>
    0. Prepare TNoodle WCA Scrambler <a target="_blank" href="https://www.worldcubeassociation.org/regulations/scrambles/">Instructions</a>.<br><br>    
    1. Generate scrambles in <a target="_blank" href="<?= $link ?>">TNoodle WCA Scrambler</a><br><br>
    2. Load file "Printing/* - All Scrambles.pdf"<br><br>
    <form name="EventSetGlueScramblesTNoodlePDF" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/EventSetGlueScramblesTNoodlePDF" ?>">           
        <div class="fileinputs">
            <input type="file" accept="application/pdf" class="file" name="file" multiple="true" onchange="document.forms['EventSetGlueScramblesTNoodlePDF'].submit();"/>
            <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
            <div class="fakefile" id="fkf">
                <button class="form_change">PDF</button> 
            </div>
        </div>
    </form>

    </center>
    <?php
exit();