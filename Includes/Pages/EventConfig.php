<?php
DataBaseClass::FromTable('Competition',"WCA='".RequestClass::getParam1()."'");
$competition=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join_current('CompetitionDelegate');
DataBaseClass::Join_current('Delegate');
$delegates=DataBaseClass::QueryGenerate();
?>
<?php $Event=RequestClass::getParam2();?>

<?php include 'CompetitionHeader.php'; ?>

<?php
DataBaseClass::FromTable('Event',"Competition='".$competition['Competition_ID']."'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current( 'Discipline');
DataBaseClass::OrderClear('Discipline','Name');
$disciplines=DataBaseClass::QueryGenerate();
            
?>
<div class="line discipline_line">
    <?php foreach($disciplines as $discipline_row){?>
        <nobr>
            <?= ImageDiscipline($discipline_row['Discipline_Code'],30) ?>
            <?php if($discipline_row['Event_ID']==$Event){ ?>
                <span class="list_select"><?= $discipline_row['Discipline_Name'] ?><?= $discipline_row['Event_vRound'] ?></span>
            <?php }else{ ?>
                <a class="<?= $discipline_row['Event_ID']==$Event?"list_select":""?>"  href="<?= LinkEvent($discipline_row['Event_ID']) ?>/config"><?= $discipline_row['Discipline_Name'] ?><?= $discipline_row['Event_vRound'] ?></a>
            <?php } ?>
        </nobr>
    <?php } ?>
    <?php if ($competition['Competition_EventPicture']){ ?>
        <nobr><img align="center" title="Picture" height=30px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
            <a class="<?= $MosaicBuilding?"list_select":""?>"  href="<?= PageIndex() ?>Competition/<?= $competition['Competition_WCA'] ?>/MosaicBuilding/config">
                 Mosaic Building
            </a> 
        </nobr>
    <?php } ?>
</div>

<?php 
    DataBaseClass::FromTable('Event',"ID='".RequestClass::getParam2()."'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format');
    DataBaseClass::Join('Event','Competition');
    $event= DataBaseClass::QueryGenerate(false); ?>
<hr class='hr_round'>
<h2>
    <?php if(sizeof($disciplines)>1){ ?>
        <?= ImageDiscipline($event['Discipline_Code'],50) ?> 
    <?php } ?>
    <a href="<?= LinkEvent($event['Event_ID']) ?>"><?= $event['Discipline_Name'] ?><?= $event['Event_vRound'] ?></a> <nobr>&#9642;<span class="config"> Setting</span></nobr>
</h2>

    
<?php if($event['Event_Comment']){?>
    <div class="form">
    <?= Echo_format($event['Event_Comment']); ?>
    </div>
<?php } ?>
    
    <?php if(CheckDelegateCompetition($competition['Competition_ID'],false)) {
        include 'EventDelegate.php';
    } ?> 