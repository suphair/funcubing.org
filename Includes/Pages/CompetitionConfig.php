<?php
DataBaseClass::FromTable('Competition',"WCA='".RequestClass::getParam1()."'");
$competition=DataBaseClass::QueryGenerate(false);
DataBaseClass::Join_current('CompetitionDelegate');
DataBaseClass::Join_current('Delegate');
$delegates=DataBaseClass::QueryGenerate();
?>
<?php include 'CompetitionHeader.php'; ?>


<?php   
    DataBaseClass::FromTable('Scramble');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Where('Event','Competition='.$competition['Competition_ID']);
    $scrambles=[];
    
    foreach(DataBaseClass::QueryGenerate() as $row){
        if($row['Scramble_Timestamp']){
            if(!isset($scrambles[$row['Discipline_Code']."_".$row['Event_Round']])){
                $Get_ID=$row['Event_ID'];
                include('Includes/Actions/EventPrintScrambles.php');
                $scrambles[$row['Discipline_Code']."_".$row['Event_Round']]=$file;
            }
        }
    }

    DataBaseClass::FromTable('Event');
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Where('Event','Competition='.$competition['Competition_ID']);
    foreach(DataBaseClass::QueryGenerate() as $row){    
        $file="Image/Scramble/Hard_".md5($row['Event_ID'].GetIni('PASSWORD','admin')).".pdf";
        if (file_exists($file)){ 
            $scrambles[$row['Discipline_Code']."_".$row['Event_Round']]=$file;
        }
    }
    
    
    $zip = new ZipArchive();
    $zip_name="Image/Scramble/".$competition['Competition_WCA']."_".md5($competition['Competition_ID'].GetIni('PASSWORD','admin')).".zip";
    @unlink($zip_name);
    $zip->open($zip_name, ZIPARCHIVE::CREATE);
    foreach($scrambles as $name=>$scramble){
        $zip->addFile($scramble,$competition['Competition_WCA']."_".$name.".pdf");
    }
    
    $zip->close(); ?>
                    

<br><a href="<?= PageIndex() ?>Actions/CompetitorsCompetitionCheck/<?= $competition['Competition_ID'] ?>">To check the registration on WCA</a>  &#9642; <a target="_blank" href="https://www.worldcubeassociation.org/competitions/<?= $competition['Competition_WCA'] ?>/registrations">Registrations on WCA</a>  &#9642; <?= ($competition['Competition_CheckDateTime'])?$competition['Competition_CheckDateTime']:"initial load" ?>
<br>Autoloading persons from WCA for on-site registration &#9642; 
<?php if($competition['Competition_LoadDateTime']){ ?>
    <?= $competition['Competition_LoadDateTime'] ?>
<?php }else{  ?>
    no load
<?php } ?>
<?php if(CheckAdmin()){ ?>
   &#9642; <a href="<?= PageIndex() ?>Actions/CompetitorsCompetitionLoad/<?= $competition['Competition_ID'] ?>">reload</a>
<?php } ?>
<br><a target="_blank" href="<?= PageIndex()?>Actions/CompetitionPrintCompetitors/?ID=<?= $competition['Competition_ID'] ?>">Print the list of competitors</a>
<?php if(sizeof($scrambles)){ ?>
    <br><a href="<?= PageIndex().$zip_name ?>">Download zip with all scrambles</a>
<?php } ?>
<br><a target="_blank" href="<?= PageIndex()?>Actions/CompetitionPrintPedestal/?ID=<?= $competition['Competition_ID'] ?>">Print pedestal</a>
<br><a target="_blank" href="<?= PageIndex()?>Actions/CompetitionExport/?ID=<?= $competition['Competition_ID'] ?>">Export resuts</a><br>


    <div class="form">
            <b>Comment for Competition</b>
            <form method="POST" action="<?= PageIndex()."Actions/CompetitionAddComment" ?>">
                <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
                <textarea name="Comment" style="height: 200px;width: 200px"><?= $competition['Competition_Comment']; ?></textarea><br>
                <input type="submit" name="submit" value="Add Comment">
            </form> 
    </div>
