
<?php include 'Navigator.php' ?>

<?php $competitor=DataBaseClass::SelectTableRow('Competitor', "ID='".RequestClass::getParam1()."'") ?>
<br>
<h1><?= $competitor['Competitor_Name'] ?> </h1>
<h2>
    <?= ImageCountry($competitor['Competitor_Country'], 50)?><?= CountryName($competitor['Competitor_Country']) ?>
<?php if ($competitor['Competitor_WCAID']){ ?>    
    &#9642; <a href="https://www.worldcubeassociation.org/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?></a>
<?php } ?>  
</h2>  
<?php if(CheckAdmin()){ ?>
    <h3>
        <?php if ($competitor['Competitor_WID']){ ?>
            <span class="badge">    
                <a target="_blank" href="https://www.worldcubeassociation.org/api/v0/users/<?= $competitor['Competitor_WID'] ?>"><?= $competitor['Competitor_WID'] ?></a>
                &#9642; <a href="<?= PageIndex()?>Actions/CompetitorUpdate/?WID=<?= $competitor['Competitor_WID'] ?>"><span class="config">Update by WID</span></a>
            </span>
        <?php }elseif($competitor['Competitor_WCAID']){ ?>
            <span class="badge">
                <a target="_blank" href="https://www.worldcubeassociation.org/api/v0/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?></a>
                &#9642; <a href="<?= PageIndex()?>Actions/CompetitorUpdate/?WCAID=<?= $competitor['Competitor_WCAID'] ?>"><span class="config">Update by WCAID</span></a>
            </span>
        <?php }else{ ?> 
            <?= $competitor['Competitor_ID'] ?>
            <span class="badge">
                <input ID="SetCountry" value="<?= $competitor['Competitor_Country'] ?>">
                <a  onclick="document.location.href='<?= PageIndex()?>Actions/CompetitorUpdate/?ID=<?= $competitor['Competitor_ID'] ?>&Country=' + $('#SetCountry').val(); return false; " href="#">
                    <span class="config">Set Country</span>
                </a>
            </span>
            <span class="badge">
                <input ID="SetName" value="<?= $competitor['Competitor_Name'] ?>">
                <a  onclick="document.location.href='<?= PageIndex()?>Actions/CompetitorUpdate/?ID=<?= $competitor['Competitor_ID'] ?>&Name=' + $('#SetName').val(); return false; " href="#">
                    <span class="config">Update Name</span>
                </a>
            </span>
            <span class="badge">
                <a href="<?= PageIndex()?>Actions/CompetitorUpdate/?ID=<?= $competitor['Competitor_ID'] ?>&UpdateByName"><span class="config">Try to update by Name from WCA</span></a>
            </span>
        <?php } ?>
    </h3>  
<?php } ?>     

<?php 

DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Command');
DataBaseClass::Where_current('Decline!=1');
DataBaseClass::Join_current('Event');
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('Event','Competition');
DataBaseClass::OrderClear('Discipline', 'Name');
DataBaseClass::SelectPre('distinct D.ID Discipline_ID, '
        . 'D.Code Discipline_Code, '
        . 'D.Name Discipline_Name, '
        . 'D.Competitors Discipline_Competitors ');

$disciplines=DataBaseClass::QueryGenerate(); ?>

