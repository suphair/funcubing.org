<?php
DataBaseClass::FromTable("Meeting", "Secret='" . RequestClass::getParam1() . "'");
DataBaseClass::Join_current("MeetingDiscipline");
DataBaseClass::Join_current("MeetingDisciplineList");
DataBaseClass::Join("MeetingDiscipline", "MeetingFormat");
DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
DataBaseClass::Join_current("MeetingCompetitor");
DataBaseClass::OrderClear("MeetingDisciplineList", "ID");
DataBaseClass::Order("MeetingDiscipline", "Round");
DataBaseClass::Order("MeetingCompetitor", "Name");

$Competitors = array();
$Disciplines = array();
$Results = array();
$rows = DataBaseClass::QueryGenerate();
foreach ($rows as $row) {
    $Competitors[$row['MeetingCompetitor_ID']] = $row['MeetingCompetitor_Name'];
}
foreach ($rows as $row) {
    $Disciplines[$row['MeetingDiscipline_ID']] = array(
        'Image' => $row['MeetingDisciplineList_Image'],
        'Name' => $row['MeetingDisciplineList_Name'],
        'Round' => $row['MeetingDiscipline_Round'],
        'ID' => $row['MeetingDiscipline_ID'],
        'Format' => $row['MeetingFormat_Format']
    );
}

foreach ($rows as $row) {
    $Results[$row['MeetingCompetitor_ID']][$row['MeetingDiscipline_ID']] = [
        'Place' => $row['MeetingCompetitorDiscipline_Place'],
        'Best' => $row['MeetingCompetitorDiscipline_Best'],
        'Average' => $row['MeetingCompetitorDiscipline_Average'],
        'Mean' => $row['MeetingCompetitorDiscipline_Mean']
    ];
}

DataBaseClass::FromTable("MeetingDiscipline", "Meeting=" . $meeting['Meeting_ID']);
DataBaseClass::Join_current("MeetingDisciplineList");
DataBaseClass::OrderClear("MeetingDisciplineList", "ID");
DataBaseClass::Order("MeetingDiscipline", "Round");
$disciplines = DataBaseClass::QueryGenerate();
$discipline_rounds = array();
$current_discipline = 0;
foreach ($disciplines as $discipline) {
    $discipline_rounds[$discipline['MeetingDisciplineList_ID']] = $discipline['MeetingDiscipline_Round'];
    if (isset($_GET['Discipline']) and $_GET['Discipline'] == $discipline['MeetingDiscipline_ID']) {
        $current_discipline = $discipline['MeetingDiscipline_ID'];
    }
}
?>
<h2>
    <a 
        class="<?= !$current_discipline ? 'config' : '' ?>"
        href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>">
        <i class="fas fa-table"></i>
    </a>
    <?php foreach ($disciplines as $discipline) { ?>
        <a class="<?= $current_discipline == $discipline['MeetingDiscipline_ID'] ? 'config' : '' ?>"
           title="<?= $discipline['MeetingDisciplineList_Name'] ?> / round <?= $discipline['MeetingDiscipline_Round'] ?>"
           href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>/?Discipline=<?= $discipline['MeetingDiscipline_ID'] ?>"><i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i></a>
       <?php } ?>
</h2>   
<?php $disciplines_special = [] ?>
<?php foreach ($disciplines as $discipline) { ?>
    <?php
    if ($discipline['MeetingDisciplineList_ID'] >= 200
            and ! in_array($discipline['MeetingDisciplineList_ID'], $disciplines_special)) {
        $disciplines_special[] = $discipline['MeetingDisciplineList_ID'];
        ?>
        <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
        <?= $discipline['MeetingDiscipline_Name'] ?>
    <?php } ?>
<?php } ?>  
<br>
<?php foreach ($disciplines as $discipline) { ?>
    <?php if ($current_discipline == $discipline['MeetingDiscipline_ID']) { ?>
        <h2>
            <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
            <?= $discipline['MeetingDiscipline_Name'] ?>
            / round <?= $discipline['MeetingDiscipline_Round'] ?>
        </h2> 
        <?php if ($discipline['MeetingDiscipline_Comment']) { ?>
            <p>
                <i class="far fa-comment"></i>
                <?= $discipline['MeetingDiscipline_Comment'] ?>
            </p>
        <?php } ?>
    <?php } ?>
<?php } ?>            

