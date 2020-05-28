<br><br><br>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $(function () {
        $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
    });
</script>
<div class="shadow2" >
    <h2>

        <i class="fas fa-cog"></i>
        Setting
    </h2>
    <form method="POST" action="<?= PageIndex() . "Actions/MeetingConfig" ?>">
        Name 
        <input required type="text" name="Name" value="<?= $meeting['Meeting_Name'] ?>" />
        <p>
            <input <?= $meeting['Meeting_Show'] ? 'checked' : '' ?> type="radio" name="Show" value="1">
            <i class="fas fa-eye"></i>
            <b>Public</b>
            </input> 
            : displayed in the competition list
        </p>
        <p>
            <input <?= !$meeting['Meeting_Show'] ? 'checked' : '' ?> type="radio" name="Show" value="0">
            <i class="far fa-eye-slash"></i>
            <b>Private</b>
            </input>
            : сompetitions are only visible via the link (for your testing or fun)
        </p>
        Details 
        <input type="text" name="Details" value="<?= $meeting['Meeting_Details'] ?>" />
        <br>
        Date            
        <input required  style="width:140px" required type="text" id="datepicker" name="Date" value="<?= date('d.m.Y', strtotime($meeting['Meeting_Date'])) ?>">
        <br>
        Website
        <input type="url" placeholder="https://example.com" pattern="http[s]?://.*" name="Website" value="<?= $meeting['Meeting_Website'] ?>">
        <br>
        <input type="checkbox" <?= $meeting['Meeting_SecretRegistration'] ? 'checked' : ''; ?> name="Registraton">
        <i class="fas fa-user-plus"></i> Open self-registration (competitors can register themselves)
        <?php
        if ($meeting['Meeting_SecretRegistration']) {
            $link = PageIndex() . 'Meetings/' . $meeting['Meeting_Secret'] . '/?Registration&secret=' . $meeting['Meeting_SecretRegistration'];
            ?>
            <br>      
            <a target='_blank' href="<?= $link ?>">link for self-registration</a>
        <?php } ?>
        <br>
        <input type="checkbox" <?= $meeting['Meeting_ShareRegistration'] ? 'checked' : ''; ?> name="ShareRegistration">
        Publish a link for self-registration (this link will be published on the competition page)
        <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
        <br>
        <input type="hidden" name="Action" value="Change">
        <button>
            <i class="fas fa-save"></i>
            Save
        </button>
    </form>
    <?php if (DataBaseClass::getRow()['count'] == 0) { ?>
        <form method="POST" action="<?= PageIndex() . "Actions/MeetingConfig" ?>">
            <input type="hidden" name="Action" value="Delete">
            <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
            <?php
            DataBaseClass::Query("Select sum(case when MC.ID is not null or MD.ID is not null then 1 else 0 end) count from Meeting M "
                    . " left outer join MeetingCompetitor MC on MC.Meeting=M.ID "
                    . " left outer join MeetingDiscipline MD on MD.Meeting=M.ID "
                    . " where M.ID=" . $meeting['Meeting_ID']);
            ?>
            <button class="delete">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </form>    
    <?php } ?>
    <br><h3>Additional organizers (all action except the settings)</h3>
    <?php
    DataBaseClass::Query("Select C.*,MO.WCAID MO_WCAID from MeetingOrganizer MO left outer join Competitor C on MO.WCAID=C.WCAID where MO.Meeting=" . $meeting['Meeting_ID']);
    foreach (DataBaseClass::getRows() as $r) {
        ?>
        <form method="POST" action="<?= PageIndex() . "Actions/MeetingOranizator" ?>">
            <i class="fas fa-user-secret"></i>
            <?= $r['MO_WCAID']; ?>
            <b><?= Short_Name($r['Name']); ?></b> 
            <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
            <input type="hidden" name="WCAID" value="<?= $r['MO_WCAID']; ?>">
            <input type="hidden" name="Action" value="Delete">
            <button class="delete">
                <i class="fas fa-user-minus"></i>
                Remove
            </button>
        </form>
    <?php } ?>
    <form method="POST" action="<?= PageIndex() . "Actions/MeetingOranizator" ?>">
        WCAID
        <input name="WCAID" required="" value="">
        <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
        <input type="hidden" name="Action" value="Add">
        <button>
            <i class="fas fa-user-plus"></i>
            Add
        </button>    
    </form>
</div>

<?php
DataBaseClass::Query("Select MDL.Image MDL_Image, MDL.ID MDL_ID,MDL.Name, MD.Name MD_Name,MD.Comment,MD.Round,MD.ID, "
        . " CONCAT(MF.Format,' of ',MF.Attempts) MF_Format "
        . " from MeetingDiscipline MD"
        . " join MeetingDisciplineList MDL on MDL.ID=MD.MeetingDisciplineList"
        . " join MeetingFormat MF on MF.ID=MD.MeetingFormat"
        . " where MD.Meeting=" . $meeting['Meeting_ID']
        . " Order by MDL.ID");
