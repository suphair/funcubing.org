<?php
$upcoming = in_array($comp, $upcomings);

$events = [];
foreach (db::rows("SELECT code, name FROM goals_events ORDER BY ID") as $row) {
    $events[$row->code] = $row->name;
}

$rowsCompetitors = goals\getCompetitors($comp);
$competitors = [];
if ($upcoming) {
    $competitors[$me->id] = (object) [
                'name' => $me->name,
                'country' => strtolower($me->country_iso2),
                'wcaid' => $me->wca_id,
                'timeStamp' => ''
    ];
}

foreach ($rowsCompetitors as $row) {
    $competitors[$row->wid] = (object) [
                'name' => $row->name,
                'country' => strtolower($row->country),
                'wcaid' => $row->wcaid,
                'timestamp' => date('d M', strtotime($row->timestamp))
    ];
}

$competitorsEvents = [];
$rowsCount = goals\getCount($comp);
foreach ($rowsCount as $row) {
    $competitorsEvents[$row->event][$row->wid] = (object) [
                'goalCount' => $row->goalCount,
                'completeCount' => $row->completeCount
    ];
}

$rowCompetition = goals\getCompetition($comp);
$competition = (object) [
            'countryImage' => "<span class='flag-icon flag-icon-" . strtolower($rowCompetition->country) . "'></span>",
            'countryName' => $rowCompetition->country_name,
            'date' => dateRange($rowCompetition->dateStart, $rowCompetition->dateEnd),
            'name' => $rowCompetition->name,
            'resultsLoad' => $rowCompetition->resultsLoad,
            'close' => $rowCompetition->close,
            'city' => $rowCompetition->city,
            'wca' => $comp,
            'events' => json_decode($rowCompetition->events)
];

$rowsGoal = goals\getGoals($comp);
$goals = [];
foreach ($rowsGoal as $row) {
    $goals[$row->wid][$row->event][$row->format] = (object) [
                'result' => $row->result,
                'record' => $row->record,
                'goal' => $row->goal,
                'complete' => $row->complete,
                'progress' => $row->progress
    ];
}
if ($upcoming) {
    $competitorRecord = goals\getPersonRecords($me->wca_id);
    $registrations = goals\getCompetitionRegistration($competition->wca);
    if (isset($registrations[$me->id])) {
        $competitorEvents = $registrations[$me->id];
    } else {
        $competitorEvents = [];
    }
}

