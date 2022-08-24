<?php
$competitions = unofficial\getCompetitionsByCompetitor($competitor->id);
$organizers = [];
foreach ($competitions as $competition) {
    if ($competition->competition_competitor_name) {
        if (!isset($organizers[$competition->competition_competitor_id])) {
            $organizers[$competition->competition_competitor_id] = (object) [
                        'name' => $competition->competition_competitor_name,
                        'competitions' => []
            ];
        }
        $organizers[$competition->competition_competitor_id]->competitions[] = "<nobr><a href='" . PageIndex() . "competitions/$competition->secret'>$competition->name</a></nobr>";
    }
}
asort($organizers);
?>
<h1>
    <i class="fas fa-user"></i> 
    <?= $competitor->name ?>
</h1>
<?php
$FCIDlist = unofficial\getFCIDlistbyName($competitor->name);
if (sizeof($FCIDlist)) {
    ?>
    <div>
        <?= $ranked_icon ?>
        <?= t('View in Federation Rankings', 'Посмотреть в рейтинге Федерации') ?>: 
        <?php foreach ($FCIDlist as $FCID) { ?>
            <a href="<?= PageIndex() . "competitions/rankings/competitor/$FCID" ?>"><?= $FCID ?></a>
        <?php } ?>
    </div>
<?php } ?>
<?php if (sizeof($organizers) > 1) { ?>
    <h2><?= t('Organizers', 'Организаторы') ?></h2>
    <table class="table">
        <tbody>
            <tr></tr>
            <?php foreach ($organizers as $organizer_id => $organizer) { ?>
                <tr>
                    <td>
                        <?php if ($organizer_id == $competitor->creator_id) { ?>
                            <input checked disabled type='checkbox' >
                            <?= $organizer->name ?>
                        <?php } else { ?>
                            <input id ='organizer_<?= $organizer_id ?>' data-organizer='<?= $organizer_id ?>' type='checkbox' >
                            <label for='organizer_<?= $organizer_id ?>'>
                                <?= $organizer->name ?>
                            </label>
                        <?php } ?> 
                    </td>
                    <td>
                        <?= implode(" &bull; ", $organizer->competitions) ?>
                    </td>
                </tr>
            <?php } ?>
        <tbody>
    </table>
<?php } ?>
<br><br>
<h2>
    <?= t('Competitions', 'Соревнования') ?> 
</h2>
<table class="table" data-showing>
    <thead>
        <tr>
            <th>
                <?= t('Competition', 'Наименование') ?>
            </th>
            <th/>
            <th>
                <?= t('Date', 'Дата') ?>
            </th>
            <th>
                <?= t('Organizer', 'Организатор') ?>
            </th>
            <th>
                <?= t('Web site', 'Сайт') ?> <i class="fas fa-external-link-alt"></i>
            </th>
            <th></th>
        </tr>    
    </thead>
    <tbody>    
        <?php foreach ($competitions as $competition) { ?>
            <tr  
            <?php if ($competition->competition_competitor_id != $competitor->creator_id) { ?>
                    data-row-organizer='<?= $competition->competition_competitor_id ?>' hidden
                <?php } ?>
                >
                <td>
                    <a href="<?= PageIndex() . "competitions/$competition->secret" ?>">
                        <?= $competition->name ?>
                    </a>
                </td>
                <td>
                    <?php if ($competition->upcoming) { ?>
                        <i class="fas fa-hourglass-start"></i>
                    <?php } ?>
                    <?php if ($competition->run) { ?>
                        <i style='color:var(--green)' class="fas fa-running"></i>
                    <?php } ?>
                </td>
                <td>
                    <?= dateRange($competition->date, $competition->date_to) ?>
                </td>
                <td>
                    <?= $competition->competition_competitor_name ?>
                </td>
                <td>
                    <?php unofficial\getFavicon($competition->website, true) ?>
                </td>
                <td>
                    <a target="_blank" href="<?= PageIndex() . "competitions/competitor/$competition->competitor_id?action=certificate" ?>">
                        <i class="fas fa-certificate"></i>
                        <?= t('certificate', 'сертификат') ?> 
                    </a>
                </td>       
            </tr>
        <?php } ?>
    <tbody> 
</table>

<?php
$results = unofficial\getResutsByCompetitorMain($competitor->id);
$results_events = [];
foreach ($results as $result) {
    $results_events[$result->event_dict][] = $result;
}
?>
<br>
<h2><?= t('Results', 'Результаты') ?></h2>
<table class="table thead_stable" data-showing >
    <thead>
        <tr>
            <th><?= t('Event, Round', 'Дисциплина, Раунд') ?></th>
            <th><?= t('Competition', 'Соревнование') ?></th>
            <th><?= t('Date', 'Дата') ?></th>
            <th><?= t('Place', 'Место') ?></th>
            <th class="table_new_center">
                <?= t('Best', 'Лучшая') ?>
            </th>
            <th class="table_new_center">
                <?= t('Average', 'Среднее') ?>
            </th>

            <th>
                <?= t('Solves', 'Сборки') ?>
            </th>
        <tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($results_events as $results_event) {
            $event_result_show = false;
            foreach ($results_event as $result) {
                ?>
                <tr  
                <?php if ($result->competition_competitor_id != $competitor->creator_id) { ?>
                        data-row-organizer='<?= $result->competition_competitor_id ?>' hidden
                        <?php
                    } else {
                        $event_result_show = true;
                    }
                    ?>
                    >
                    <td>
                        <i class="<?= $result->event_image ?>"></i>
                        <?= $result->event_name ?>, 
                        <?= $result->round_name ?>  
                    </td>
                    <td>
                        <a href="<?= PageIndex() . "competitions/$result->secret" ?>">
                            <?= $result->competition_name ?>
                        </a> 
                    </td>
                    <td>
                        <?= dateRange($result->competition_date, $result->competition_date_to); ?>
                    </td>
                    <td align='center' class="<?= $result->podium ? 'podium' : '' ?>">
                        <?= $result->place ?> 
                    </td>

                    <td class='attempt' style="font-weight: bold">
                        <?= $result->best; ?>
                    </td>
                    <td class='attempt' style="font-weight: bold">
                        <?= $result->average; ?>
                        <?= $result->mean; ?>
                    </td>    
                    <?php
                    $solves = [];
                    foreach (range(1, 5) as $i) {
                        $solves[] = strtoupper(str_replace('dns', '', $result->{"attempt$i"} ?? false));
                    }
                    ?>
                    <td class='solves'>
                        <?= implode(' ', $solves) ?>
                    </td>
                </tr>    
            <?php } ?>
            <?php if ($i > 1 and $event_result_show) {
                ?>
                <tr>
                    <td colspan='11' >
                        &nbsp;
                    </td>
                </tr>    
            <?php } ?>
        <?php } ?>
    </tbody>
</table>

<script>
<?php include 'competitor.js' ?>
</script>