<?php if (!$current_discipline) { ?>
    <h2>All events</h2>
    <table class="table_new">
        <thead>
            <tr>
                <td></td>
                <?php foreach ($Disciplines as $DisciplineID => $DisciplineValue) { ?>
                    <td colspan="2" class="table_new_center">
                        <i class="<?= $DisciplineValue['Image'] ?>"></i>
                        <?php if ($DisciplineValue['Round'] > 1) { ?>
                            / <?= $DisciplineValue['Round'] ?>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        </thead>
    </tbody>
    <?php
    $DisciplineCount = array();
    $DisciplineCountResult = array();
    foreach ($Competitors as $CompetitorID => $CompetitorName) {
        ?>
        <tr>
            <td>
                <a href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1(); ?>/?Competitor=<?= $CompetitorID ?>"><nobr><?= $CompetitorName; ?></nobr></a>
            </td>
            <?php
            foreach ($Disciplines as $DisciplineID => $DisciplineValue) {
                if (!isset($DisciplineCount[$DisciplineID])) {
                    $DisciplineCount[$DisciplineID] = 0;
                }
                if (!isset($DisciplineCountResult[$DisciplineID])) {
                    $DisciplineCountResult[$DisciplineID] = 0;
                }
                ?>
                <td align="left" style="border-right: 0px;" class="td_border_left">
                    <?php if (isset($Results[$CompetitorID][$DisciplineID])) { ?>
                        <font style="color:<?= $Results[$CompetitorID][$DisciplineID]['Place'] <= 3 ? 'var(--red)' : 'var(--light_gray)' ?>" >
                        <?= $Results[$CompetitorID][$DisciplineID]['Place']; ?>
                        </font>
                    <?php } ?>
                </td>
                <?php
                if (isset($Results[$CompetitorID][$DisciplineID])) {
                    $DisciplineCount[$DisciplineID] ++;
                    ?>
                    <?php
                    if ($Results[$CompetitorID][$DisciplineID][$DisciplineValue['Format']]) {
                        $DisciplineCountResult[$DisciplineID] ++;
                        ;
                        ?>
                        <td style="text-align:right; border-left: 0px;">
                            <?= $Results[$CompetitorID][$DisciplineID][$DisciplineValue['Format']]; ?>
                        </td>
                    <?php } else { ?>
                        <td style="text-align:center; border-left: 0px;">    
                            &bull;
                        </td>
                    <?php } ?>
                <?php } else { ?>
                    <td style="border-left: 0px;"></td>
                <?php } ?>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Total  <?= sizeof($Competitors) ?>
            </td>
            <?php foreach ($Disciplines as $DisciplineID => $DisciplineValue) { ?>
                <td colspan='2' align='center'><nobr>
            <?= $DisciplineCount[$DisciplineID] != $DisciplineCountResult[$DisciplineID] ? ($DisciplineCountResult[$DisciplineID] . " / " . $DisciplineCount[$DisciplineID]) : $DisciplineCount[$DisciplineID] ?>
        </nobr></td>
    <?php } ?>
    </tr>
    </tfoot>
    </table>
    <?php if (!sizeof($Competitors)) { ?>
        <p>No competitors</p>
    <?php } ?>
    <?php if (($Competitor and $Competitor->id == $meeting['Meeting_Competitor']) or CheckMeetingGrand()) { ?>
        <br><a href="<?= PageIndex() . "Actions/MeetingPrintCompetitor/?Secret=" . RequestClass::getParam1(); ?>">Download certificates</a>  ▪
        <?php if (sizeof($Competitors)) { ?>
            <a target="_blank" href="<?= PageIndex() . "Actions/MeetingPrintScoreCards/?Secret=" . RequestClass::getParam1(); ?>&Discipline=0">Print competitors cards</a> ▪
        <?php } ?>        
        <a target="_blank" href="<?= PageIndex() . "Actions/MeetingPrintScoreCards/?Secret=" . RequestClass::getParam1(); ?>&Discipline=0&blank">Print blank competitors cards</a>

    <?php } ?>

    <?php
} else {

    DataBaseClass::FromTable("MeetingDiscipline", "ID=" . $current_discipline);
    DataBaseClass::Join_current("MeetingFormat");
    $format = DataBaseClass::QueryGenerate(false);

    DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
    DataBaseClass::Join_current("MeetingCompetitor");
    DataBaseClass::OrderSpecial("coalesce(MCD.Place,999)");
    DataBaseClass::Order("MeetingCompetitor", "Name");
    $competitors = DataBaseClass::QueryGenerate();
    ?>
    <table class="table_new">
        <thead>
            <tr>
                <td>Place</td>
                <td>Competitor</td>
                <?php for ($i = 1; $i <= $format['MeetingFormat_Attempts']; $i++) { ?>
                    <td class="attempt"><?= $i ?></td>
                <?php } ?>
                <?php if ($format['MeetingFormat_Format'] == 'Average') { ?>
                    <td class="attempt">Average</td>
                <?php } ?>
                <?php if ($format['MeetingFormat_Format'] == 'Mean') { ?>
                    <td class="attempt">Mean</td>
                <?php } ?>
                <td  class="attempt">Best</td>
            <tr>
        </thead>
        <tbody>
            <?php foreach ($competitors as $result) { ?>
                <tr class="<?= $result['MeetingCompetitorDiscipline_Place'] <= 3 ? 'podium' : '' ?>">
                    <td align="center">
                        <font>
                        <?= $result['MeetingCompetitorDiscipline_Place']; ?>
                        </font>
                    </td>
                    <td ><nobr>
                <a href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1(); ?>/?Competitor=<?= $result['MeetingCompetitor_ID'] ?>"><?= $result['MeetingCompetitor_Name']; ?></a>
            </nobr></td>
        <?php for ($i = 1; $i <= $format['MeetingFormat_Attempts']; $i++) { ?>
            <td class="<?= $i == $format['MeetingFormat_Attempts'] ? 'border-right-solid' : '' ?> attempt">
                <?= str_replace("DNS", "", $result['MeetingCompetitorDiscipline_Attempt' . $i]) ?>
            </td>
        <?php } ?>
        <?php if ($format['MeetingFormat_Format'] == 'Average') { ?>
            <td  class="attempt">
                <b>
                    <?= str_replace(["DNF", "-cutoff"], "", $result['MeetingCompetitorDiscipline_Average']) ?>
                </b>
            </td>
        <?php } ?>
        <?php if ($format['MeetingFormat_Format'] == 'Mean') { ?>
            <td  class="attempt">
                <b>
                    <?= str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Mean']) ?>
                </b>
            </td>
        <?php } ?>
        <td  class="attempt"><?= str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Best']) ?></td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    <?php if (!sizeof($competitors)) { ?>
        <p>No competitors</p>
    <?php } ?>
<?php } ?>
