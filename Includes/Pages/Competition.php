<?php
DataBaseClass::FromTable('Competition',"WCA='".RequestClass::getParam1()."'");
$competition=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join_current('CompetitionDelegate');
DataBaseClass::Join_current('Delegate');
DataBaseClass::OrderClear('Delegate','Name');
$delegates=DataBaseClass::QueryGenerate();
?>

<?php # include 'Competitions_Line.php'; ?>


<?php $Event=RequestClass::getParam2();?>
<?php $round=RequestClass::getParam3();
if(!$round){
    $round=1;
} ?>


<?php
        DataBaseClass::FromTable('Competition',"ID='".$competition['Competition_ID']."'");
        DataBaseClass::Join_current('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Join_current('Discipline');
        DataBaseClass::OrderClear('Discipline','Name');
        DataBaseClass::Join('DisciplineFormat','Format');
        if($Event){
            DataBaseClass::Where('Event',"ID=$Event");    
        }

        $event=DataBaseClass::QueryGenerate(false);
        if(isset($event['Event_ID'])){
            $Event=$event['Event_ID'];
        }
  ?> 
<?php include 'CompetitionHeader.php'; ?>
<?php

if(isset(getRequest()[2]) and getRequest()[2]==='MosaicBuilding'){
    $MosaicBuilding=true;    
}else{
    $MosaicBuilding=false;    
} 

if($MosaicBuilding){
    $Event=false;
}
?>

<?php
DataBaseClass::FromTable('Event',"Competition='".$competition['Competition_ID']."'");
DataBaseClass::Join('Event', 'DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::OrderClear('Discipline','Name');
DataBaseClass::Order('Event','Round');
$disciplines=DataBaseClass::QueryGenerate();
$Competitor=GetCompetitorData();            
?>
<?php
if(!sizeof($disciplines)){
    $MosaicBuilding=true;
}
if(sizeof($disciplines) or $competition['Competition_EventPicture']){ ?>
<?php if((sizeof($disciplines) + $competition['Competition_EventPicture']>1) or true){?>
<div class="line discipline_line">
    <?php $rounds_out=array();
    $countCommands=array();
    $classes=[];
    foreach($disciplines as $discipline_row){
        $event_id=$discipline_row['Event_ID']; ?>
        <nobr>

            <?php
            
            DataBaseClass::Query("Select coalesce(sum(case Com.Decline when 0 then 1 else 0 end),0) Commands, count(A.ID)+0 Attempts"
                    . " from Command Com "
                    . " join Event E on E.ID=Com.Event "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
                    . " join Discipline D on D.ID=DF.Discipline "
                    . " left outer join Attempt A on A.Attempt=1 and A.Command=Com.ID"
                    . " where Event=$event_id and Com.vCompetitors=D.Competitors");
                $data=DataBaseClass::getRow();
                $countCommands[$event_id]=$data['Commands'];
                $countAttempts=$data['Attempts'];
            
            $class="";    
                if($competition['Competition_Registration']==1){
                        $class="RegisterOpen";
                }
                
                if($countCommands[$event_id]>=$discipline_row['Event_Competitors'] and $competition['Competition_Registration']==1 ){
                    $class="RegisterLimit";
                }
                
                DataBaseClass::FromTable("Command","Event=".$discipline_row['Event_ID']);
                DataBaseClass::Join_current("CommandCompetitor");
                DataBaseClass::Join_current("Competitor");
                
                if($Competitor){
                    DataBaseClass::Where("Competitor","WID='".$Competitor->id."'");

                    if(DataBaseClass::QueryGenerate(false)['Command_ID']){
                        $class="CompetitionRegister";
                    }
                } 
                            
                if($countAttempts or strtotime($competition['Competition_StartDate'])<=time() or $discipline_row['Event_Round']>1){
                    $class="";
                }
                
                $classes[$discipline_row['Event_ID']]=$class;
                ?>
            
                <?= ImageDiscipline($discipline_row['Discipline_Code'],30) ?>  
                <a class="<?= $discipline_row['Event_ID']==$Event?"list_select":""?> "  href="<?= LinkEvent($discipline_row['Event_ID'],$discipline_row['Event_Round']) ?>">
                    <?= $discipline_row['Discipline_Name'] ?><?= $discipline_row['Event_vRound'] ?>
                </a>
                <span class="badge">
                    <?php if($class=='CompetitionRegister'){ ?>
                        <?= svg_blue(10,'You are registered'); ?>
                    <?php } ?>
                    <?php if($class=='RegisterLimit'){ ?>
                        <?= svg_red(10,'The limit of competitors for the event has been reached'); ?>
                    <?php } ?>
                    <?php if($class=='RegisterOpen'){ ?>
                        <?= svg_green(10,'Registration is opened'); ?>
                    <?php } ?>
                    <?php if($attempts_exists and $countAttempts!=$countCommands[$event_id]){ ?>
                        <?= $countAttempts ?> / <?= $countCommands[$event_id] ?>
                    <?php }else{ ?>
                        <?php if(!$attempts_exists and $countCommands[$event_id]<$discipline_row['Event_Competitors'] and $competition['Competition_Registration']==1){ ?>
                            <?= $countCommands[$event_id] ?> / <?= $discipline_row['Event_Competitors']==500?'*':$discipline_row['Event_Competitors'] ?>
                        <?php }else{ ?>
                            <?= $countCommands[$event_id] ?>
                        <?php } ?>
                    <?php } ?>
                </span>
        </nobr>
    <?php }
    if ($competition['Competition_EventPicture']){ ?>
           <nobr><img align="center" title="Picture" height=30px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
               <?php if($MosaicBuilding){ ?>
                    <span class="list_select">
                        Mosaic Building
                    </span> 
               <?php }else{ ?>
                <a  href="<?= PageIndex() ?>Competition/<?= $competition['Competition_WCA'] ?>/MosaicBuilding">
                     Mosaic Building
                </a> 
               <?php } ?>
           </nobr>
    <?php } ?>
</div>
      <hr class="hr_round"> 
<?php } ?>      
      
      
      
<?php if($MosaicBuilding){
        include 'Competition_MosaicBuilding.php';
    }else{
        if ($attempts_exists){
            include 'Competition_result.php';
        }else{
            include 'competition_psych_sheet.php'; ?>
    <?php } ?>     
<?php } ?>
<?php } ?>