if ($upcoming and!isset($goals[$me->id])) {
    $goals[$me->id] = [];
    foreach ($competitorEvents as $event) {
        $goals[$me->id][$event] = false;
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
        <a href='<?= PageIndex() ?>goals/<?= $competition->wca ?>'>
            <?= $competition->name ?>
        </a>
        <?php if ($competition->resultsLoad) { ?>
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
        if (!$upcoming or $wid != ($me->id ?? FALSE)) {
            ?>
            <div 
                data-competitor-div='<?= $wid ?>'
                class='shadow2' <?= $wid != ($me->id ?? FALSE) ? 'hidden' : '' ?>>
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
                                    <?= $competitors[$wid]->timestamp ?>
                                </td>
                                <td align='right'>
                                    Goal
                                </td>
                                <td align='right'>
                                    <?php if ($competition->resultsLoad) { ?>
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
                            $goalCount = $competitorsEvents[$eventCode][$wid]->goalCount ?? FALSE;
                            $completeCount = $competitorsEvents[$eventCode][$wid]->completeCount ?? FALSE;
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
                                    <?php
                                    $event[$format] ??= (object) [
                                                'record' => FALSE,
                                                'goal' => FALSE,
                                                'result' => null,
                                                'progress' => FALSE
                                    ];
                                    ?>
                                    <td align='right' style='border-left:1px solid var(--black)'>
                                        <span style='color:var(--light_gray)'>
                                            <?= $event[$format]->record ?>
                                        </span>
                                    </td>
                                    <td align='right'>
                                        <?= $event[$format]->goal ?>
                                    </td>

                                    <td align='right'>
                                        <?php if ($competition->resultsLoad) { ?>
                                            <?php if ($event[$format]->result) { ?>
                                                <?= $event[$format]->result ?>
                                            <?php } elseif ($event[$format]->goal) { ?>
                                                <i class="fas fa-times"></i>
                                            <?php } ?>                                            
                                        <?php } else { ?>
                                            <?php if (strpos($event[$format]->progress, '-') === FALSE) { ?>
                                                <?= $event[$format]->progress ?>
                                            <?php } ?>
                                        <?php } ?>  
                                    </td>

                                    <td align='center'>
                                        <?php if ($event[$format]->goal) { ?>
                                            <?= $icons [$competition->resultsLoad][$event[$format]->goal > 0][$event[$format]->complete]; ?>
                                        <?php } ?>
                                    </td>

                                <?php } ?>
                                <td style='border-left:1px solid var(--black)' align='center'>
                                    <?= $icons[$competition->resultsLoad][$goalCount][$completeCount] ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>  
        <?php } else { ?>
            <div 
                data-competitor-div='<?= $wid ?>'
                class='shadow2' <?= $wid != $me->id ?? FALSE ? 'hidden' : '' ?>>
                <h2>
                    <span class='flag-icon flag-icon-<?= strtolower($competitors[$wid]->country) ?>'></span>
                    <b><?= $competitors[$wid]->name ?> </b>
                    <a href='https://www.worldcubeassociation.org/persons/<?= $competitors[$wid]->wcaid ?>'>
                        <?= $competitors[$wid]->wcaid ?><sup><i class=" fa-xs fas fa-external-link-alt"></i></sup>
                    </a>
                </h2>
                <form method="POST" action="?set">
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
                            <?php foreach ($competitorEvents as $event) { ?>
                                <?php $goalCount = $competitorsEvents[$event][$wid]->goalCount ?? FALSE; ?>
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
                                        $goal = $goals[$me->id][$event][$format]->goal ?? '';
                                        $record = goals\recordFormat($competitorRecord[$event][$format] ?? FALSE, $event, $format);
                                        ?>
                                        <td align='right' style='border-left:1px solid var(--black)'>
                                            <?php if ($record) { ?>
                                                <?= $record ?>
                                            <?php } else { ?>
                                                <i class="fas fa-times"></i>
                                            <?php } ?>
                                            <input hidden value='<?= $record ?>' name='data[<?= $event ?>][<?= $format ?>][record]'>
                                        </td>
                                        <?php if ($event == '333mbf') { ?>
                                            <td/>
                                            <td/>
                                            <td/>
                                        <?php } else { ?>
                                            <td>
                                                <input name="data[<?= $event ?>][<?= $format ?>][goal]" maxlength='8' autocomplete="off" style="width:90px;  font-family: monospace; font-size: 16px;text-align: center" name="Value"
                                                       oninput="GoalEnter($(this),'<?= $event ?>','<?= $format ?>')"
                                                       value="<?= $goal ?: '' ?>">
                                                       <?= $goal ?>
                                            </td>
                                            <td align='right'>
                                                <?= goals\progress($record, $goal) ?>
                                            </td>
                                            <td align='center'>
                                                <?php if ($goal) { ?>
                                                    <i title='Waiting for results' style='color: var(--gray)' class="fas fa-hourglass-start"></i>                  
                                                <?php } ?>
                                            </td>
                                        <?php } ?>    
                                    <?php } ?>
                                    <td align='center' style='border-left:1px solid var(--black)'>
                                        <?= $icons[0][$goalCount][0]; ?>
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
                        <a href="#" class='<?= $wid == ($me->id ?? FALSE) ? 'goal_select' : '' ?>'>
                            <?= $competitor->name ?>
                        </a>
                    </td>
                    <?php foreach ($competition->events as $event) { ?>
                        <?php $goalCount = $competitorsEvents[$event][$wid]->goalCount ?? FALSE ?>
                        <?php $completeCount = $competitorsEvents[$event][$wid]->completeCount ?? FALSE ?>
                        <td align='center'>
                            <?= $icons[$competition->resultsLoad][$goalCount][$completeCount]; ?>
                        </td>
                    <?php } ?>      
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
<?php include('competition.js') ?>
</script>