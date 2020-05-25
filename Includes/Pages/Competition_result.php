    
<h2>
    <?php if(sizeof($disciplines)>1){ ?>
        <?= ImageDiscipline($event['Discipline_Code'],50)?>
    <?php } ?>
    <a href="<?= LinkEvent($event['Event_ID'],$event['Event_Round']) ?>"><?= $event['Discipline_Name'] ?><?= $event['Event_vRound'] ?></a> 
    / Results 
    &#9642; <a href="<?= LinkDiscipline(explode(":",$event['Discipline_Code'])[0])?>">Rankings</a>
    <?php if(CheckDelegateCompetition($competition['Competition_ID'],false)){ ?>
        <nobr>&#9642; <a href="<?= LinkEvent($event['Event_ID'])?>/config">Settings</a></nobr>
    <?php } ?>
</h2>

<?php $regulation=regulation_block(['ID'=>$event['Discipline_ID'],'Code'=>$event['Discipline_Code']]); ?>

<?php if($event['Event_Comment'] or $regulation){?>
    <div class="block_comment">
        <?= $regulation ?>
        <?php if($event['Event_Comment'] and $regulation){ 
            if(strpos($event['Event_Comment'],"\n")===false){ ?>
                &#9642;
            <?php }else{ ?>        
                <br>
            <?php } ?>    
        <?php } ?>    
        <?= Echo_format($event['Event_Comment']); ?>
    </div>
<?php } ?>

<div class="block_comment">
<?php
        if($event['Discipline_Competitors']>1){ ?>
            Team has <?= $event['Discipline_Competitors'] ?> competitors &#9642;
        <?php } ?>
    <?= $event['Format_Result'].' of '.$event['Format_Attemption']?>
    <?php if($event['Event_CutoffMinute']+$event['Event_CutoffSecond']>0){ ?>
        &#9642; Cutoff <?= sprintf("%02d:%02d",$event['Event_CutoffMinute'],$event['Event_CutoffSecond'])?>
    <?php } ?>
        &#9642; <?= $event['Event_Cumulative']?"Cumulative limit":"Limit"; ?> <?= sprintf("%02d:%02d",$event['Event_LimitMinute'],$event['Event_LimitSecond'])?>
</div>
<?php

if(!isset($event['Format_ExtResult'])){
    $formats=[$event['Format_Result']];
}else{
    $formats=[$event['Format_Result'],$event['Format_ExtResult']];
}

