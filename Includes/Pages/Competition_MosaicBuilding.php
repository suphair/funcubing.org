<h2>
    <?php if(sizeof($disciplines)>0){ ?>
        <img align="center" title="Picture" height=50px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
    <?php } ?>
    Mosaic Building
    <?php if(CheckDelegateCompetition($competition['Competition_ID'],false)){ ?>
        <nobr>&#9642; <a href="<?= PageIndex() ?>Competition/<?= $competition['Competition_WCA'] ?>/MosaicBuilding/config">Setting</a></nobr>
    <?php } ?>

&#9642; <a href='<?=Pageindex(); ?>Discipline/MosaicBuilding'>Gallery</a>
</h2>
<?php DataBaseClass::FromTable("MosaicBuilding","Competition=".$competition['Competition_ID']);
DataBaseClass::OrderSpecial("length(MB.Description) desc");
foreach(DataBaseClass::QueryGenerate() as $row){ 
    
    $competitors=[];
    $Description=$row['MosaicBuilding_Description']; 
    $template = "/\{(.*)\}/";
    preg_match($template, $Description, $maches); 
    if(isset($maches[1])){
        $Description=str_replace("{".$maches[1]."}","",$Description);
        $competitors=explode(",",$maches[1]);
    } ?>
<div class="form">
    <h3><?= $Description ?></h3>
    <?php foreach($competitors as $competitor){ 
             $competitor=trim($competitor);
             DataBaseClass::Query("Select * from Competitor where WCAID='$competitor' or Name like '$competitor%'");
             $comp= DataBaseClass::getRow();
         ?>
         <p><?php
         if(is_array($comp)){ ?>
             <a href="<?= PageIndex()?>Competitor/<?= $comp['ID'] ?>"><?= Short_Name($comp['Name'])?></a>
         <?php }else{ ?>
             <?= $competitor ?>
         <?php } ?>
         </p>
     <?php } ?>    
    <?php 
    DataBaseClass::FromTable("MosaicBuildingImage","MosaicBuilding=".$row['MosaicBuilding_ID']);
    foreach(DataBaseClass::QueryGenerate() as $rowI){ ?>    
    <div style="width:300px;height: 200px; float: left; ">
        <!--<a target="_blank" href="<?= PageIndex()?>Image/MosaicBuilding/<?= $rowI['MosaicBuildingImage_Filename']?>">-->
            <img class="imageSmall" style=" max-height: 100%;max-width: 100%;
                 display: block; margin: auto;height: auto;
  " src="<?= PageIndex()?>Image/MosaicBuilding/<?= $rowI['MosaicBuildingImage_Filename']?>">
        <!--</a>-->
    </div>    
    <?php } ?>
</div>    
<?php } ?>
<?php include 'MosaicBuilding_Show.php'; ?>

    