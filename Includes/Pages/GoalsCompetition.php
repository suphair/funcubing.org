<style>
<?php include 'GoalsCompetition.css' ?>
</style>
<?php
$Competitor = GetCompetitorData();
$competitionWca = DataBaseClass::Escape(getRequest()[2]);

$upcoming = false;
if ($Competitor) {
    foreach (GetUpcomingCompetition($Competitor->id)['upcoming_competitions'] as $competition) {
        if (strtoupper($competition['id']) == strtoupper($competitionWca)) {
            $upcoming = true;
        }
    }
}

DataBaseClass::Query("
        SELECT 
            Code code,
            Name name
        FROM GoalDiscipline
        ORDER BY ID
    ");
$events = [];
foreach (DataBaseClass::getRows() as $row) {
    $events[$row['code']] = $row['name'];
}

DataBaseClass::Query("
        SELECT
            Competitor.WID wid, 
            Competitor.Name name,
            Competitor.WCAID wcaid,
            Competitor.Country country,
            GoalCompetitor.TimeStamp timeStamp
        FROM GoalCompetition
        JOIN Goal ON GoalCompetition.WCA = Goal.Competition
        JOIN GoalCompetitor 
            ON GoalCompetitor.competitorWid = Goal.Competitor
            AND GoalCompetitor.competitionWca = GoalCompetition.Wca
            AND GoalCompetitor.eventCode = Goal.Discipline
        JOIN Competitor ON Competitor.WID = Goal.Competitor
        WHERE GoalCompetition.WCA = '$competitionWca'
        ORDER BY Competitor.Name
    ");
$competitors = [];
if ($upcoming) {
    $competitors[$Competitor->id] = arrayToObject([
        'name' => short_Name($Competitor->name),
        'country' => strtolower($Competitor->country_iso2),
        'wcaid' => $Competitor->wca_id,
        'timeStamp' => ''
    ]);
}
foreach (DataBaseClass::getRows() as $row) {
    $competitors[$row['wid']] = arrayToObject([
        'name' => short_Name($row['name']),
        'country' => strtolower($row['country']),
        'wcaid' => $row['wcaid'],
        'timeStamp' => date('d M', strtotime($row['timeStamp']))
    ]);
}



$competitorsEvents = [];
DataBaseClass::Query("
    SELECT
        count(*) goalCount,
        Goal.Competitor wid,
        GoalDiscipline.Code event,
        SUM(Goal.Complete) completeCount
    FROM Goal
       JOIN GoalCompetition on GoalCompetition.WCA = Goal.Competition
       JOIN GoalDiscipline on GoalDiscipline.Code = Goal.Discipline
       WHERE GoalCompetition.WCA = '$competitionWca'   
    GROUP BY 
        GoalCompetition.ID,
        Goal.Competitor,
        GoalDiscipline.Code  
    ");
foreach (DataBaseClass::getRows() as $r) {
    $competitorsEvents[$r['event']][$r['wid']] = arrayToObject([
        'goalCount' => $r['goalCount'],
        'completeCount' => $r['completeCount']
    ]);
}

DataBaseClass::Query("
    SELECT
        DateStart<now() Close,
        GoalCompetition.Result Result,
        COUNT(distinct Goal.Competitor) Competitors,
        COUNT(distinct Goal.ID) Goals,
        GoalCompetition.* 
    FROM GoalCompetition
        LEFT OUTER JOIN Goal 
            ON GoalCompetition.WCA = Goal.Competition
        LEFT OUTER JOIN GoalDiscipline 
            ON GoalDiscipline.Code = Goal.Discipline
    WHERE 
        GoalCompetition.WCA = '$competitionWca'
    GROUP BY 
        GoalCompetition.ID
    ORDER BY 
        GoalCompetition.DateEnd DESC,
        GoalCompetition.WCA
    ");
$row = DataBaseClass::getRow();
$competition = arrayToObject([
    'countryImage' => "<span class='flag-icon flag-icon-" . strtolower($row['Country']) . "'></span>",
    'countryName' => CountryName($row['Country']),
    'date' => date_range($row['DateStart'], $row['DateEnd']),
    'name' => $row['Name'],
    'resultLoad' => $row['Result'],
    'close' => $row['Close'],
    'city' => $row['City'],
    'wca' => $competitionWca,
    'events' => json_decode($row['Events'])
        ]);

DataBaseClass::Query("
                SELECT
                    Goal.Discipline event,
                    Goal.Format format,
                    Goal.Result result,
                    Goal.Record record,
                    Goal.Progress progress,
                    Goal.Goal goal,
                    Goal.Complete compete,
                    Goal.Competitor wid,
                    GoalDiscipline.Name eventName 
                FROM Goal 
                JOIN GoalDiscipline ON GoalDiscipline.Code = Goal.Discipline
                WHERE
                    Competition = '{$competitionWca}'
                ORDER BY 
                    GoalDiscipline.ID,
                    Goal.Format
                ");
$goals = [];
foreach (DataBaseClass::getRows() as $row) {
    $goals[$row['wid']][$row['event']][$row['format']] = arrayToObject([
        'result' => $row['result'],
        'record' => $row['record'],
        'goal' => $row['goal'],
        'complete' => $row['compete'],
        'progress' => $row['progress']
    ]);
}

if ($upcoming) {
    $competitorRecord = getPersonRecords($Competitor->wca_id);
    $registrations = getCompetitionRegistration($competition->wca);
    if (isset($registrations[$Competitor->id])) {
        $competitorEvents = $registrations[$Competitor->id];
    } else {
        $competitorEvents = [];
    }
    $competitorEvents = ['333'];
}

if ($upcoming and ! isset($goals[$Competitor->id])) {
    $goals[$Competitor->id] = [];
    foreach ($competitorEvents as $event) {
        $goals[$Competitor->id][$event] = false;
    }
}

$icons = [];
$icons [0][0][0] = "";
$icons [1][0][0] = "";
$icons [1][1][0] = "<i style='color:var(--red)' class='far fa-star'></i>";
$icons [1][1][1] = "<i style='color:var(--green)' class='fas fa-star'></i>";
$icons [1][2][0] = "<i style='color:var(--red)' class='far fa-sun'></i>";
$icons [1][2][1] = "<i style='color:var(--light_gray)' class='fas fa-star-half-alt'></i>";
$icons [1][2][2] = "<i style='color:var(--green)' class='fas fa-sun'></i>";
$icons [0][1][0] = "<i style='color: var(--gray)' class='fas fa-hourglass-start'></i>";
$icons [0][2][0] = "<i style='color: var(--gray)' class='fas fa-hourglass'></i>";
?>

<div class="shadow">    
    <h2>
        <?= $competition->countryImage ?>
        <a href='<?= PageIndex() ?>Goals/Competition/<?= $competition->wca ?>'>
            <?= $competition->name ?>
        </a>
        <?php if ($competition->resultLoad) { ?>
            <i title='Results uploaded' style='color: var(--green)' class="fas fa-chevron-circle-down"></i>
        <?php } elseif (!$upcoming) { ?>
            <i title='Waiting for results' style='color: var(--red)' class="fas fa-hourglass-start"></i>
        <?php } else { ?>
            <i title='Upcoming competitions' style='color: var(--blue)' class="fas fa-door-open"></i>
        <?php } ?>
    </h2>
    <h3>
        <?= $competition->date ?>
        <br>
        <b><?= $competition->countryName ?></b>, 
        <?= $competition->city ?>
        <a href='https://www.worldcubeassociation.org/competitions/<?= $competition->wca ?>'>
            WCA<sup><i class=" fa-xs fas fa-external-link-alt"></i></sup>
        </a>        
    </h3>
    <?php
    foreach ($goals as $wid => $competitor) {
        if (!$upcoming or ! $Competitor or $wid != $Competitor->id) {
            ?>
            <div 
                data-competitor-div='<?= $wid ?>'
                class='shadow2' <?= (!$Competitor or $wid != $Competitor->id ) ? 'hidden' : '' ?>>
                <h2>
                    <span class='flag-icon flag-icon-<?= $competitors[$wid]->country ?>'></span>
                    <b><?= $competitors[$wid]->name ?> </b>
                    <a href='https://www.worldcubeassociation.org/persons/<?= $competitors[$wid]->wcaid ?>'>
                        <?= $competitors[$wid]->wcaid ?><sup><i class=" fa-xs fas fa-external-link-alt"></i></sup>
                    </a>
                </h2>
                <table class="table_new">
                    <thead>
                        <tr>
                            <td/>
                            <td/>
                            <?php foreach (['average', 'single'] as $format) { ?>
                                <td colspan='4'>
                                    <?= ucfirst($format) ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td/>
                            <td/>
                            <?php foreach (['average', 'single'] as $format) { ?>
                                <td align='right'>
                                    <?= $competitors[$wid]->timeStamp ?>
                                </td>
                                <td align='right'>
                                    Goal
                                </td>
                                <td align='right'>
                                    <?php if ($competition->resultLoad) { ?>
                                        Result
                                    <?php } else { ?>
                                        Progress
                                    <?php } ?>
                                </td>
                                <td>
                                </td>
                            <?php } ?>
                            <td>
                                Total
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($competitor as $eventCode => $event) {
                            if (isset($competitorsEvents[$eventCode][$wid])) {
                                $goalCount = $competitorsEvents[$eventCode][$wid]->goalCount;
                                $completeCount = $competitorsEvents[$eventCode][$wid]->completeCount;
                            } else {
                                $goalCount = false;
                                $completeCount = false;
                            }
                            ?>
                            <tr>
                                <td>
                                    <i class="cubing-icon event-<?= $eventCode ?>"></i>
                                </td>
                                <td>
                                    <b>
                                        <?= $events[$eventCode] ?>
                                    </b>
                                </td>
                                <?php foreach (['average', 'single'] as $format) { ?>
                                    <td align='right' style='border-left:1px solid var(--black)'>
                                        <?php if (isset($event[$format])) { ?>
                                            <span style='color:var(--light_gray)'>
                                                <?= $event[$format]->record ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td align='right'>
                                        <?php if (isset($event[$format])) { ?>
                                            <?= $event[$format]->goal ?>
                                        <?php } ?>
                                    </td>

                                    <td align='right'>
                                        <?php if ($competition->resultLoad and isset($event[$format])) { ?>
                                            <?php if ($event[$format]->result) { ?>
                                                <?= $event[$format]->result ?>
                                            <?php } else { ?>
                                                <i class="fas fa-times"></i>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php if (isset($event[$format]) and strpos($event[$format]->progress, '-') === FALSE) { ?>
                                                <?= $event[$format]->progress ?>
                                            <?php } ?>
                                        <?php } ?>  
                                    </td>

                                    <td align='center'>
                                        <?php if (isset($event[$format])) { ?>
                                            <?= $icons [$competition->resultLoad][$event[$format]->goal > 0][$event[$format]->complete]; ?>
                                        <?php } ?>
                                    </td>

                                <?php } ?>
                                <td style='border-left:1px solid var(--black)' align='center'>
                                    <?= $icons[$competition->resultLoad][$goalCount][$completeCount] ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>  
        <?php } else { ?>
            <div 
                data-competitor-div='<?= $wid ?>'
                class='shadow2' <?= !$Competitor or $wid != $Competitor->id ? 'hidden' : '' ?>>
                <h2>
                    <span class='flag-icon flag-icon-<?= strtolower($competitors[$wid]->country) ?>'></span>
                    <b><?= $competitors[$wid]->name ?> </b>
                    <a href='https://www.worldcubeassociation.org/persons/<?= $competitors[$wid]->wcaid ?>'>
                        <?= $competitors[$wid]->wcaid ?><sup><i class=" fa-xs fas fa-external-link-alt"></i></sup>
                    </a>
                </h2>
                <form method="POST" action="<?= PageIndex() . "Actions/GoalSet" ?>">
                    <input hidden value='<?= $Competitor->id ?>' name='Competitor'>    
                    <input hidden value='<?= $competition->wca ?>' name='Competition'>    
                    <table class='table_new'>
                        <thead>
                            <tr>
                                <td/>
                                <td/>
                                <?php foreach (['average', 'single'] as $type) { ?>
                                    <td colspan="4">
                                        <?= ucfirst($type) ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td/>
                                <td>
                                    Event
                                </td>
                                <?php foreach (['average', 'single'] as $type) { ?>
                                    <td>
                                        <?= date('d M'); ?>
                                    </td>
                                    <td>
                                        Goal
                                    </td>
                                    <td>
                                        Progress
                                    </td>
                                    <td>
                                    </td>
                                <?php } ?>                        
                                <td>
                                    Total
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($competitorEvents as $event) {
                                if (isset($competitorsEvents[$event][$wid])) {
                                    $goalCount = $competitorsEvents[$event][$wid]->goalCount;
                                } else {
                                    $goalCount = false;
                                }
                                ?>
                                <tr>
                                    <td> 
                                        <i class="cubing-icon event-<?= $event ?>"></i> 
                                    </td>
                                    <td>
                                        <?php if ($event == '333mbf') { ?>
                                            3x3x3 Multi-Blind
                                            <br>
                                            <span style='color:var(--red)'>
                                                Not implemented
                                            </span>
                                        <?php } else { ?>
                                            <?= $events[$event]; ?>
                                        <?php } ?>
                                    </td>
                                    <?php
                                    foreach (['average', 'single'] as $format) {
                                        if (isset($goals[$Competitor->id][$event][$format]->goal)) {
                                            $goal = $goals[$Competitor->id][$event][$format]->goal;
                                        } else {
                                            $goal = '';
                                        }
                                        if (!isset($competitorRecord[$event][$format])) {
                                            $competitorRecord[$event][$format] = false;
                                        }
                                        $record = GoalRecordFormat($competitorRecord[$event][$format], $event, $format);
                                        ?>
                                        <td align='right' style='border-left:1px solid var(--black)'>
                                            <?php if ($record) { ?>
                                                <?= $record ?>
                                            <?php } else { ?>
                                                <i class="fas fa-times"></i>
                                            <?php } ?>
                                            <input hidden value='<?= $record ?>' name='Data[<?= $event ?>][<?= $format ?>][Record]'>
                                        </td>
                                        <?php if ($event == '333mbf') { ?>
                                            <td/>
                                            <td/>
                                            <td/>
                                        <?php } else { ?>
                                            <td>
                                                <input name="Data[<?= $event ?>][<?= $format ?>][Goal]" maxlength='8' autocomplete="off" style="width:90px;  font-family: monospace; font-size: 16px;text-align: center" name="Value"
                                                       oninput="GoalEnter($(this),'<?= $event ?>','<?= $format ?>')"
                                                       value="<?= $goal ? $goal : '' ?>">
                                                       <?= $goal ?>
                                            </td>
                                            <td align='right'>
                                                <?= GoalProgress($record, $event, $format, $goal) ?>
                                            </td>
                                            <td align='center'>
                                                <?php if ($goal) { ?>
                                                    <i title='Waiting for results' style='color: var(--gray)' class="fas fa-hourglass-start"></i>                  
                                                <?php } ?>
                                            </td>
                                        <?php } ?>    
                                    <?php } ?>
                                    <td align='center' style='border-left:1px solid var(--black)'>
                                        <?= $icons [0][$goalCount][0]; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button>
                        <i class="far fa-save"></i>
                        Save goals
                    </button>    
                </form> 
            </div>
        <?php } ?>  
    <?php } ?>  
    <table class="table_new">
        <thead>
            <tr>
                <td/>
                <td/>
                <?php foreach ($competition->events as $event) { ?>
                    <td>
                        <h1>    
                            <i class="cubing-icon event-<?= $event ?>"></i>
                        </h1>
                    </td>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competitors as $wid => $competitor) { ?>          
                <tr>
                    <td>
                        <span class='flag-icon flag-icon-<?= $competitor->country ?>'></span>
                    </td>
                    <td data-competitor-row='<?= $wid ?>'>
                        <a href="#" <?= ($Competitor and $Competitor->id == $wid) ? 'goal_select' : '' ?>'>
                            <?= $competitor->name ?>
                        </a>
                    </td>
                    <?php
                    foreach ($competition->events as $event) {
                        if (isset($competitorsEvents[$event][$wid])) {
                            $goalCount = $competitorsEvents[$event][$wid]->goalCount;
                            $completeCount = $competitorsEvents[$event][$wid]->completeCount;
                        } else {
                            $goalCount = false;
                            $completeCount = false;
                        }
                        ?>
                        <td align='center'>
                            <?= $icons [$competition->resultLoad][$goalCount][$completeCount]; ?>
                        </td>
                    <?php } ?>      
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
<?php include 'GoalsCompetition.js' ?>
</script>
