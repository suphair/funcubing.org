<?php
if(!isset($classes[$event['Event_ID']])){
    $classes[$event['Event_ID']]="";
 } ?>
<?php $Competitor=GetCompetitorData(); ?>
<h2>
    
    <?php if(sizeof($disciplines)>1){ ?>
        <?= ImageDiscipline($event['Discipline_Code'],50)?>
    <?php } ?>
    <?php if($classes[$event['Event_ID']]=='CompetitionRegister'){ ?>
        <?= svg_blue(20,'You are registered'); ?>
    <?php } ?>
    <?php if($classes[$event['Event_ID']]=='RegisterLimit'){ ?>
        <?= svg_red(20,'The limit of competitors for this event has been reached'); ?>
    <?php } ?>
    <?php if($classes[$event['Event_ID']]=='RegisterOpen'){ ?>
        <?= svg_green(20,'Registration is opened'); ?>
    <?php } ?>
    
    <a href="<?= LinkEvent($event['Event_ID']) ?>"><?= $event['Discipline_Name'] ?><?= $event['Event_vRound'] ?></a>
    <?php if($competition['Competition_Registration']!=0){ ?>
        / Register        
    <?php } ?>
        &#9642; <a href="<?= LinkDiscipline($event['Discipline_Code'])?>">Rankings</a>
    <?php if(CheckDelegateCompetition($competition['Competition_ID'],false)){ ?>
        <nobr>&#9642; <a href="<?= LinkEvent($event['Event_ID'])?>/config">Setting</a></nobr>
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
    DataBaseClass::FromTable('Event',"ID='".$event['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    $commands=DataBaseClass::QueryGenerate();
    $isTeamPartly=false;
    if($event['Discipline_Competitors']>1){
        foreach($commands as $command){
            if($command['Command_vCompetitors']<$event['Discipline_Competitors']){
                $isTeamPartly=true;
            }
        }
    } 
        $count=$countCommands[$event['Event_ID']];//count(DataBaseClass::SelectTableRows('Command',"Event='".$event['Event_ID']."'"));
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
    DataBaseClass::FromTable('Event',"ID='".$event['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    if($event['Event_Competitors']<=$count){
        DataBaseClass::Where("Com.vCompetitors=".$event['Discipline_Competitors']);
    }
    $commands=DataBaseClass::QueryGenerate();
?>
<br>
<div class="form">
    <?php if($competition['Competition_Registration']==0){ ?>
        <p class="error">Registration is closed</p>
    <?php } ?>
    <?php if($event['Event_Round']>1){ ?>
        <nobr><?= $event['Event_Competitors'] ?> <?=$event['Discipline_Competitors']>1?'teams':'competitors'; ?></nobr>
    <?php } ?>   
        
    <?php if($competition['Competition_Registration']!=0 and $event['Event_Round']==1){ ?>
        <?php if($event['Event_Competitors']!=500){ ?>            
            <?php if($event['Event_Competitors']<=$count){ ?>
                <nobr><span class="error">The limit of competitors for this event has been reached</span></nobr> &#9642;
                <nobr><?= $count?"$count":'No' ?> <?= ($event['Discipline_Competitors']>1)?'teams':'competitors'; ?></nobr>
            <?php }else{ ?>
                <nobr><span class="message">Registration is opened</span></nobr> &#9642;
                <nobr><?= $count ?> of <?= $event['Event_Competitors']?> <?= ($event['Discipline_Competitors']>1)?'teams':'competitors'; ?></nobr>
            <?php } ?>    
        <?php }else{ ?>
                <span class="message">Registration is opened</span>
        <?php } ?>        
        <?php if($Competitor){
            DataBaseClass::FromTable("Competitor","Name ='".$Competitor->name."'");
            DataBaseClass::Join_current("CommandCompetitor");
            DataBaseClass::Join_current("Command");
            DataBaseClass::Where_current("Event=".$event['Event_ID']);
            $competitorevent_row=DataBaseClass::QueryGenerate(false);

            $CompetitorEvent=$competitorevent_row['Command_ID']; 
            if($CompetitorEvent){ ?>

                <form method="POST" action="<?= PageIndex()."Actions/CompetitionRegistrationDelete" ?>" onsubmit="return confirm('Cancel registration \'<?= $Competitor->name ?> / <?= $Competitor->wca_id ?>\'\n on event \'<?= $event['Discipline_Name'] ?>\'?')"> 
                    <input name="ID" type="hidden" value="<?=  $event['Event_ID'] ?>" />
                    <span class="message"><?= $Competitor->name ?>: Registered</span>
                    <input class="delete" type="submit" value="Cancel registration">  
                    <?php if($event['Discipline_Competitors']>$competitorevent_row['Command_vCompetitors'] ){ ?>
                        <br>Team key <b><?= $competitorevent_row['Command_Secret'] ?></b>
                    <?php } ?>
                    <?php $err=GetMessage("RegistrationDeleteError");
                    if($err){ ?>
                        <br><span class="error"><?= $err?></span>
                    <?php } ?>
                </form>

        <?php }else{ ?>
                <?php if($event['Event_Competitors']>$count){ ?>
                    <form method="POST" action="<?= PageIndex()."Actions/CompetitionRegistration" ?>" onsubmit="return confirm('Register \'<?= $Competitor->name ?> / <?= $Competitor->wca_id ?>\'\n on event \'<?= $event['Discipline_Name'] ?>\'?')"> 
                        <input name="ID" type="hidden" value="<?=  $event['Event_ID'] ?>" />
                        <?= $Competitor->name ?></span>
                        <?php if($event['Discipline_Competitors']==1){ ?>
                            <input class="form_enter" type="submit" value="Register">  
                        <?php }else{ ?>
                            <input class="form_enter" type="submit" value="Create team">  
                        <?php } ?>
                        <?php $err=GetMessage("RegistrationError");
                        if($err){ ?>
                            <br><span class="error"><?= $err?></span>
                        <?php } ?>
                    </form>
                <?php if($isTeamPartly){ ?>
                     <form method="POST" action="<?= PageIndex()."Actions/CompetitionRegistration" ?>" onsubmit="return confirm('To join the team \'<?= $Competitor->name ?> / <?= $Competitor->wca_id ?>\'\n on event \'<?= $event['Discipline_Name'] ?>\'?')"> 
                        <input name="ID" type="hidden" value="<?=  $event['Event_ID'] ?>" />
                        <input type="text" required style="width: 120px;" placeholder="Enter team key" name="Secret" >
                        <input type="submit" value="Join the team">   
                        <?php $err=GetMessage("CompetitionRegistrationKey");
                        if($err){ ?>
                            <br><span class="error"><?= $err?></span>
                        <?php } ?>        
                    </form>
                <?php } ?>       
                <?php } ?> 
          <?php }
        }else{
            if($event['Event_Competitors']>$count){ ?>
                <?php $_SESSION['Refer']=$_SERVER['REQUEST_URI'];  ?>    
                <nobr>&#9642;  <span class="error">To register you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a></span></nobr> 
        <?php }
        }
    }?>
</div>     
<?php  
    $types=array('ExtResult'=>$event['Format_ExtResult'],'Result'=>$event['Format_Result']);  
    $commandsData=array();
    foreach($commands as $command){
        $commandsData[$command['Command_ID']]['DateCreated']=$command['Command_DateCreated'];
        foreach($types as $name=>$type){
            $commandsData[$command['Command_ID']][$name]=array(
                'Competition_Name'=>$event['Competition_Name'],
                'Event'=>$event['Event_ID'],
                'Out'=>''
            ); 
            DataBaseClass::Query("Select C.Name from Competitor C join CommandCompetitor CC on CC.Competitor=C.ID"
                    . " join Command Com on Com.ID=CC.Command "
                    . " where Com.ID=".$command['Command_ID']
                    . " order by C.Name Limit 1");
            $commandsData[$command['Command_ID']]['Name']=DataBaseClass::getRow()['Name'];
            if($command['Command_vCompetitors']==$event['Discipline_Competitors'] or true){
                DataBaseClass::Query("Select * from CommandCompetitor CC where CC.Command=".$command['Command_ID']);
                
                $sql='Select Com.ID from Command Com ';
                foreach(DataBaseClass::getRows() as $competitor){
                    $Competitor_ID=$competitor['Competitor'];
                    $sql.=' join CommandCompetitor CC'.$Competitor_ID.' on CC'.$Competitor_ID.'.Command=Com.ID';
                    $sql.=' join Competitor C'.$Competitor_ID.' on C'.$Competitor_ID.'.ID=CC'.$Competitor_ID.'.Competitor and C'.$Competitor_ID.'.ID='.$Competitor_ID;
                
                }
                DataBaseClass::Query($sql);
                $command_ids=array();
                foreach(DataBaseClass::getRows() as $com){
                    $command_ids[]=$com['ID'];
                }

                DataBaseClass::FromTable('Command',"ID in('".implode("','",$command_ids)."')");
                DataBaseClass::Join_current('Event');
                DataBaseClass::Join_current('DisciplineFormat');
                DataBaseClass::Where_current("Discipline='".$event['Discipline_ID']."'");
                DataBaseClass::Join('Event','Competition');
                DataBaseClass::Join('Command','Attempt');
                DataBaseClass::Where_current("Special='$type'");
                DataBaseClass::OrderClear('Attempt', 'vOrder') ;
                DataBaseClass::Limit('1');
                $result=DataBaseClass::QueryGenerate(false);
                $commandsData[$command['Command_ID']][$name]=array(
                    'Competition_Name'=>$result['Competition_Name'],
                    'Event'=>$result['Event_ID'],
                    'vOut'=>$result['Attempt_vOut'],
                    'vOrder'=>$result['Attempt_vOrder'],
                    'vName'=>$result['Command_vName'],
                    'DateCreated'=>'',
                );
            }
        }
    }    
?>
<?php if(!sizeof($commandsData)){ ?>
<?php }else{ ?>
<h3>Psych Sheet</h3>
    <table>
        <thead>
            <tr>
            <td/>
            <td/>
            <td class="attempt">
                <?= $event['Format_Result']?>
            </td>
            <td/>
            <?php if($event['Format_ExtResult']){ ?>
            <td class="attempt">
                <?= $event['Format_ExtResult']?>
            </td>
            <?php } ?>
            <td/>
        </tr> 
    </thead>
    <?php 
    $n=0;
   
    uasort($commandsData,'SortCommandOrder');
    
    foreach($commandsData  as $commandID=>$command){ ?>
        <tr class=""
            onmouseover="this.className='competitor_block_select';"
            onmouseout=" this.className='';">
            <td  class="number">
            <?php if($command['Result']['vOut'] or (isset($command['ExtResult']['vOut']) and $command['ExtResult']['vOut'])){ ?>
                <?= ++$n ?>
            <?php } ?>    
            </td>
            <td><?php   
             DataBaseClass::FromTable("Command","ID=".$commandID);
             DataBaseClass::Join_current("CommandCompetitor");
             DataBaseClass::Join_current("Competitor");
             DataBaseClass::OrderClear("Competitor","Name");
             $competitors=DataBaseClass::QueryGenerate();
            for($i=0;$i<$event['Discipline_Competitors'];$i++){ 
                if(isset($competitors[$i])){
                    $name= Short_Name($competitors[$i]['Competitor_Name']); ?>
                     <div class="result_many_rows"><a class="pos" href="<?= LinkCompetitor($competitors[$i]['Competitor_ID'],$competitors[$i]['Competitor_WCAID'])?>">
                         <nobr>
                            <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competitors[$i]['Competitor_Country'])?>.png">
                            <?= $name ?>
                         </nobr>
                     </a></div>
                <?php }else{ ?>
                  <div class="result_many_rows"><?= svg_red(10,'Need add teammate'); ?></span></div>
                <?php }
            } ?>
            </td>
            <td  class="attempt">
                <?= $command['Result']['vOut']; ?>    
            </td>
            <td>
                <?php if($command['Result']['Competition_Name']){ ?>
                <a href="<?= LinkEvent($command['Result']['Event']) ?>">
                    <nobr><?= $command['Result']['Competition_Name'] ?></nobr>
                </a>
                <?php } ?>
            </td>
            
            <?php if($event['Format_ExtResult'] and isset($command['ExtResult']) and !in_array($command['ExtResult']['vOut'],array('DNF','DNS'))){ ?>
                <td  class="attempt">
                    <?= $command['ExtResult']['vOut']; ?> 
                </td>
                <td>
                    <?php if($command['ExtResult']['Competition_Name']){ ?>
                    <a href="<?= LinkEvent($command['ExtResult']['Event']) ?>">
                        <nobr><?= $command['ExtResult']['Competition_Name'] ?></nobr>
                    </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </table>
<?php } ?>

