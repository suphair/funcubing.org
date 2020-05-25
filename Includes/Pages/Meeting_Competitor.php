<?php
$found = false;
if (isset($_GET['Competitor']) and is_numeric($_GET['Competitor'])) {
    ?>
    <?php
    DataBaseClass::FromTable("MeetingCompetitor", "ID=" . $_GET['Competitor']);
    $competitor = DataBaseClass::QueryGenerate(false);
    if (is_array($competitor)) {
        $found = true;
    }
}
if ($found) {
    ?>
    <h2><?= $competitor['MeetingCompetitor_Name'] ?></h2>
    <br>
    <h3>
        Competitions
    </h3>
    <table class="table_new">
        <thead>
            <tr>
                <td>
                    <i class="far fa-calendar-alt"></i>
                    Date
                </td>
                <td>
                    <i class="fas fa-cube"></i>
                    Competition
                </td>
                <td></td>
            </tr>
        </thead>
        <tbody>    
            <?php
            $Competitor_IDs = array();
            DataBaseClass::FromTable("MeetingCompetitor", "Name='" . $competitor['MeetingCompetitor_Name'] . "'");
            DataBaseClass::Join_current("Meeting");
            DataBaseClass::OrderClear("Meeting", "Date DESC");
            foreach (DataBaseClass::QueryGenerate() as $meeting) {
                ?>
                <tr>
                    <td>
                        <?= date('d F Y', strtotime($meeting['Meeting_Date'])); ?>
                    </td>
                    <td>
                        <a href="<?= PageIndex() . "Meetings/" . $meeting['Meeting_Secret'] ?>">
                            <?= $meeting['Meeting_Name'] ?>
                        </a>
                    </td>
                    <td>
                        <?php
                        DataBaseClass::FromTable("MeetingCompetitor", "Meeting='" . $meeting['Meeting_ID'] . "'");
                        DataBaseClass::Join_current("MeetingCompetitorDiscipline");
                        DataBaseClass::Join_current("MeetingDiscipline");
                        DataBaseClass::Join_current("MeetingDisciplineList");
                        DataBaseClass::Where_current("ID<100");
                        DataBaseClass::Where("MCD.Place is not null");
                        DataBaseClass::Where("MC.ID=" . $meeting['MeetingCompetitor_ID']);
                        $result = DataBaseClass::QueryGenerate(false);
                        $Competitor_IDs[] = $meeting['MeetingCompetitor_ID'];
                        if (is_array($result)) {
                            ?>    
                            <a target="_blank" href="<?= PageIndex() . "Actions/MeetingPrintCompetitor/?Secret=" . $meeting['Meeting_Secret'] ?>&Competitor=<?= $meeting['MeetingCompetitor_ID'] ?>">
                                <i class="fas fa-print"></i>
                                Certificate
                            </a>
                        <?php } ?>
                    </td>       
                </tr>
            <?php } ?>
        <tbody> 
    </table>
    <?php
    DataBaseClass::FromTable("MeetingCompetitor", "ID in(" . implode(", ", $Competitor_IDs) . ")");
    DataBaseClass::Join_current("MeetingCompetitorDiscipline");
    //DataBaseClass::Where_current("Place is not null");
    DataBaseClass::Join_current("MeetingDiscipline");
    DataBaseClass::Join_current("MeetingDisciplineList");
    DataBaseClass::Where_current("ID<100");
    DataBaseClass::Join("MeetingCompetitor", "Meeting");
    DataBaseClass::OrderClear("Meeting", "ID desc");
    DataBaseClass::Order("MeetingDiscipline", "Round desc");
    $results = array();
    $MeetingDisciplineListS = array();
    $MeetingDisciplineResultS = array();
    foreach (DataBaseClass::QueryGenerate() as $row) {
        $MeetingDisciplineListS[$row['MeetingDisciplineList_ID']] = $row;
        $MeetingDisciplineResultS[$row['MeetingDisciplineList_ID']][] = $row;
    }
    ?>
    <br>
    <h3>Results</h3>
    <table class="table_new">
        <thead>
            <tr>
                <td>Place</td>
                <td>Discipline</td>
                <td>Competition</td>

                <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <td class='attempt'>
                        <?= $i ?>
                    </td>
                <?php } ?>
                <td class="table_new_center">
                    Average
                </td>
                <td class="table_new_center">
                    Best
                </td>
            <tr>
        </thead>
        <tbody>
            <?php foreach ($MeetingDisciplineResultS as $discipline => $results) { ?>
                <tr><td colspan='10' >&nbsp;</td></tr>    
                <?php foreach ($results as $result) { ?>
                    <tr>
                        <td align='center'>
                            <?= $result['MeetingCompetitorDiscipline_Place'] ?> 
                        </td>
                        <td>
                            <img width="15px" src="<?= PageIndex() . "Image/MeetingImage/" . $MeetingDisciplineListS[$discipline]['MeetingDisciplineList_Name'] ?>.png">
                            <?= $MeetingDisciplineListS[$discipline]['MeetingDisciplineList_Name'] ?>, Round <?= $result['MeetingDiscipline_Round'] ?>  
                        </td>
                        <td >
                            <a href="<?= PageIndex() . "Meetings/" . $meeting['Meeting_Secret'] ?>"><?= $result['Meeting_Name'] ?></a> 
                        </td>
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <td class='attempt'> <?= str_replace('DNS', '', $result['MeetingCompetitorDiscipline_Attempt' . $i]); ?> </td>
                        <?php } ?>
                        <td class='attempt border-left-solid'>
                            <?= str_replace(['DNS', 'DNF', '-cutoff'], '', $result['MeetingCompetitorDiscipline_Average']); ?>
                            <?= str_replace(['DNS', 'DNF'], '', $result['MeetingCompetitorDiscipline_Mean']); ?>
                        </td>    
                        <td class='attempt'>
                            <?= str_replace(array('DNS', 'DNF'), '', $result['MeetingCompetitorDiscipline_Best']); ?>
                        </td>
                    </tr>    
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <h3><span class="error">Competitor not found</span></h3>
<?php } ?>