<?php   

    DataBaseClass::Query(" select count(distinct E.ID) Rounds,D.ID Discipline_ID from "
            . " Discipline D "
            . " left outer join DisciplineFormat DF on D.ID=DF.Discipline"
            . " left outer join Event E on E.DisciplineFormat=DF.ID and E.Competition=".$competition['Competition_ID']
            //. " where D.Status='Active'"
            . " group by D.ID");
            
    foreach(DataBaseClass::getRows() as $discipline){
        $rounds[$discipline['Discipline_ID']]=$discipline['Rounds']+1;
    }
    
    
    DataBaseClass::FromTable('Event',"Competition='".$competition['Competition_ID']."'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format'); 
    DataBaseClass::OrderClear('Discipline','Name');
    DataBaseClass::Order('Event','Round');
foreach(DataBaseClass::QueryGenerate() as $event){   
    $commands=count(DataBaseClass::SelectTableRows('Command',"Event='".$event['Event_ID']."' and vCompetitors=".$event['Discipline_Competitors']));
    DataBaseClass::FromTable('Command',"Event='".$event['Event_ID']."'");
    DataBaseClass::Join_current('Attempt');
    $attemps=count(DataBaseClass::QueryGenerate());
    ?>
    <div class="form">
        <form method="POST" action="<?= PageIndex()."Actions/EventAction" ?>" onsubmit="return confirm('Attention: Confirm the action.')">
        <input name="ID" type="hidden" value="<?= $event['Event_ID'] ?>" />
        
        <table>
            <tr>
                <td style='border:0px'>
                    <?= ImageDiscipline($event['Discipline_Code'],40)?>
                </td>
                <td style='border:0px'>
                    <?php if(isset($scrambles[$event['Event_ID']])){ ?>
                        <?= svg_green(10,'Scrambles ready')?>
                    <?php } ?>
                    <b><?= $event['Discipline_Name']; ?></b>
                    <br>
                    <?php if($event['Event_vRound']){ ?>
                        <?= str_replace(": ","",$event['Event_vRound']); ?><br>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <hr>
        <nobr>
            <?= $event['Format_Result'] ?> of <?= $event['Format_Attemption'] ?>
            <?php if($event['Discipline_Competitors']>1){ ?>&#9642; Team has <?= $event['Discipline_Competitors'] ?> competitors</nobr><?php } ?>
        </nobr>
        <br>
        <nobr>    
            <?= html_spellcount($event['Event_Groups'],'group','groups','groups'); ?> &#9642;
            <input class="small_input" ID="Groups" size=1  name="Groups" required type="number" step="1" min="1" max="6" value="<?= $event['Event_Groups'] ?>" /> 
        </nobr>
        <br>
        <nobr>
            Cutoff - 
            <?php if($event['Event_CutoffMinute'] or $event['Event_CutoffSecond']){ ?>
                 <?= sprintf("%02d:%02d",$event['Event_CutoffMinute'],$event['Event_CutoffSecond']);  ?>
           <?php }else{ ?>
                no
           <?php } ?>
            &#9642;
            <input class="small_input" ID="CutoffMinute" size=2  name="CutoffMinute" required type="number" step="1" min="0" max="60" value="<?=$event['Event_CutoffMinute'] ?>" /> :
            <input class="small_input" ID="CutoffSecond" size=2  name="CutoffSecond" required type="number" step="1" min="0" max="59" value="<?= $event['Event_CutoffSecond'] ?>" />        
        </nobr>
        <br>
        <nobr>
            Limit - <?= sprintf("%02d:%02d",$event['Event_LimitMinute'],$event['Event_LimitSecond']); ?>   
            &#9642; 
            <input class="small_input" ID="LimitMinute" size=2 name="LimitMinute" required type="number" step="1" min="0" max="60" value="<?= $event['Event_LimitMinute'] ?>" /> :
            <input class="small_input" ID="LimitSecond" size=2  name="LimitSecond" required type="number" step="1" min="0" max="59" value="<?= $event['Event_LimitSecond'] ?>" />        
        </nobr>
        <br>
        Cumulative limit <?= $event['Event_Cumulative']?'+':'' ?> <input type="checkbox" name="Cumulative" <?= $event['Event_Cumulative']?'checked':'' ?>><br>
        <nobr>
            <?= $commands ?> of <?= $event['Event_Competitors']?> <?= ($event['Discipline_Competitors']>1)?'teams':'competitors'; ?>
            &#9642; 
            <input class="small_input" ID="Competitors" size=1  name="Competitors" required type="number" step="1" min="1" max="500" value="<?= $event['Event_Competitors']; ?>" /> 
         </nobr>
        <br>
        <?php if(!$attemps){ 
            DataBaseClass::FromTable("DisciplineFormat","Discipline=".$event['Discipline_ID']);
            DataBaseClass::Join_current("Format");
            $formats=DataBaseClass::QueryGenerate();?>
            Format <select style="width: 120px" name="Format">
                <?php foreach($formats as $format){ ?>
                    <option <?= $format['DisciplineFormat_ID']==$event['DisciplineFormat_ID']?'selected':'' ?> value="<?=$format['DisciplineFormat_ID'] ?>"><?= $format['Format_Result']?> of <?= $format['Format_Attemption']?></option>
                <?php } ?>
            </select>
                <br>
        <?php }else{ ?>
                <input hidden name="Format" value="<?= $event['DisciplineFormat_ID'] ?>">
        <?php } ?>
        <?php  DataBaseClass::FromTable("Command","Event=".$event['Event_ID']);
            DataBaseClass::Join_current("CommandCompetitor");
            DataBaseClass::Where_current("CheckStatus=0");
            if(sizeof(DataBaseClass::QueryGenerate())){ ?>
                <nobr>
                  <span class="error"><?= html_spellcount(sizeof(DataBaseClass::QueryGenerate()),'registration','registrations','registrations') ?> are not on the WCA</span>
                </nobr>  
                <br>
            <?php } ?>
        <nobr>
            <input name="EventButton" id="EventButton" class=""  type="submit" value="Change">
            <?php if(!$commands and $event['Event_Round']==$rounds[$event['Discipline_ID']]-1){ ?>
                <input name="EventButton" id="EventButton" class="delete"  type="submit" value="Delete">
            <?php } ?>    
        </nobr>  
        </form>
    </div>
<?php } ?>
    <div class="form">
        <b>Add events</b>
        <hr>
        <form method="POST" action="<?= PageIndex()."Actions/EventAction" ?>" onsubmit="return confirm('Attention: Confirm the action.')">
            <input name="Competition" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
            <select required="" style="width:400px" Name="DisciplineFormat" data-placeholder="Choose event" class="chosen-select" multiple>
            <option value=""></option>
            <?php DataBaseClass::FromTable("Discipline","Status='Active'");
            DataBaseClass::Join_current("DisciplineFormat");
            DataBaseClass::Join_current("Format");
            foreach(DataBaseClass::QueryGenerate() as $discipline){ ?><?php
                if($rounds[$discipline['Discipline_ID']]<=4){?>
                    <option value="<?= $discipline['DisciplineFormat_ID'] ?>">
                        <?= $discipline['Discipline_Name'] ?> &#9642; <?= substr($discipline['Format_Result'],0,1) ?>o<?= $discipline['Format_Attemption'] ?> <?php 
                        if($rounds[$discipline['Discipline_ID']]>1){ 
                            echo '&#9642; '.array("","1","2","3","4")[$rounds[$discipline['Discipline_ID']]].' round' ;
                        } ?> 
                    </option>
                <?php } ?>    
            <?php } ?>
        </select>
            <br>
            Groups &#9642;
            <input class="small_input" ID="Groups" size=1  name="Groups" required type="number" step="1" min="1" max="6" value="2" /> 
            <br>
            Cutoff &#9642;  
            <input class="small_input" ID="CutoffMinute" size=2  name="CutoffMinute" required type="number" step="1" min="0" max="60" value="0" /> :
            <input class="small_input" ID="CutoffSecond" size=2  name="CutoffSecond" required type="number" step="1" min="0" max="59" value="0" />        
            <br>
            Limit &#9642; 
            <input class="small_input" ID="LimitMinute" size=2 name="LimitMinute" required type="number" step="1" min="0" max="60" value="10" /> :
            <input class="small_input" ID="LimitSecond" size=2  name="LimitSecond" required type="number" step="1" min="0" max="59" value="0" />        
            <br>
            Cumulative limit <input type="checkbox" name="Cumulative">
            <br>
            Teams / Competitors
            &#9642; 
            <input class="small_input" ID="Competitors" size=1  name="Competitors" required type="number" step="1" min="1" max="500" value="500" />
            <br><input name="EventButton" id="EventButton" class=""  type="submit" value="Create">
            <br>
        </form>
    </div>
</body>
</table>
<br>
<div class="form">
    <form method="POST" action="<?= PageIndex()."Actions/CompetitionReload" ?>">
    <input name="WCA" type="hidden" value="<?=  $competition['Competition_WCA'] ?>" />
WCA &#9642; <a href="https://www.worldcubeassociation.org/competitions/<?= $competition['Competition_WCA'] ?>"><?=  $competition['Competition_WCA'] ?></a><br>
Name &#9642; <?=  $competition['Competition_Name'] ?><br>
City &#9642; <?=  $competition['Competition_City'] ?><br>
Country &#9642; <?=  $competition['Competition_Country'] ?> (<?=  CountryName($competition['Competition_Country']) ?>)<br>
StartDate &#9642; <?=  $competition['Competition_StartDate'] ?><br>
EndDate &#9642; <?=  $competition['Competition_EndDate'] ?><br>
WebSite &#9642; <a href="<?=  $competition['Competition_WebSite'] ?>"><?=  $competition['Competition_WebSite'] ?></a>
<br><input type="submit" name="submit" value="Reload information from WCA">
    </form>
</div>
    <?php
    $Registration=array(1=>'Online registration enabled',0=>'Online registration is disabled');
    $Onsite=array(1=>'On-site registration enabled',0=>'On-site registration is disabled');
    $Status=array(0=>'Competition are hidden',1=>'Competition are displayed');
    ?>
    <div class="form">
            <form method="POST" action="<?= PageIndex()."Actions/CompetitionChange" ?>">
                <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
                <?php if($competition['Competition_Status']){ ?>
                    <?= svg_green(10,$Status[$competition['Competition_Status']]) ?>
                <?php } ?>
                <?= $Status[$competition['Competition_Status']]  ?><br>
                <select name="Status">
                    <?php foreach($Status as $n=>$v){?>
                    <option <?= ($n==$competition['Competition_Status'])?"selected":"" ?> value="<?= $n ?>" ><?= $v ?></option>
                    <?php } ?>
                </select><br>
                <?php if($competition['Competition_Registration']){ ?>
                    <?= svg_green(10,$Registration[$competition['Competition_Registration']]) ?>
                <?php } ?>
                <?= $Registration[$competition['Competition_Registration']]  ?><br>
                <select name="Registration">
                    <?php foreach($Registration as $n=>$v){?>
                    <option <?= ($n==$competition['Competition_Registration'])?"selected":"" ?> value="<?= $n ?>" ><?= $v ?></option>
                    <?php } ?>
                </select><br>
                <?php if($competition['Competition_Onsite']){ ?>
                    <?= svg_green(10,$Onsite[$competition['Competition_Onsite']] ) ?>
                <?php } ?>
                <?= $Onsite[$competition['Competition_Onsite']]  ?><br>
                <select name="Onsite">
                    <?php foreach($Onsite as $n=>$v){?>
                    <option <?= ($n==$competition['Competition_Onsite'])?"selected":"" ?> value="<?= $n ?>" ><?= $v ?></option>
                    <?php } ?>
                </select><br>
                <input type="submit" name="submit" value="Change setting">
            </form> 
    </div> 
 
<?php if(CheckAdmin()){
    
    ?>
<div class="form">      
    <form method="POST" action="<?= PageIndex()."Actions/CompetitionDelegateDelete" ?>">
        <input name="Competition" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
        <?php foreach($delegates as $delegate){?>
                <?= $delegate['Delegate_Name'] ?><br>
        <?php } ?>
    </form>
    <form method="POST" action="<?= PageIndex()."Actions/CompetitionEdit" ?>">
        <input name="Competition" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
        <?php 
        $delegates_from_table = DataBaseClass::SelectTableRows('Delegate');
        foreach($delegates as $i=>$delegate){?>
            <select name="Delegates[<?= $i ?>]">
                <option value="">-</option>
            <?php foreach($delegates_from_table as $delegate_from_table){ ?>
                <option <?= $delegate['Delegate_ID']==$delegate_from_table['Delegate_ID']?"selected":"" ?> value="<?= $delegate_from_table['Delegate_ID'] ?>">
                    <?= $delegate_from_table['Delegate_Status']=='Archive'?'- ':'' ?>
                    <?= $delegate_from_table['Delegate_Name'] ?>
                </option>
            <?php } ?>
            </select>
            <br>
         <?php } ?>
        <select name="Delegates['+']">
            <option value="">+</option>
            <?php foreach($delegates_from_table as $delegate_from_table){ ?>
                <option value="<?= $delegate_from_table['Delegate_ID'] ?>">
                    <?= $delegate_from_table['Delegate_Status']=='Archive'?'- ':'' ?>
                    <?= $delegate_from_table['Delegate_Name'] ?>
                </option>            
            <?php } ?>
        </select>    
        <br>
        <input  class="form_change"  type="submit" value="Change Judge">
    </form>  
    <?php 
    DataBaseClass::FromTable('Event',"Competition='".$competition['Competition_ID']."'");
            if (sizeof(DataBaseClass::QueryGenerate())==0){ ?>
            <form method="POST" action="<?= PageIndex()."Actions/CompetitionDelete" ?>"   onsubmit="return confirm('Attention: Confirm the delete.')">
                <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
                <input class="delete"  type="submit" value="Delete competition">
            </form>
    <?php  } ?>
    </div> 
<?php } ?>
<div class="form">
    <?= ImageCompetition($competition['Competition_WCA']) ?> 
    <form name="LoadCompetitionImage" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/CompetitionImage" ?>">           
            <div class="fileinputs">
                <input type="file" name="uploadfile" class="file"  onchange="document.forms['LoadCompetitionImage'].submit();"/>
                <div class="fakefile" id="fkf">
                    <button class="form_change">Load.jpg</button>
                </div>
            </div>  
            <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
        </form> 
</div> 


<div class="form">
    <form  method="POST" action="<?= PageIndex()."Actions/CompetitionSetEventPicture" ?>">           
        <?php if ($competition['Competition_EventPicture']){ ?>
            <img width=60px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
        <?php } ?>
           <input name="ID" type="hidden" value="<?= $competition['Competition_ID'] ?>" />
           <input type="checkbox" name="EventPicture" <?= $competition['Competition_EventPicture']?'checked':'' ?>>
           <input type="submit" value="Set event Picture">
    </form> 
</div> 