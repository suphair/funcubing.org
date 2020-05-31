<?php
$Competitor = GetCompetitorData();
#$Delegate=GetDelegateData(); 
$isAdmin = CheckAdmin();

if ($Competitor) {
    ?>
    <?= Short_Name($Competitor->name) ?>

    <a href="<?= PageIndex() ?>Actions/LogoutCompetitor">
        <i class="fas fa-sign-out-alt"></i>
        Sign out
    </a>
    <?php if ($Section == 'UnofficialCompetitions') { ?>
        <?php
        DataBaseClass::FromTable("Meeting", "Competitor=" . $Competitor->id);
        $MyMeetings = DataBaseClass::QueryGenerate();
        DataBaseClass::FromTable("MeetingOrganizer", "WCAID='" . $Competitor->wca_id . "'");
        $OrgMeetings = DataBaseClass::QueryGenerate();
        if (sizeof($MyMeetings) or sizeof($OrgMeetings) or CheckMeetingGrand()) {
            ?>    
            &#9642; <a href="<?= PageIndex() ?>?Meetings&Mine">My unofficial competitions</a> 
        <?php } ?>  
        <?php
    }
} else {
    ?>

    <?php $_SESSION['ReferAuth'] = $_SERVER['REQUEST_URI']; ?> 
    <a href="<?= GetUrlWCA(); ?>">
        <i class="fas fa-sign-in-alt"></i>
        Sign in with WCA
    </a>
<?php } ?>