$Comments = [];
$Rounds = [];
foreach (DataBaseClass::getRows() as $row) {
    $Comments[$row['Name']][$row['Round']] = $row;
    $Rounds[$row['Round']] = 1;
}
if (sizeof($Rounds)) {
    ?> 

    <div class="shadow2">
        <h2>Comments for competitors cards</h2>
        <form method="POST" action="<?= PageIndex() . "Actions/MeetingSetComments" ?>">
            <table class="table_new">
                <thead>
                    <tr>
                        <td></td>
                        <td>
                            Event
                        </td>  
                        <td>
                            Format
                        </td>
                        <?php foreach ($Rounds as $round => $empty) { ?>
                            <td align="center">
                                round <?= $round ?>
                            </td>
                        <?php } ?>    
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Comments as $discipline => $row) { ?>
                        <tr>
                            <td>
                                <i class="<?= $row[1]['MDL_Image'] ?>"></i>
                            </td>
                            <td>
                                <?= $row[1]['MD_Name'] ?>
                            </td>
                            <td>
                                <?= $row[1]['MF_Format'] ?>
                            </td>
                            <?php foreach ($Rounds as $round => $empty) { ?>
                                <td align="center">
                                    <?php if (isset($row[$round])) { ?>
                                        <input name="Comments[<?= $row[$round]['ID'] ?>]" value="<?= $row[$round]['Comment'] ?>">
                                    <?php } ?>
                                </td>
                            <?php } ?>        
                        </tr>    
                    <?php } ?>   
                <tbody>
            </table>
            <button>
                <i class="far fa-save"></i>
                Set comments
            </button>
            <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
        </form>
    <?php } ?>
    <br>
    <?php
    $MeetingDiscipline = array();
    DataBaseClass::Query("Select max(Round) Round,max(MeetingFormat) MeetingFormat,MeetingDisciplineList,  Sum(coalesce(MCD.Place,0)) Places, Count(distinct MCD.ID) Count, sum(MD.Amount) Amount  "
            . " from MeetingDiscipline MD"
            . " left outer join MeetingCompetitorDiscipline MCD on MCD.MeetingDiscipline=MD.ID"
            . " where Meeting=" . $meeting['Meeting_ID'] . ""
            . " group by MeetingDisciplineList");
    foreach (DataBaseClass::getRows() as $row) {
        $MeetingDiscipline[$row['MeetingDisciplineList']] = $row;
    }
    ?>   


</div>

