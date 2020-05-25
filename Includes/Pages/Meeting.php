<?php
DataBaseClass::FromTable("Meeting", "Secret='" . RequestClass::getParam1() . "'");
$meeting = DataBaseClass::QueryGenerate(false);

DataBaseClass::FromTable("Competitor", "WID=" . $meeting['Meeting_Competitor']);
$competitor_row = DataBaseClass::QueryGenerate(false);
$Competitor = GetCompetitorData();

DataBaseClass::Query("Select count(*) count"
        . " from MeetingDiscipline MD"
        . " where Meeting=" . $meeting['Meeting_ID']);
$countDiscipline = DataBaseClass::getRow()['count'];
?>
<h1>
    <span class='flag-icon flag-icon-<?= strtolower($competitor_row['Competitor_Country']) ?>'></span>
    <a href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>"><?= $meeting['Meeting_Name'] ?></a></nobr>
</h1>


<p>    
    <i class="far fa-calendar-alt"></i> 
    <?= date('d F Y', strtotime($meeting['Meeting_Date'])) ?>    
    <?php if ($meeting['Meeting_Website']) { ?>
        <i class="fas fa-link"></i>
        <a target="_blank" href="<?= $meeting['Meeting_Website'] ?>">
            <?php preg_match('/https?:\/\/(?:www\.|)([\w.-]+).*/', $meeting['Meeting_Website'], $matches); ?>
            <?php if (isset($matches[1])) { ?>
                <?= $matches[1] ?>
            <?php } else { ?>
                Website
            <?php } ?>        
        </a>
    <?php } ?>
    <i class="fas fa-user-tie"></i>
    <a target='_blank' 
       href='https://www.worldcubeassociation.org/persons/<?= $competitor_row['Competitor_WCAID'] ?>'>
           <?= Short_Name($competitor_row['Competitor_Name']) ?>
    </a>   
<h2>
    <?= $meeting['Meeting_Details'] ?>
</h2>
</p>
<?php if ($meeting['Meeting_ShareRegistration'] and isset($_GET['Registration'])) { ?>  
    <h3 style="color:var(--red)">
        <i class="fas fa-user-plus"></i>
        Self-registration
    </h3>
<?php } ?>  
<?php if (isset($_GET['Setting']) and $Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand())) { ?>
    <h3 style="color:var(--red)">
        <i class="fas fa-cog"></i>
        Setting
    </h3>
<?php } ?>
<?php if (isset($_GET['Registrations']) and $Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand())) { ?>
    <h3 style="color:var(--red)">
        <i class="fas fa-users-cog"></i>
        Registrations
    </h3>
<?php } ?>

<?php if ($Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand())) { ?>
    <i class="fas fa-cog"></i>
    <a href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>/?Setting">Setting</a> 
<?php } ?>

<?php if ($Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand())) { ?>
    <i class="fas fa-users-cog"></i>
    <a href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>/?Registrations">Registrations</a> 
<?php } ?>

<?php if ($meeting['Meeting_SecretRegistration'] and $meeting['Meeting_ShareRegistration']) { ?>
    <i class="fas fa-user-plus"></i>
    <a href="<?= PageIndex() . 'Meetings/' . $meeting['Meeting_Secret'] . '/?Registration&secret=' . $meeting['Meeting_SecretRegistration']; ?>">
        Self-registration
    </a>
<?php } ?>
<hr>
<?php
if (isset($_GET['Competitor'])) {
    include 'Meeting_Competitor.php';
} elseif ($Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand()) and isset($_GET['Setting'])) {
    include 'Meeting_Setting.php';
} elseif ($Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand()) and isset($_GET['Registrations'])) {
    if (!$countDiscipline) {
        include 'Meeting_Setting.php';
    } else {
        include 'Meeting_Registrations.php';
    }
} elseif ($Competitor and CheckMeetingOrganizer($meeting['Meeting_ID']) and isset($_GET['Registrations'])) {
    if (!$countDiscipline) {
        include 'Meeting_Page.php';
    } else {
        include 'Meeting_Registrations.php';
    }
} elseif ($Competitor and ( $Competitor->id == $meeting['Meeting_Competitor'] or CheckMeetingGrand() or CheckMeetingOrganizer($meeting['Meeting_ID'])) and isset($_GET['Discipline'])) {
    include 'Meeting_Results.php';
} else {
    $reg_fl = false;
    if (isset($_GET['Registration']) and isset($_GET['secret'])) {
        $SecretRegistation = DataBaseClass::Escape($_GET['secret']);

        DataBaseClass::FromTable("Meeting", "SecretRegistration='$SecretRegistation'");
        $m = DataBaseClass::QueryGenerate(false);
        if (is_array($m)) {
            $reg_fl = true;
        }
    }
    if ($reg_fl) {
        include 'Meeting_Registration.php';
    } else {
        include 'Meeting_Page.php';
    }
}
?>

