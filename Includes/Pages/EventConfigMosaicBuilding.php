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
                <a href="<?= LinkEvent($discipline_row['Event_ID']) ?>/config"><?= $discipline_row['Discipline_Name'] ?><?= $discipline_row['Event_vRound'] ?></a>
        </nobr>
    <?php } ?>
    <?php if ($competition['Competition_EventPicture']){ ?>
           <nobr><img align="center" title="Picture" height=30px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
               <span class="list_select">
                    Mosaic Building
               </span> 
           </nobr>
    <?php } ?>
</div>
<hr class='hr_round'>
<h2>
    <?php if(sizeof($disciplines)>0){ ?>
        <img align="center" title="Picture" height=50px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png"> 
    <?php } ?>
    <a href="<?= PageIndex() ?>Competition/<?= $competition['Competition_WCA'] ?>/MosaicBuilding">
            Mosaic Building
    </a> <nobr>&#9642;<span class="config"> Setting</span></nobr>
</h2>
<br>
<div class="form">
    Team members in the format<br> <b>{WCAID_1, WCAID_2, NAME_3}</b>
    <?= ImageCompetition($competition['Competition_WCA']) ?> 
    <form  enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/CompetitionMosaicBuildingLoad" ?>">           
        <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
        <div class="form_field">
            Images
        </div>
        <div class="form_input">
            <input required="" type="file" name="FILES[]" multiple/>
        </div>
        <div class="form_field">
            Description
        </div>
        <div class="form_input">
            <textarea name="Description"></textarea>
        </div>
        <br><input type=submit value="Load images" />
    </form> 
</div> 
<?php DataBaseClass::FromTable("MosaicBuilding","Competition=".$competition['Competition_ID']);
foreach(DataBaseClass::QueryGenerate() as $row){ 
    $competitors=[];
    $Description=$row['MosaicBuilding_Description']; 
    $template = "/\{(.*)\}/";
    preg_match($template, $Description, $maches); 
    if(isset($maches[1])){
        //$Description=str_replace("{".$maches[1]."}","",$Description);
        $competitors=explode(",",$maches[1]);
    }
    
    ?>
<div class="form">
    <form  method="POST" action="<?= PageIndex()."Actions/CompetitionMosaicBuildingChange" ?>">           
        <span class="form_input">
            <textarea  name="Description"><?= $Description ?></textarea>
        </span>
        <?php foreach($competitors as $competitor){ 
                $competitor=trim($competitor);
                DataBaseClass::Query("Select * from Competitor where WCAID='$competitor' or Name like '$competitor%'");
                $comp= DataBaseClass::getRow();
            ?>
            <p><?php
            if(is_array($comp)){ ?>
                <?= '+ '.Short_Name($comp['Name']).' '.$comp['WCAID']?>
            <?php }else{ ?>
                <?= '- '.$competitor ?>
            <?php } ?>
            </p>
        
        <?php } ?>
        <?php 
        DataBaseClass::FromTable("MosaicBuildingImage","MosaicBuilding=".$row['MosaicBuilding_ID']);
        foreach(DataBaseClass::QueryGenerate() as $rowI){ ?>    
            <img width="100px" src="<?= PageIndex()?>Image/MosaicBuilding/<?= $rowI['MosaicBuildingImage_Filename']?>">
        <?php } ?>
            <br>
        <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
        <input name="MosaicBuilding" type="hidden" value="<?= $row['MosaicBuilding_ID'] ?>" />
        <input type=submit value="Save" name="Action"/>
        <input class="delete" type=submit value="Delete" name="Action" onclick="return confirm('Attention: Delete?')" />
    </form>
</div>    
<?php } 

    foreach (scandir("Image/MosaicBuilding") as $filename){
        if(explode("_",$filename)[0]==$competition['Competition_ID']){ ?>
            <a target="_blank" href="<?= PageIndex()?>Image/MosaicBuilding/<?= $filename?>"><img width="50px" src="<?= PageIndex()?>Image/MosaicBuilding/<?= $filename?>"></a>
        <?php }
    }
?>
