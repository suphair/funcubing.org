<h1>Add competition</h1>
<?php
$Delegate= GetDelegateData(); 
if(!$Delegate or $Delegate['Delegate_Candidate'] or $Delegate['Delegate_Status']!='Active'){?>
    <h2><span class="error">Unauthorized for add competition</span></h2>
<?php }else{ ?>
<div class="wrapper">
    <div class="form"> 
        <form method="POST" action="<?= PageIndex()."Actions/CompetitionCreate" ?>">
            <div class="form_field">
                WCA 
            </div>
            <div class="form_input">
                <input required type="text" name="WCA" value="" />
            </div>
            <div class="form_field">
                Judge 
            </div>
            <div class="form_input">
                <?php if($Delegate['Delegate_Admin']){ ?>
                
                <select name="Delegate">
                    <?php foreach(DataBaseClass::SelectTableRows('Delegate') as $delegate){ ?>
                        <option  <?= $delegate['Delegate_ID']==$Delegate['Delegate_ID']?'selected':'' ?> value="<?= $delegate['Delegate_ID'] ?>">
                            <?= $delegate['Delegate_Status']=='Archive'?'- ':'' ?>
                            <?= $delegate['Delegate_Name'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php }else{ ?>
                    <?= $Delegate['Delegate_Name'] ?>
                <?php } ?>
            </div>
            <div class="form_enter">
                <input type="submit" value="Create">
            </div>
        </form>
        <?php $err=GetMessage("CompetitionCreate");
        if($err){ ?>
            <br><span class="error"><?= $err?></span>
        <?php } ?>
    </div>
</div>
<?php } ?>