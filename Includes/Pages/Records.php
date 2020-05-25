<?php

if(isset($_GET['Country'])){
    $country_filter= DataBaseClass::Escape($_GET['Country']);
}else{
    $country_filter='0';    
}    
    
$DisciplineCode='';
if(isset($_GET['Discipline'])){
    $DisciplineCode=DataBaseClass::Escape($_GET['Discipline']);
}

DataBaseClass::FromTable("Discipline","Code='".$DisciplineCode."'");
DataBaseClass::Join_current("DisciplineFormat");
DataBaseClass::Join_current("Format");

$DiscipineFilter= DataBaseClass::QueryGenerate(false);
DataBaseClass::FromTable('Discipline'); 
DataBaseClass::Where_current("Status='Active'");
$disciplines=DataBaseClass::QueryGenerate(); ?>
<div class="line">
    <a class="<?= (!$DisciplineCode and !$country_filter)?"line_select":""?>" title="World records" href="<?= PageIndex()?>?Records"><?= ImageCountry('', 50); ?></a>
    <?php if($country_filter){ ?>
        <a class="<?= !$DisciplineCode?"line_select":""?>" title="National records <?= CountryName($country_filter) ?>" href="<?= PageIndex()?>?Records&Country=<?= $country_filter?>"><?= ImageCountry($country_filter, 50); ?></a>
    <?php } ?>
    <?php foreach($disciplines as $discipline_row){ ?>   
        <a class="<?= $discipline_row['Discipline_Code']==$DisciplineCode?"line_select":""?>" title="<?= $discipline_row['Discipline_Name'] ?>" href="<?= PageIndex()?>?Records&Discipline=<?= $discipline_row['Discipline_Code']?>&Country=<?= $country_filter?>"><?= ImageDiscipline($discipline_row['Discipline_Code'],50) ?></a> 
    <?php } ?>
</div>
<hr>
<?php
    DataBaseClass::FromTable('Competition');   
    DataBaseClass::OrderClear('Competition', 'EndDate');
    $competitions= DataBaseClass::QueryGenerate();
    $res=array(); 
    $results=array();
    $formats=array();
    
    foreach($competitions as $competition){      
        DataBaseClass::FromTable("Competition","ID='".$competition['Competition_ID']."'");
        DataBaseClass::Join_current("Event");
        DataBaseClass::Join_current("DisciplineFormat");
        DataBaseClass::Join_current("Format");
        DataBaseClass::Join("DisciplineFormat","Discipline");
        DataBaseClass::Join("Event","Command");
        DataBaseClass::Join("Command","Attempt");
        DataBaseClass::Join("Command","CommandCompetitor");
        DataBaseClass::Join("CommandCompetitor","Competitor");
        if($DisciplineCode){
            DataBaseClass::Where('Discipline',"Code='$DisciplineCode'");    
        }
        DataBaseClass::Where('A.Special in (F.Result,F.ExtResult)');
        DataBaseClass::Where('A.isDNF = 0');
        if($country_filter){
            DataBaseClass::Where('Command',"vCountry='$country_filter'");    
        }
        DataBaseClass::OrderClear('Discipline', 'Code');
        DataBaseClass::Order('Attempt', 'vOrder');
        foreach(DataBaseClass::QueryGenerate() as $n=>$row){
            $formats[$row['Attempt_Special']]=1;
            $MS=$row['Attempt_vOrder'];
            $row['Attempt_Special']=str_replace('Mean','Average',$row['Attempt_Special']);
            if(!isset($cuts[$row['Discipline_Code']][$row['Attempt_Special']])
                or $MS<$cuts[$row['Discipline_Code']][$row['Attempt_Special']]){
                    $cuts[$row['Discipline_Code']][$row['Attempt_Special']]=$MS;
                $results[$competition['Competition_EndDate']][]=$row;
            }
        }
    }
    
    $results= array_reverse($results);
    
    foreach($results as $n=>$comp){        
        $results[$n]=array_reverse($comp);
    } 
       
    $countries=array();
    DataBaseClass::FromTable("Command");
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current("DisciplineFormat");
    DataBaseClass::Join_current("Discipline");
    DataBaseClass::Join("DisciplineFormat","Format");
    DataBaseClass::Join("Command","Attempt");
    DataBaseClass::Where('A.Special in (F.Result,F.ExtResult)');
    DataBaseClass::Where('A.isDNF = 0');
    if($DisciplineCode){
        DataBaseClass::Where("Discipline","Code='".$DisciplineCode."'");
    }
    foreach(DataBaseClass::QueryGenerate() as $country){
        if($country['Command_vCountry'] and !in_array($country['Command_vCountry'],$countries)){
            $countries[]=$country['Command_vCountry'];
        }
    }
    sort($countries); ?>
