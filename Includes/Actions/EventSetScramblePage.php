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
Databaseclass::Join_current('DisciplineFormat');
Databaseclass::Join_current('Discipline');
Databaseclass::Join('DisciplineFormat','Format');
Databaseclass::Join('Event','Competition');
$data=Databaseclass::QueryGenerate(false);
$Discipline=$data['Discipline_Code'];
$Attemption=$data['Format_Attemption'];

include 'Extras.php';


for($g=1;$g<=$data['Event_Groups'];$g++){
    for($a=1;$a<=$data['Format_Attemption']+$exs;$a++){
        if(!isset($scrambles[$g][$a])){
            $scrambles[$g][$a]=array('Scramble'=>'','Timestamp'=>'');
        }
    }
}
?>
<head>
    <title>Set scrambles</title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>
<h3>Set scrambles</h3>
<h2><?= $data['Competition_Name']?> / <?= $data['Discipline_Name']?> 
<?php if ($data['Discipline_Code']=='Prepared'){
    $data['Event_Groups']=1;
    $data['Format_Attemption']=1;
    $exs=0;
    ?>
    (1 scramble)
<?php }else{ ?>
    (<?= $data['Event_Groups']*($data['Format_Attemption']+2) ?> scrambles)
<?php } ?>
</h2>
<table>
    <tr>
        <td>
            <table border="1">
                <tr>
                    <td>Group</td>
                    <td>Attempt</td>
                    <td>Timestamp</td>
                    <td>Scramble</td>
                </tr>
            <?php
            for($g=1;$g<=$data['Event_Groups'];$g++){
                for($a=1;$a<=$data['Format_Attemption']+$exs;$a++){ ?>
                    <tr>
                        <td><?= $g ?></td>
                        <td><?= $a>$data['Format_Attemption']?"Ex ".($a-$data['Format_Attemption']):$a ?></td>
                        <td><?= $scrambles[$g][$a]['Timestamp'] ?></td>
                        <td align="left">
                            <?= $scrambles[$g][$a]['Scramble'] ?>
                        </td>            
                    </tr>
                <?php }
            } ?>
            </table>
        </td>
        <td>
        <?php if($data['Discipline_TNoodle']){?>
            <?php $link="http://localhost:2014/scramble-legacy/#competitionName=".$data['Competition_WCA']."_".$data['Discipline_Code']."_".$data['Event_Round']."&rounds=i('eventID'-'".$data['Discipline_TNoodle']."'_'round'-'1'_'scrambleSetCount'-".$data['Event_Groups']."_'scrambleCount'-".$data['Format_Attemption']."_'extraScrambleCount'-".$exs."_'copies'-1)!&version=1.0" ?>
                0. Prepare TNoodle WCA Scrambler <a target="_blank" href="https://www.worldcubeassociation.org/regulations/scrambles/">Instructions</a>.<br>
                1. Generate scrambles in <a target="_blank" href="<?= $link ?>">TNoodle WCA Scrambler</a> {<?= $data['Discipline_TNoodle']?>}<br>
                2. Load file "Interchange/*.json"
            <form name="EventSetScrambleFile" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/EventSetScrambleFile" ?>">           
                <div class="fileinputs">
                    <input type="file" class="file" name="file" multiple="true" onchange="document.forms['EventSetScrambleFile'].submit();"/>
                    <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
                    <div class="fakefile" id="fkf">
                        <button class="form_change">Json</button> 
                    </div>
                </div>
            </form>
        <?php } ?>
        <form method="POST" action="<?= PageIndex()?>Actions/EventSetScramble">
            <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
            <textarea cols="60" rows="30" name="Scrambles"><?= implode("\n",$scrambles_row); ?></textarea><br>
            <input style="background-color:lightgreen" type="submit" value="Manual load>">
        </form>
            
        </td>
    </tr>
</table>    

<?php
exit();