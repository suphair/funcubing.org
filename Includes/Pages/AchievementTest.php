<h1><a href="<?= PageIndex()?>?Achievements">Achievements</a> 
            ▪ <a href="<?= PageIndex()?>?Achievements&config">Setting</a>
            <span class="config">▪ Test</span>
        </h1>
        <h2>
            <?= $WCAID; ?>
            <input ID="WCAID" value="" placeholder="WCAID">
            <input type="submit" value=">>" onclick="document.location.href = '<?= PageIndex().'?Achievements&test&WCA_ID=' ?>' + $('#WCAID').val();">
        </h2>


        <?php foreach($Achievements as $AGroup=>$achievement_groups){ ?>
                <hr>
                <h2>
                    <?= $achievement_groups['AchievementGroup']['GroupName']?>
                </h2>
                <?php foreach($achievement_groups['Achievements'] as $A=>$achievement_goals){ ?>
                    <h3 class="<?= $achievement_goals['Total']['Complete']?'message':'error';?>">
                        <?= $achievement_goals['Name']; ?>
                        <?php if (isset($achievement_goals['Total']['Sum'])){?>
                            {<?= $achievement_goals['Total']['personal_record'].' / '.$achievement_goals['Total']['Sum']; ?>}
                        <?php } ?>
                        : <?= $achievement_goals['Rank']; ?>    
                    </h3><?php
                    foreach($achievement_goals['AchievementGoals'] as $AGoal=>$achievement_goal){   
                        ?>

                    <img border="<?= $achievement_goal['Result']=='average'?1:0 ?>" valign=middle width="25px" src="Image/AchievementImage/<?= $achievement_goal['Event']?>.png">

                    <?php if(isset($achievement_goal['Condition']['value'])){ ?>

                        <span class="<?= $achievement_goal['Condition']['Complete']?'message':'error';?>">
                            <?= $achievement_goal['Condition']['personal_record']?>
                            / <?= $achievement_goal['Condition']['value'] ?>
                        </span>    
                    <?php }else{?>
                        <span class="<?= $achievement_goal['Condition']['Complete']?'':'error';?>">
                           <?= $achievement_goal['Condition']['personal_record']?>
                        </span>    
                    <?php } ?>
                </span>
                        <?php

                    }   
                }
            } ?>