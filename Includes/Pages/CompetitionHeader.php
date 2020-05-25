<?php

DataBaseClass::Query("select Cn.ID, count( distinct C.Name) count,count(A.ID) Attempts, now()>=Cm.StartDate Start from `Competition` Cn  
join `Event` E on E.Competition=Cn.ID
join `Competition` Cm on Cm.ID=E.Competition
join `Command` Com on Com.Event=E.ID 
join `CommandCompetitor` CC on CC.Command=Com.ID 
join `Competitor` C on CC.Competitor=C.ID
left outer join Attempt A on A.Command=Com.ID
where Com.Decline!=1 and E.Competition='".$competition['Competition_ID']."'
group by Cn.ID");
$data=DataBaseClass::getRow();

$count_competitors=$data['count']+0;
$attempts_exists=($data['Attempts']>0 or $data['Start']);
    
?>
    <table><tr class="no_border"><td>
    <?= ImageCompetition($competition['Competition_WCA'],100)?>
    </td><td>
    <h1 class="competition_name">      
        <?php if(!$attempts_exists){
            if($competition['Competition_Registration']){ ?>
                <?= svg_green(30,'Registration is opened') ?>
            <?php }else{ ?>
                <?= svg_red(30,'Registration is closed') ?>
        <?php }
        }?>
        <nobr><a href="<?= LinkCompetition($competition['Competition_WCA'])?>"><?= $competition['Competition_Name'] ?></a>
        <span class="badge"><?= $count_competitors ?></span></nobr>
        &#9642; <a href="https://www.worldcubeassociation.org/competitions/<?= $competition['Competition_WCA'] ?>">WCA</a>
    <?php if(CheckDelegateCompetition($competition['Competition_ID'])){ ?>
            <?php if(RequestClass::getParam2()==='config'){ ?>
                <span class="config" >&#9642; Setting</span>
            <?php }else{ ?>
                <nobr><a href="<?= LinkCompetition($competition['Competition_WCA']) ?>/config">&#9642; Setting</a></nobr>
            <?php } ?>
        <?php }elseif(CheckDelegateCompetition($competition['Competition_ID'],false)){ ?>
            <span class='disabled'>&#9642; Setting</span>    
        <?php } ?>
    </h1>            
    <h2 class="competition_details">
        <img align='center' width='50px' src='<?= PageIndex() ?>Image/Flags/<?= strtolower($competition['Competition_Country']) ?>.png'>
        <?=  date_range($competition['Competition_StartDate'],$competition['Competition_EndDate']); ?>
        &#9642;
        <?= CountryName($competition['Competition_Country']); ?>, <?= $competition['Competition_City'] ?>
    </h2>    
        
        <?php if(sizeof($delegates)==1){?>
            <?php foreach($delegates as $delegate){ ?>
                <?php if($delegate['Delegate_Status']=='Active'){ ?>
                    <span title="Judge Unofficial Events">Judge of unofficial events: <a href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>"><?= Short_Name($delegate['Delegate_Name'])?></a>
                <?php }elseif($delegate['Delegate_ID']!=8){ ?>
                    <span title="Judge Unofficial Events">Judge of unofficial events: <?= Short_Name($delegate['Delegate_Name'])?>
                <?php } ?>
            <?php } ?>
        <?php }else{ ?>
            <span title=" Judges Unofficial Events">Judges of unofficial events</span>:       
            <?php foreach($delegates as $i=>$delegate){ 
                 if($i){ ?>, <?php } ?>         
                <?php if($delegate['Delegate_Status']=='Active'){ ?>
                        <nobr><a href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>"><?= Short_Name($delegate['Delegate_Name'])?></a></nobr><?php
                    }elseif($delegate['Delegate_ID']!=8){ ?>
                        <nobr><?= Short_Name($delegate['Delegate_Name'])?></nobr><?php 
                    }
                } ?>         
        <?php } ?>

    </td></tr></table>

<?php if($competition['Competition_Comment']){?>
    <div class="block_comment">
    <?= Echo_format($competition['Competition_Comment']); ?>
    </div>
    <br>
<?php } ?>
