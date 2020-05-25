<?php
if (isset($request[2]) and $request[2]=='config'){
    $config="/config";
}else{
    $config='';
}  
$isAdmin=CheckAdmin();

DataBaseClass::FromTable('Discipline'); 

if($isAdmin){
    
}else{
    DataBaseClass::Where_current("Status='Active'");
}
    
$disciplines=DataBaseClass::QueryGenerate();
    ?>
<div class="line">
    <?php foreach($disciplines as $discipline_row){ ?>   
        <a class="<?= $discipline_row['Discipline_ID']==$ID?"line_select":""?>" title="<?= $discipline_row['Discipline_Name'] ?>" href="<?= LinkDiscipline($discipline_row['Discipline_Code']) ?>/<?= $config ?>"><?= ImageDiscipline($discipline_row['Discipline_Code'],50) ?></a> 
    <?php } ?>
        <a class="<?= $Code=='MosaicBuilding'?"line_select":""?>" title="Mosaic Building" href="<?=Pageindex(); ?>Discipline/MosaicBuilding">
            <img align="center" title="Picture" height=50px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
        </a> 
</div>