<div class="shadow2">

    <h2>Events</h2>
    <?php if (!sizeof($MeetingDiscipline)) { ?>
        <span class='error'><br>Please, select the disciplines!</span>
        <?php
    }
    DataBaseClass::FromTable("MeetingFormat");
    $formats = DataBaseClass::QueryGenerate();
    ?>
    <form method="POST" action="<?= PageIndex() . "Actions/MeetingRounds" ?>">
        <table class='table_new'>
            <thead>
                <tr>
                    <td class="table_new_center" colspan='4'>Rounds</td>
                    <td class="table_new_center" colspan='2'>Event</td>
                    <td class="table_new_center" colspan='<?= sizeof($formats) ?>'>Format</td>
                    <td class="table_new_center" colspan='2'>Type</td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-times"></i>
                    </td>
                    <?php foreach ([1, 2, 3] as $round) { ?>
                        <td><?= $round ?></td>
                    <?php } ?>
                    <td/>
                    <td></td>
                    <?php
                    foreach ($formats as $format) {
                        ?>
                        <td><?= substr($format['MeetingFormat_Format'], 0, 1) ?>o<?= $format['MeetingFormat_Attempts'] ?></td>
                    <?php } ?>
                    <td>
                        Time
                    </td>
                    <td>
                        Amount
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php
                DataBaseClass::FromTable("MeetingDisciplineList");
                foreach (DataBaseClass::QueryGenerate() as $discipline) {
                    DataBaseClass::FromTable("MeetingDisciplineList");
                    DataBaseClass::Join_current("MeetingDiscipline");
                    DataBaseClass::Where_current("Round=1");
                    DataBaseClass::Where_current("Meeting=" . $meeting['Meeting_ID']);
                    DataBaseClass::Where_current("MeetingDisciplineList=" . $discipline['MeetingDisciplineList_ID']);
                    $row = DataBaseClass::QueryGenerate(false);
                    $name = $discipline['MeetingDisciplineList_Name'];
                    if (isset($row['MeetingDiscipline_Name'])) {
                        $name_special = $row['MeetingDiscipline_Name'];
                    } else {
                        $name_special = $name;
                    }
                    $disciplineID = $discipline['MeetingDisciplineList_ID'];
                    if (!isset($MeetingDiscipline[$disciplineID]['Places'])) {
                        $MeetingDiscipline[$disciplineID]['Places'] = 0;
                    }
                    if (!isset($MeetingDiscipline[$disciplineID]['Round'])) {
                        $MeetingDiscipline[$disciplineID]['Round'] = 0;
                    }
                    if (!isset($MeetingDiscipline[$disciplineID]['Count'])) {
                        $MeetingDiscipline[$disciplineID]['Count'] = 0;
                    }
                    if (!isset($MeetingDiscipline[$disciplineID]['MeetingFormat'])) {
                        $MeetingDiscipline[$disciplineID]['MeetingFormat'] = 0;
                    }
                    if (!isset($MeetingDiscipline[$disciplineID]['Amount'])) {
                        $MeetingDiscipline[$disciplineID]['Amount'] = 0;
                    }
                    if (!isset($MeetingDiscipline[$disciplineID]))
                        $MeetingDiscipline[$disciplineID] = array('Round' => 0, 'MeetingFormat' => 1, 'Count' => 0, 'Amount' => 0);
                    ?>
                    <tr>
                        <td>
                            <?php if ($MeetingDiscipline[$disciplineID]['Count']) { ?>
                            <?php } else { ?>
                                <input  checked type="radio" value="0" name="DisciplineRound[<?= $disciplineID ?>]">
                            <?php } ?>
                        </td>
                        <?php foreach ([1, 2, 3] as $round) { ?>
                            <td><input <?= $MeetingDiscipline[$disciplineID]['Round'] == $round ? 'checked' : '' ?> type="radio" value="<?= $round ?>" name="DisciplineRound[<?= $disciplineID ?>]"></td>
                        <?php } ?>
                        <td>
                            <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
                        </td>
                        <td>
                            <?php if ($discipline['MeetingDisciplineList_ID'] < 100) { ?>
                                <?= $name_special ?>
                            <?php } else { ?>
                                <input name="Names[<?= $discipline['MeetingDisciplineList_ID'] ?>]" value='<?= $name_special ?>'>
                            <?php } ?>
                        </td>
                        <?php foreach ($formats as $f => $format) { ?>
                            <td align="center">
                                <?php if ($MeetingDiscipline[$disciplineID]['Places']) { ?>
                                    <?php if ($MeetingDiscipline[$disciplineID]['MeetingFormat'] == $format['MeetingFormat_ID']) { ?> 
                                        +
                                        <input type="hidden" value="<?= $format['MeetingFormat_ID'] ?>" name="DisciplineFormat[<?= $disciplineID ?>]">
                                    <?php } ?>
                                <?php } else { ?>
                                    <input  <?= !$f ? 'checked' : '' ?>
                                    <?= $MeetingDiscipline[$disciplineID]['MeetingFormat'] == $format['MeetingFormat_ID'] ? 'checked' : '' ?> 
                                        type="radio" value="<?= $format['MeetingFormat_ID'] ?>" name="DisciplineFormat[<?= $disciplineID ?>]">
                                    <?php } ?>    
                            </td>
                        <?php } ?>
                        <?php if ($discipline['MeetingDisciplineList_ID'] < 100) { ?>
                            <?php if ($discipline['MeetingDisciplineList_Amount']) { ?>
                                <td/>
                                <td align="center">
                                    v
                                </td>
                            <?php } else { ?>
                                <td align="center">
                                    v
                                </td>
                                <td/>
                            <?php } ?>
                        <?php } else { ?>

                            <?php if ($MeetingDiscipline[$disciplineID]['Places']) { ?>
                                <?php if ($MeetingDiscipline[$disciplineID]['Amount']) { ?>
                                    <td/>
                                    <td align="center">
                                        +
                                    </td>
                                <?php } else { ?>
                                    <td align="center">
                                        +
                                    </td>
                                    <td/>
                                <?php } ?>       

                            <?php } else { ?>
                                <td align="center">
                                    <input checked="" type="radio" value="time" name="DisciplineType[<?= $disciplineID ?>]">
                                </td>
                                <td align="center">
                                    <input <?= $MeetingDiscipline[$disciplineID]['Amount'] ? 'checked' : '' ?> type="radio" value="amount" name="DisciplineType[<?= $disciplineID ?>]">
                                </td>
                            <?php } ?>           
                        <?php } ?>                            
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button>
            <i class="far fa-save"></i>
            Set events
        </button>
        <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
    </form>
</div>
