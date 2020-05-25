<?php
$AchievementGroupIDPrev= GetMessage('AchievementGroupID');
$AchievementIDPrev= GetMessage('AchievementID');
$AchievementGoalIDPrev= GetMessage('AchievementGoalID');
?>

<h1><a href="<?= PageIndex()?>?Achievements">Achievements</a> 
    <span class="config">▪ Setting</span>
    ▪  <a href="<?= PageIndex()?>?Achievements&test">Test</a>
</h1>
<?php
DataBaseClass::Query('Select * from  AchievementEvent');
$events=DataBaseClass::getRows();
foreach($Achievements as $AGroup=>$achievement_groups){ ?>
<div class="form">
        
            <a href="#" onclick="var 
                e=$('#AchievementGroup_<?= $AGroup ?>');
                e_hide=$('#AchievementGroup_<?= $AGroup ?>_Hide');
                e_show=$('#AchievementGroup_<?= $AGroup ?>_Show');
                if(e.is(':visible')){
                    e.hide(); e_hide.show(); e_show.hide(); 
                }else{
                    e.show(); e_hide.hide(); e_show.show(); 
                } return false;">
                <span ID="AchievementGroup_<?= $AGroup ?>_Hide">&#9658;</span>
                <span ID="AchievementGroup_<?= $AGroup ?>_Show" hidden>&#9660;</span>
                <?= $achievement_groups['AchievementGroup']['GroupName']?></a>
                <span class="badge">
                    <?= sizeof($achievement_groups['Achievements'])?>
                </span>
        
<div <?= $AchievementGroupIDPrev==$AGroup?'':'hidden' ?> id="AchievementGroup_<?= $AGroup ?>"> 
        <p>
        <?php if(!sizeof($achievement_groups['Achievements'])){ ?>
            <form method="POST" action="<?= PageIndex()."Actions/AchievementGroupAction" ?>">
                <input hidden name="GroupID" value="<?= $AGroup ?>">
                <input onclick="return confirm('Delete Achievement group <?= $achievement_groups['AchievementGroup']['GroupName']?>')" name="Action" type="submit" class="form_row delete" style="padding:4px" value="---">   
            </form>
        <?php  } ?>
        <form method="POST" action="<?= PageIndex()."Actions/AchievementGroupAction" ?>">
            <input hidden name="GroupID" value="<?= $AGroup ?>">
            <input name="GroupName" value="<?= $achievement_groups['AchievementGroup']['GroupName']?>">
            <input type="submit" name="Action" class="form_row" style="padding:4px" value=">>>">
        </form>    
        </p>
        <?php foreach($achievement_groups['Achievements'] as $A=>$achievement_goals){ ?>
        <div class="form2">
            <form method="POST" action="<?= PageIndex()."Actions/AchievementAction" ?>"> 
                <a href="#" onclick="var 
                    e=$('#Achievement_<?= $A ?>');
                    e_hide=$('#Achievement_<?= $A ?>_Hide');
                    e_show=$('#Achievement_<?= $A ?>_Show');
                    if(e.is(':visible')){
                        e.hide(); e_hide.show(); e_show.hide(); 
                    }else{
                        e.show(); e_hide.hide(); e_show.show(); 
                    } return false;">
                <span ID="Achievement_<?= $A ?>_Hide">&#9658;</span>
                <span ID="Achievement_<?= $A ?>_Show" hidden>&#9660;</span>

                <?= $achievement_goals['Name']; ?></a>
                <span class="badge">
                    <?= sizeof($achievement_goals['AchievementGoals'])?>
                </span>    
                <input hidden name="GroupID" value="<?= $AGroup ?>">
                <input hidden name="AchievementID" value="<?= $A ?>">
                <?php if(!sizeof($achievement_goals['AchievementGoals'])){ ?>
                    <input onclick="return confirm('Delete Achievement <?= $achievement_goals['Name']; ?>')" name="Action" type="submit" class="form_row delete" style="padding:4px" value="--">   
                <?php  }else{ ?>
                    <input onclick="return confirm('Dublacite Achievement <?= $achievement_goals['Name']; ?>')" name="Action" type="submit" class="form_row" style="padding:4px" value="**">
                <?php } ?>         
            </form>             
            </b>    
        <div <?= $AchievementIDPrev==$A?'':'hidden' ?> id="Achievement_<?= $A ?>">
                <form method="POST" action="<?= PageIndex()."Actions/AchievementAction" ?>">
                    <?php $AchievementTotalError=GetMessage('AchievementTotalError_'.$A);?>
                    <input name="Name" value="<?= $achievement_goals['Name']; ?>">
                    <input style="width: 30px" name="Rank" value='<?= $achievement_goals['Rank']; ?>'> 
                    <input class="<?= $AchievementTotalError?'error':'' ?>" name="Total" value='<?= $AchievementTotalError?:$achievement_goals['Total']; ?>'> 
                    <input hidden name="GroupID" value="<?= $AGroup ?>">
                    <input hidden name="AchievementID" value="<?= $A ?>">
                    <input type="submit" name="Action" class="form_row" style="padding:4px" value=">>">
                    <?php $Total=json_decode($achievement_goals['Total'],true);
                    if(isset($Total['Sum'])){
                        $Total['Sum']=R($Total['Sum']);    
                    }?>
                    <span style="font-size:10px">
                        <?php print_r($Total); ?>
                    </span>
                </form>
            <?php foreach($achievement_goals['AchievementGoals'] as $AGoal=>$achievement_goal){  ?>
                <div class="form3">
                    <form method="POST" action="<?= PageIndex()."Actions/AchievementGoalAction" ?>">
                        <?php $AchievementConditionError=GetMessage('AchievementGoalConditionError_'.$AGoal);?>
                        <img border="<?= $achievement_goal['Result']=='average'?1:0 ?>" valign=middle width="25px" src="Image/AchievementImage/<?= $achievement_goal['Event']?>.png">

                        <select name="Result" style="width: 40px">  
                            <?php foreach(['average','single'] as $result){ ?>
                            <option value="<?= $result ?>" <?= $achievement_goal['Result']==$result?'selected':'' ?>>
                                    <?= substr(strtoupper($result),0,1) ?>
                            </option>
                            <?php } ?>
                        </select>                
                         <select name="Event" style="width: 75px">  
                            <?php foreach($events as $event){ ?>
                            <option <?= $achievement_goal['Event']==$event['EventCode']?'selected':'' ?>>
                                    <?= $event['EventCode'] ?>
                            </option>
                            <?php } ?>
                        </select>
                        <input class="<?= $AchievementConditionError?'error':'' ?>" style="width: 200px" name="Condition" value='<?= $AchievementConditionError?:$achievement_goal['Condition']; ?>'>
                        <input hidden name="GroupID" value="<?= $AGroup ?>">
                        <input hidden name="AchievementID" value="<?= $A ?>">
                        <input hidden name="AchievementGoalID" value="<?= $AGoal ?>">
                        <input type="submit"  name="Action" class="form_row" style="padding:4px" value=">">
                        <input type="submit"  name="Action" class="form_row delete" style="padding:4px" value="-">
                        <?php $Condition=json_decode($achievement_goal['Condition'],true);
                        if(isset($Condition['value'])){
                            $Condition['value']=R($Condition['value']);    
                        } ?>
                        <span style="font-size:10px">
                            <?php print_r($Condition); ?>
                        </span>
                    </form>
                </div>    
        <?php } ?>
            <div class="form3">
                <form method="POST" action="<?= PageIndex()."Actions/AchievementGoalAction" ?>">
                    <select name="Result" style="width: 40px  ">  
                        <?php foreach(['average','single'] as $result){ ?>
                        <option value="<?= $result ?>">
                                <?= substr(strtoupper($result),0,1) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <select name="Event" style="width: 75px">  
                        <?php foreach($events as $event){ ?>
                            <option>
                                <?= $event['EventCode'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input hidden name="GroupID" value="<?= $AGroup ?>">
                    <input hidden name="AchievementID" value="<?= $A ?>">
                    <input hidden name="AchievementGoalID" value="0">
                    <input name="Action" type="submit" class="form_row" style="padding:4px" value="+">
                </form>
            </div>
        </div> 
    </div>         
    <?php } ?>
        <div class="form2">
            <form method="POST" action="<?= PageIndex()."Actions/AchievementAction" ?>">
                <input name="Name" value="">
                <input hidden name="GroupID" value="<?= $AGroup ?>">
                <input hidden name="AchievementID" value="0">
                <input name="Action" type="submit" class="form_row" style="padding:4px" value="++">   
            </form>
        </div>   
</div>     
    </div>     
<?php } ?>
<div class="form">
    <form method="POST" action="<?= PageIndex()."Actions/AchievementGroupAction" ?>">
        <input name="GroupName" value="">
        <input hidden name="GroupID" value="0">
        <input name="Action" type="submit" class="form_row" style="padding:4px" value="+++">   
    </form>
</div>

<div class="form">
    <div class="form2">
        {"And":"1"}
    </div>
    <div class="form2">
        {"Sum": "42000"}
    </div>
    <div class="form2">
        <div class="form3">
            {"wcaid" : "2007STRO01"}
        </div>
        <div class="form3">
            {"record" : "world_records"}
        </div>
        <div class="form3">
            {"value" : 120000}
        </div>
        <div class="form3">
            {..,"mult": 3"}
        </div>
    </div>
</div>
