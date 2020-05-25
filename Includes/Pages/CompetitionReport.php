<?php

DataBaseClass::FromTable('Competition',"WCA='".RequestClass::getParam1()."'");
$competition=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join_current('CompetitionReport');
DataBaseClass::Where_current('Report<>""');
DataBaseClass::Join_current( 'Delegate');
$reports=DataBaseClass::QueryGenerate();
$delegates_report=[];
?>

<h1><a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name'] ?></a> &#9642; Report</h1>
<?php foreach($reports as $report){ 
    $delegates_report[$report['Delegate_ID']]=$report;
     ?>
    <div class="form">
        <b>Report by <?= $report['Delegate_Name'] ?></b><br>
        <?= Echo_format($report['CompetitionReport_Report']); ?>
    </div>
<?php } ?>


<?php 
$Instruction=GetBlockText("Report");
if(CheckAdmin()){
    DataBaseClass::FromTable('Competition',"WCA='".RequestClass::getParam1()."'");
    DataBaseClass::Join_current('CompetitionDelegate');
    DataBaseClass::Join_current( 'Delegate');
    DataBaseClass::SelectPre( 'Dl.Name Delegate_Name, Dl.ID Delegate_ID');
    foreach(DataBaseClass::QueryGenerate() as $delegate){
        $delegates[]=$delegate;
        $dls[]=$delegate['Delegate_ID'];
    }
    
    foreach($delegates_report as $d_ID=>$delegate){
        if(!in_array($d_ID,$dls)){
            $delegates[]=$delegate;
        }
    }
}else{
    $delegates[]=$Delegate;
}

if(CheckDelegateCompetition($competition['Competition_ID'],false)){ ?>
    <br>
    <div class="form">
        <b>Instruction</b><br>
        <?= Echo_format($Instruction); ?>
            <br>
            <?php foreach($delegates as $delegate){?>
                <br><b>Enter report by <?= $delegate['Delegate_Name'] ?></b>

                <form method="POST" action="<?= PageIndex()."Actions/CompetitionAddReport" ?>">
                    <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
                    <input name="Delegate" type="hidden" value="<?= $delegate['Delegate_ID'] ?>" />
                    <textarea name="Report" style="height: 200px;width: 400px"><?php
                        if(isset($delegates_report[$delegate['Delegate_ID']]['CompetitionReport_Report'])){ ?><?= $delegates_report[$delegate['Delegate_ID']]['CompetitionReport_Report'] ?><?php }
                    ?></textarea><br>
                    <input type="submit" name="submit" value="Save report">
                </form> 
            <?php } ?>
    </div>
<?php } ?>