<?php 
$Competitor=GetCompetitorData();
#$Delegate=GetDelegateData(); 
$isAdmin=CheckAdmin();

if($Competitor){ ?>
    <?= svg_green(12,'Competitor is logged in') ?>
    <?= $Competitor->name ?> <a href="<?= PageIndex() ?>Actions/LogoutCompetitor"><font color="red">Sign out</font></a>
    <?php if($Section=='UnofficialCompetitions'){ ?>
            <?php DataBaseClass::FromTable("Meeting","Competitor=".$Competitor->id); 
            $MyMeetings=DataBaseClass::QueryGenerate();
            DataBaseClass::FromTable("MeetingOrganizer","WCAID='".$Competitor->wca_id."'"); 
            $OrgMeetings=DataBaseClass::QueryGenerate();
            if(sizeof($MyMeetings) or sizeof($OrgMeetings) or CheckMeetingGrand()){ ?>    
                &#9642; <a href="<?= PageIndex() ?>?Meetings&My">My unofficial competitions</a> 
            <?php } ?>  
    <?php }
    if($Section=='CompetitionGoals'){ ?>
           &#9642; <a href="<?= PageIndex() ?>?CompetitionGoals&My">My competition goals</a> 
    <?php }
 /*   if($Section=='UnofficialEvents'){ ?>   
                <?php if($Delegate){ ?>
                    &#9642; <a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']); ?>">
                    <?php if($Delegate['Delegate_Admin']){?>
                        Senior Judge
                    <?php }elseif($Delegate['Delegate_Candidate']){?>
                        Junior Judge
                    <?php }else{ ?>
                        Middle Judge
                    <?php } ?>
                    </a>
                <?php } ?>        

                &#9642; <a href="<?= PageIndex() ?>Competitor/<?= $Competitor->local_id ?>">My results</a>
                &#9642; <a href="<?= PageIndex() ?>?Competitions&My">My competitions</a>
                 
    <?php } */?>
<?php }else{ ?>
    <?= svg_red(12,'Competitor is not logged in') ?>
    <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
    <a href="<?= GetUrlWCA(); ?>">Sign in with WCA</a>
<?php } ?>