<h2>
    <?php if($country_filter){ ?>
        <?= ImageCountry($country_filter, 50); ?>
        <nobr>History of records <?= CountryName($country_filter)?></nobr>
    <?php }else{ ?>
        <nobr>History of world records</nobr>
    <?php } ?>
        
     <select onchange="document.location='<?= PageIndex()?>' + this.value ">
        <option <?= ($country_filter=='0' )?'selected':''?> value="?Records">World records</option>
        <option disabled >&#9642; National records</option>
        <?php if(!in_array($country_filter,$countries) and $country_filter){ ?>
            <option selected><?= CountryName($country_filter) ?> [<?= $country_filter ?>] - no result</option>
        <?php } ?>
        <?php foreach($countries as $country){ ?>
                <option <?= $country_filter==$country?'selected':''?> value="?Records&Country=<?= $country?>">        
                    <?= CountryName($country) ?> [<?= $country ?>]
                </option> 
        <?php } ?>      
    </select>   
    <?php if($DisciplineCode){ ?>
        <br>
        <nobr><?= ImageDiscipline($DiscipineFilter['Discipline_Code'],50) ?> 
        <a href="<?= LinkDiscipline($DiscipineFilter['Discipline_Code']) ?>"><?=$DiscipineFilter['Discipline_Name']?></a></nobr>
    <?php } ?>            
</h2>
<?php if(sizeof($results)){ ?>
<table class="Records">
                <tr class="tr_title">
                <td>Date</td>
                <?php if(!$DisciplineCode){ ?>
                    <td>Event</td>
                <?php } ?>
                <td>Single</td>
                <td>Average</td>
                <td>Competitor</td>
                <td>Competition</td>
            </tr>
<?php $record_out=array();
    foreach($results as $date=>$comp){
        foreach($comp as $ci=>$c){ ?>
            <tr>
                <td>
                    <?= date_range($c['Competition_EndDate'],$c['Competition_EndDate']); ?>
                </td> 
                <?php if(!$DisciplineCode){ ?>
                <td>
                    <?= ImageDiscipline($c['Discipline_Code'],30,$c['Discipline_Name']); ?> 
                    <a href="<?= LinkDiscipline($c['Discipline_Code']) ?>">
                        <?= $c['Discipline_Name'] ?>
                    </a>
                </td>
                <?php } ?>

                <?php $class="";
                    if(!in_array($c['Discipline_ID'].'_'.$c['Attempt_Special'],$record_out)){
                        $record_out[]=$c['Discipline_ID'].'_'.$c['Attempt_Special'];
                        $class="message";
                    } ?>   
                <td class="attempt border-left-solid border-right-solid">
                    <?php if(in_array($c['Attempt_Special'],array('Best','Sum'))){ ?>
                         <span class="<?= $class ?>"><?= $c['Attempt_vOut'] ?></span>
                     <?php } ?>
                </td>

                <td class="attempt border-left-solid border-right-solid">
                    <?php if(!in_array($c['Attempt_Special'],array('Best','Sum'))){ ?>
                         <span class="<?= $class ?>"><?= $c['Attempt_vOut'] ?></span>
                     <?php } ?>
                </td>
                <td>
                    <?php 
                    DataBaseClass::FromTable("Command","ID=".$c['Command_ID']);
                    DataBaseClass::Join_current("CommandCompetitor");
                    DataBaseClass::Join_current("Competitor");
                    DataBaseClass::OrderClear("Competitor","Name");
                    $competitors=DataBaseClass::QueryGenerate();
                    foreach($competitors as $competitor){ ?>
                        <p>
                            <img width="30" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competitor['Competitor_Country'])?>.png">
                            <a href="<?= PageIndex() ?>Competitor/<?= $competitor['Competitor_WCAID']?$competitor['Competitor_WCAID']:$competitor['Competitor_ID'] ?>"><?= trim(explode("(",$competitor['Competitor_Name'])[0]) ?></a></p>
                    <?php } ?>

                </td>
                <td>
                    <img width="30" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($c['Competition_Country'])?>.png">
                    <a href="<?= LinkCompetition($c['Competition_WCA']) ?>">
                        <?= $c['Competition_Name'] ?>
                    </a>
                </td>
                <?php if($c['Command_Video']){ ?>    
                    <td>
                        <a target=_blank" href="<?= $c['Command_Video'] ?>"><img class="video"  src="<?= PageIndex()?>Image/Icons/Video.png"></a>
                    </td>    
                <?php } ?>
            </tr>
        <?php }
    } ?>
    </table>
<?php }else{ ?>
<div class="form2"><span class="error">No result</span></form>
<?php } ?>
 </div>