$WRecords=[];
$NRecords=[];
foreach($formats as $format){
    $format_arr=[$format];
    if(in_array($format,['Mean','Average'])){
        $format_arr=['Mean','Average'];
    }
    DataBaseClass::FromTable('Competition',"EndDate<='".$event['Competition_EndDate']."'");       
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current("DisciplineFormat");
    DataBaseClass::Join_current("Format");
    DataBaseClass::Join("DisciplineFormat","Discipline");
    DataBaseClass::Join("Event","Command");
    DataBaseClass::Join("Command","Attempt");
    DataBaseClass::Join("Command","CommandCompetitor");
    DataBaseClass::Where('Discipline',"ID='".$event['Discipline_ID']."'");    
    DataBaseClass::Where("A.Special in ('".implode("','",$format_arr)."')");
    DataBaseClass::Where('A.isDNF = 0');
    DataBaseClass::OrderClear('Attempt', 'vOrder');
    DataBaseClass::SelectPre("distinct Com.ID Command, Com.vCountry, A.vOut, A.vOrder,A.Special,C.WCA ");
        
    foreach(DataBaseClass::QueryGenerate() as $r){
        if(!isset($WRecords[$format])){
            $WRecords[$format]=$r['Command'];
        }
        if($r['vCountry'] and !isset($NRecords[$r['vCountry']][$format])){
            $NRecords[$r['vCountry']][$format]=$r['Command'];
        }
    }
}

    DataBaseClass::FromTable('Event',"ID='".$event['Event_ID']."'"); 
    DataBaseClass::Join_current('Command');
    DataBaseClass::Where_current('Decline=0');
    DataBaseClass::OrderSpecial('case when Place>0 then Place else 9999 end ');
    DataBaseClass::Order("Command", "vName");
    $commands=DataBaseClass::QueryGenerate(); ?>
        <table class="competition_result">
            <tr class="tr_title"> 
                <td>Place</td>
                <td>Competitor</td>
                <?php for($i=1;$i<=$event['Format_Attemption'];$i++) {?>
                <td class="attempt attempt_header_num">
                    <?php if($image=IconAttempt($event['Discipline_Code'],$i)){ ?>
                        <img src="<?= PageIndex() ?>/<?= $image ?>" width="20px">
                    <?php }else{ ?>
                        <?= $i ?>
                    <?php } ?>
                </td>
                <?php } ?>
                <td class="attempt_result">
                    <?= $event['Format_Result']?>
                </td>
                <?php if($event['Format_ExtResult']){ ?>
                    <td class="attempt">
                        <?= $event['Format_ExtResult']?>
                    </td>
                <?php } ?>
            </tr> 
            <tbody>
        <?php foreach($commands as $command){ 

        DataBaseClass::Query("select * from `Attempt` A where Command='".$command['Command_ID']."' ");
        $attempt_rows=DataBaseClass::getRows();
        $attempts=array();
        for($i=1;$i<=$event['Format_Attemption'];$i++) {
            $attempts[$i]="";
        }
        foreach(DataBaseClass::SelectTableRows("Format") as $format){
            $attempts[$format['Format_Result']]="";    
        }


        foreach($attempt_rows as $attempt_row){
            $attempt=trim($attempt_row['vOut']);
            
            /*if($attempt_row['IsDNF']){
                $attempt='DNF';
            }elseif($attempt_row['IsDNS']){
                $attempt='DNS';
            }elseif($attempt_row['Minute']){
                $attempt=$attempt_row['Minute'].':'.sprintf('%02d', $attempt_row['Second']).".".sprintf('%02d', $attempt_row['Milisecond']);
            }else{
               $attempt=$attempt_row['Second'].".".sprintf('%02d', $attempt_row['Milisecond']);  
            }
            */
            if($attempt_row['Except']){
                $attempt="($attempt)";
            }

            if($attempt_row['Attempt']){
               $attempts[$attempt_row['Attempt']]= $attempt;
            }else{
               $attempts[$attempt_row['Special']]= $attempt; 
            }
        }   
        
            $class=$command['Command_Place']<=3?"podium":""; ?> 
            <tr class="<?= $class ?>">
                <td class="number">
                    <?= $command['Command_Place']?$command['Command_Place']:'' ?>
                </td>
                <td class="result_many_rows">
                <?php 
                 DataBaseClass::Query("select C.* from `Competitor` C "
                         . " join `CommandCompetitor` CC on CC.Competitor=C.ID where CC.Command='".$command['Command_ID']."' order by C.Name");
                 $competitors=DataBaseClass::getRows();   
                 foreach($competitors as $competitor){ ?>
                    <div class="result_many_rows">
                        <a href="<?= LinkCompetitor($competitor['ID'],$competitor['WCAID'])?>">
                            <nobr>
                                <?php 
                                $flag="Image/Flags/".strtolower($competitor['Country']).".png";
                                if(file_exists($flag)){ ?>
                                    <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competitor['Country'])?>.png">
                                <?php }else{ ?>
                                    <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/All.png">
                                <?php } ?>
                                <?= $competitor['Name'] ?>
                            </nobr>       
                        </a>
                    </div>
                    <?php } ?>
                </td>
                <?php for($i=1;$i<=$event['Format_Attemption'];$i++) {?>
                <td  class="attempt" >
                    <nobr><?= $attempts[$i]; ?></nobr>
                </td>
                <?php } ?>
                <td class="attempt_result">
                    <nobr>
                        <?= $attempts[$event['Format_Result']]?>
                        <?php if(isset($WRecords[$event['Format_Result']]) and  $WRecords[$event['Format_Result']]==$command['Command_ID']){ ?>
                            <span class="message">WR</span>
                        <?php }elseif(isset($NRecords[$command['Command_vCountry']][$event['Format_Result']]) and  $NRecords[$command['Command_vCountry']][$event['Format_Result']]==$command['Command_ID']){ ?>
                              <span class="message">NR</span>
                        <?php } ?>
                    </nobr>
                </td>
                <?php if($event['Format_ExtResult']){ ?>
                    <td class="attempt">
                        <?php if(!in_array($attempts[$event['Format_ExtResult']],array('DNF','DNS'))){ ?>
                            <nobr>
                                <?= $attempts[$event['Format_ExtResult']]; ?>
                                <?php if(isset($WRecords[$event['Format_ExtResult']]) and  $WRecords[$event['Format_ExtResult']]==$command['Command_ID']){ ?>
                                    <span class="message">WR</span>
                                <?php }elseif(isset($NRecords[$command['Command_vCountry']][$event['Format_ExtResult']]) and  $NRecords[$command['Command_vCountry']][$event['Format_ExtResult']]==$command['Command_ID']){ ?>
                                    <span class="message">NR</span>
                                <?php } ?>
                            </nobr>
                        <?php }?>
                    </td>
                <?php } ?>
                <?php if($command['Command_Video']){ ?>    
                    <td>
                        <a target=_blank" href="<?=$command['Command_Video'] ?>"><img class="video" src="<?= PageIndex()?>Image/Icons/Video.png"></a>
                    </td>    
                <?php } ?>    
            </tr>

        <?php } ?>
        </tbody>
        </table>