<table class="discipline_result">
<?php foreach($disciplines as $discipline){
    
    DataBaseClass::FromTable('Discipline',"ID='".$discipline['Discipline_ID']."'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Format');
    DataBaseClass::OrderClear('Format', 'Result');
    DataBaseClass::SelectPre('distinct F.Result,F.ExtResult,F.Attemption ');
    $types=array();
    $attemption=0;
    foreach(DataBaseClass::QueryGenerate() as $row){        
        $types[]=$row['Result'];
        if($row['ExtResult']){
            $types[]=$row['ExtResult'];    
        }
        $attemption=$attemption<$row['Attemption']?$row['Attemption']:$attemption;
    }
    
    foreach($types as $t=>$type){
        if($type=='Mean'){
            $types[$t]='Average';
        }
    }
    $types=array_unique($types);
    ?>
<tr class="no_border">
    <td colspan='<?= $attemption+3+sizeof($types)?>'><b>
        <br><?= ImageDiscipline($discipline['Discipline_Code'],30) ?>
        <a href="<?= LinkDiscipline($discipline['Discipline_Code'])?>"><?= $discipline['Discipline_Name'] ?></a>
    </b></td>        
</tr>        
        <tr class='tr_title'> 
            <td></td>
            <?php foreach($types as $type){ ?>
                <td class='attempt'>
                    <?= $type ?>
                </td>
            <?php } ?>
            <?php for($i=sizeof($types);$i<3;$i++){ ?>    
                <td/>
            <?php } ?>
            <td style="text-align:left">Competition</td>
            <td style="text-align:left"></td>

            <?php for($i=1;$i<=$attemption;$i++) {?>
            <td class="attempt">             
                <?php if($image=IconAttempt($discipline['Discipline_Code'],$i)){ ?>
                    <img src="<?= PageIndex() ?>/<?= $image ?>" width="20px">
                <?php }else{ ?>
                    <?= $i ?>
                <?php } ?>
            </td>
            <?php } ?>
        </tr>   
 
<?php             
 //foreach($discipline as $row){
    
DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Command');
DataBaseClass::Where_current('Decline!=1');
DataBaseClass::Join_current('Event');
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
DataBaseClass::Join('Event','Competition');
//DataBaseClass::OrderClear('Competition', 'ID Desc');
DataBaseClass::OrderClear('Competition', 'StartDate desc');
DataBaseClass::Order('Competition', 'EndDate desc');
DataBaseClass::Order('Event', 'Round desc');
$commands=DataBaseClass::QueryGenerate(true,false);


//usort($competitorEvents,'Competition_Sort');
$bestID=array();

foreach($types as $type){
    $format_arr=[$type];
    if($type=='Average'){
        $format_arr=['Mean','Average'];
    }
    
    DataBaseClass::FromTable('Competitor',"ID='".$competitor['Competitor_ID']."'");
    DataBaseClass::Join_current('CommandCompetitor');
    DataBaseClass::Join_current('Command');
    DataBaseClass::Where_current('Decline!=1');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Where_current("ID='".$discipline['Discipline_ID']."'");
    DataBaseClass::Join('Command','Attempt');        
    DataBaseClass::Where("A.Special in ('".implode("','",$format_arr)."')");
    DataBaseClass::Where_current("IsDNF=0");
    DataBaseClass::Limit("1");
    DataBaseClass::OrderClear("Attempt","vOrder");
    $bestID[]=DataBaseClass::QueryGenerate(false)['Attempt_ID']; 
}


    foreach($commands as $command){ 
        $attempts=array();
        for($i=1;$i<=$attemption;$i++) {
            $attempts[$i]="";
        }
        foreach(DataBaseClass::SelectTableRows("Format") as $format){
            $attempts[$format['Format_Result']]="";    
        }

        $is_attempt=false;
        DataBaseClass::FromTable('Attempt',"Command='".$command['Command_ID']."' ");
        foreach(DataBaseClass::QueryGenerate() as $attempt_row){
            $is_attempt=true;
            $attempt=$attempt_row['Attempt_vOut'];
            if($attempt_row['Attempt_Except']){
                $attempt="($attempt)";
            }

            if($attempt_row['Attempt_Attempt']){
               $attempts[$attempt_row['Attempt_Attempt']]= $attempt;
            }else{
                if($attempt_row['Attempt_Special']=='Mean'){
                    $type='Average';
                }else{
                    $type=$attempt_row['Attempt_Special'];
                }
               $attempts[$type]= $attempt; 
               $attempts_ID[$type]= $attempt_row['Attempt_ID'];
            }
        }
        
        $class=($command['Command_Place']<=3 and $command['Command_Place'])?"podium":"";?>

        <tr class="<?= $class ?>"
            onmouseover="this.className='competitor_block_select';"
            onmouseout=" this.className='<?= $class ?>';">
            <td class="number" <?= $command['Command_Place']<=3?"class='bold'":""; ?>>
                <?php if($command['Command_Place']){ ?>
                    <?= $command['Command_Place'] ?>
                <?php } ?>
                <?php if(!$is_attempt){ ?>
                    <?= svg_green(12,'Upcoming competition') ?>
                <?php } ?>
            </td>

            
            
            
            <?php 
            $WRecords=[];
            $NRecords=[];
            foreach($types as $type){ 
                    $format_arr=[$type];
                    if($type=='Average'){
                        $format_arr=['Mean','Average'];
                    }
                    DataBaseClass::FromTable('Competition',"EndDate<='".$command['Competition_EndDate']."'");       
                    DataBaseClass::Join_current("Event");
                    DataBaseClass::Join_current("DisciplineFormat");
                    DataBaseClass::Join_current("Format");
                    DataBaseClass::Join("DisciplineFormat","Discipline");
                    DataBaseClass::Join("Event","Command");
                    DataBaseClass::Join("Command","Attempt");
                    DataBaseClass::Join("Command","CommandCompetitor");
                    DataBaseClass::Where('Discipline',"ID='".$command['Discipline_ID']."'");    
                    DataBaseClass::Where("A.Special in ('".implode("','",$format_arr)."')");
                    DataBaseClass::Where('A.isDNF = 0');
                    DataBaseClass::OrderClear('Attempt', 'vOrder');
                    DataBaseClass::SelectPre("distinct Com.ID Command, Com.vCountry, A.vOut, A.vOrder,A.Special,C.WCA ");

                    foreach(DataBaseClass::QueryGenerate() as $r){
                        if(!isset($WRecords[$type])){
                            $WRecords[$type]=$r['Command'];
                        }
                        if($r['vCountry'] and !isset($NRecords[$r['vCountry']][$type])){
                            $NRecords[$r['vCountry']][$type]=$r['Command'];
                        }
                    }
                ?>
                <td class="attempt">
                   <?php if(isset($attempts[$type]) and !in_array($attempts[$type],array('DNF','DNS'))){ ?>
                        <nobr>
                           <span class="<?= in_array($attempts_ID[$type],$bestID)?"PB":"" ?>">
                               <?=  $attempts[$type]; ?>
                           </span>
                            <?php if(isset($WRecords[$type]) and  $WRecords[$type]==$command['Command_ID']){ ?>
                                <span class="message">WR</span>
                            <?php }elseif(isset($NRecords[$command['Command_vCountry']][$type]) and  $NRecords[$command['Command_vCountry']][$type]==$command['Command_ID']){ ?>
                                <span class="message">NR</span>
                            <?php } ?>
                    </nobr>        
                   <?php } ?> 
               </td>  
            <?php } ?>
            <?php for($i=sizeof($types);$i<3;$i++){ ?>    
                <td/>
            <?php } ?>
            <td>
                <a href="<?= LinkEvent($command['Event_ID']) ?>">
                    <nobr><?= $command['Competition_Name'] ?> 
                        <?php if($command['Command_Video']){ ?>    
                            <a target=_blank" href="<?= $command['Command_Video'] ?>"><img class="video" src="<?= PageIndex()?>Image/Icons/Video.png"></a>
                        <?php } ?>
                    </nobr>
                </a>
            
            <?php if($discipline['Discipline_Competitors']>1){ ?>
            
                        <?php DataBaseClass::FromTable("Command","ID='".$command['Command_ID']."'") ;
                        DataBaseClass::Join_current("CommandCompetitor");
                        DataBaseClass::Join_current("Competitor");
                        DataBaseClass::Where_current("ID<>".$competitor['Competitor_ID']);
                        $competitiors=DataBaseClass::QueryGenerate();
                        foreach($competitiors as $competitior_com){ ?>
                                <br>
                                <nobr>
                                <?= svg_blue(12,'Teammate') ?>
                                <a href="<?= LinkCompetitor($competitior_com['Competitor_ID'],$competitior_com['Competitor_WCAID'])?>">
                                    <?= Short_Name($competitior_com['Competitor_Name']) ?>      
                                </a>
                                </nobr>      
                        <?php } ?>
                
             <?php }?>
            </td>
            <td class="attempt">
                    <nobr><?= str_replace(": ","",$command['Event_vRound']) ?></nobr> 
            </td>

            <?php if(!$is_attempt){ ?>
                <td  class="future" colspan="<?= $attemption ?>">
                    <nobr><?= date_range($command['Competition_StartDate'], $command['Competition_EndDate']) ?></nobr>
                </td>
            <?php }else{ ?>
                <?php for($i=1;$i<=$attemption;$i++) {?>
                <td class="attempt">
                    <nobr><?= $attempts[$i]; ?></nobr>
                </td>
                <?php } ?>

            <?php } ?>
        </tr>
    <?php } ?>
<?php } ?>
</table>
<?php
DataBaseClass::Query("Select C.Name, C.WCA,MB.ID from MosaicBuilding MB "
        . " join Competition C on C.ID=MB.Competition"
        . " where "
        . " Description like '%".DataBaseClass::Escape(Short_Name($competitor['Competitor_Name']))."%'"
        . " or ( '".$competitor['Competitor_WCAID']."'<>'' and Description like '%". $competitor['Competitor_WCAID']."%')");
$competitions=DataBaseClass::getRows();

if(sizeof($competitions)){ ?>
    <br>
    <img align="center" title="Picture" height=30px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png"> 
    <a href='<?=Pageindex(); ?>Discipline/MosaicBuilding'><b>Mosaic Building</b></a>
    <br>
    <?php foreach($competitions as $competition){ ?>
        <div class="form">
            <h3><a href="<?= PageIndex() ?>Competition/<?= $competition['WCA'] ?>/MosaicBuilding"><?= $competition['Name']?></a></h3>
            <?php DataBaseClass::FromTable("MosaicBuildingImage","MosaicBuilding=".$competition['ID']);
                foreach(DataBaseClass::QueryGenerate() as $file){ ?>
                    <div style="width:300px;height: 200px; float: left; ">
                            <img class="imageSmall" style=" max-height: 100%;max-width: 100%;
                                 display: block; margin: auto;height: auto;
                  " src="<?= PageIndex()?>Image/MosaicBuilding/<?= $file['MosaicBuildingImage_Filename']?>"> 
                    </div>  
            <?php } ?>
        </div>
    <?php } ?>
    <?php include 'MosaicBuilding_Show.php'; ?>
<?php } ?>
