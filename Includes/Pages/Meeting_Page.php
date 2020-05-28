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
        'Format' => $row['MeetingFormat_Format'],
        'listId' => $row['MeetingDisciplineList_ID']
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
<br><br><br>
<h1>
    <a 
        class="<?= !$current_discipline ? 'config' : '' ?>"
        href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>">
        <i title='All events' class="fas fa-table"></i></a>
    <?php foreach ($disciplines as $discipline) { ?>
        <a class="<?= $current_discipline == $discipline['MeetingDiscipline_ID'] ? 'config' : '' ?>"
           title="<?= $discipline['MeetingDisciplineList_Name'] ?> / round <?= $discipline['MeetingDiscipline_Round'] ?>"
           href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>/?Discipline=<?= $discipline['MeetingDiscipline_ID'] ?>"><i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i></a>
       <?php } ?>
</h1>   
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
<div class="shadow2" >
    <?php foreach ($disciplines as $discipline) { ?>
        <?php
        if ($current_discipline == $discipline['MeetingDiscipline_ID']) {
            $current = $discipline;
            ?>
            <h2>
                <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
                <?= $discipline['MeetingDiscipline_Name'] ?>
                / round <?= $discipline['MeetingDiscipline_Round'] ?>
            </h2> 
            <?php if ($discipline['MeetingDiscipline_Comment']) { ?>
                <p>
                    <?= $discipline['MeetingDiscipline_Comment'] ?>
                </p>
            <?php } ?>
        <?php } ?>
    <?php } ?>            

    <?php if (!$current_discipline) { ?>
        <table class="table_new">
            <thead>
                <tr>
                    <td></td>
                    <?php foreach ($Disciplines as $DisciplineID => $DisciplineValue) { ?>
                        <td class="table_new_center" style='vertical-align: bottom'>
                            <?php
                            if ($discipline_rounds[$DisciplineValue['listId']] > 1) {
                                for ($i = 1; $i <= $DisciplineValue['Round']; $i++) {
                                    ?>
                                    <font size='1'>
                                    <i class="far fa-star"></i>
                                    </font>
                                <?php } ?>
                            <?php } ?>
                            <h2>
                                <a  href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>/?Discipline=<?= $DisciplineID ?>">
                                    <i class="<?= $DisciplineValue['Image'] ?>"></i>
                                </a>
                            </h2>


                        </td>
                    <?php } ?>
                </tr>
            </thead>
            </tbody>
            <?php
            $DisciplineCount = [];
            $DisciplineCountResult = [];
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
                        <td align="center"  style="border-right: 0px;" >
                            <?php if (isset($Results[$CompetitorID][$DisciplineID])) { ?>
                                <?php $DisciplineCount[$DisciplineID] ++; ?>
                                <?php if ($Results[$CompetitorID][$DisciplineID]['Place']) { ?>

                                    <?php $DisciplineCountResult[$DisciplineID] ++; ?>
                                    <font <?=
                                    ($Results[$CompetitorID][$DisciplineID]['Place'] <= 3 and
                                    $discipline_rounds[$DisciplineValue['listId']] == $DisciplineValue['Round']) ? 'class="podium"' : ''
                                    ?> >
                                        <?= $Results[$CompetitorID][$DisciplineID]['Place']; ?>
                                    </font>
                                <?php } else { ?>
                                    &bull;
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        Total <?= sizeof($Competitors) ?>
                    </td>
                    <?php foreach ($Disciplines as $DisciplineID => $DisciplineValue) { ?>
                        <td align='center' style="vertical-align:bottom;">
                            <?= $DisciplineCount[$DisciplineID] != $DisciplineCountResult[$DisciplineID] ? ($DisciplineCountResult[$DisciplineID] . "<br>" . $DisciplineCount[$DisciplineID]) : $DisciplineCount[$DisciplineID] ?>
                        </td>
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
                    <tr>
                        <td align="center"
                        <?=
                        ($result['MeetingCompetitorDiscipline_Place'] <= 3 and
                        $discipline_rounds[$current['MeetingDisciplineList_ID']] == $current['MeetingDiscipline_Round']) ? 'class="podium"' : ''
                        ?>>
                                <?= $result['MeetingCompetitorDiscipline_Place']; ?> 
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
</div>