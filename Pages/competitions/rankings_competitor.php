<?php
$FCID = strtoupper(db::escape(request(3)));
$competitor = unofficial\getCompetitorRankings($FCID);
if (!$competitor) {
    ?>
    <div class="shadow" >
        <h3 class="error" style="padding:20px 0px;">
            <i class="far fa-hand-paper"></i> 
            Competitor [<?= $FCID ?>] not found
        </h3>
    </div>
    <?php
    exit();
}
change_title($competitor->name);
?>
<h1>
    <i class="fas fa-user"></i> <?= $competitor->name ?> [<?= $competitor->FCID ?>]
</h1> 

<?php
$results = unofficial\getResutsByCompetitorRankings($competitor->FCID);
$results_events = [];
$competitor_ids = [];
foreach ($results as $result) {
    $competitor_ids[$result->competition_id] = $result->competitor_id;
    $results_events[$result->event_dict][] = $result;
}
?>
<div>
    <h2>
        <?= t('Personal Records', 'Текущие личные рекорды') ?>
    </h2>
    <table class="table_new" data-showing>
        <thead>
            <tr>
                <td><?= t('Event', 'Дисциплина') ?></td>
                <td><?= t('Rank', 'Рейтинг') ?></td>
                <td><?= t('Single', 'Лучшая') ?></td>
                <td><?= t('Average', 'Среднее') ?></td>
                <td><?= t('Rank', 'Рейтинг') ?></td>
            <tr>
        </thead>
        <tbody>
            <?php
            foreach ($events_dict as $event_att) {
                $rating_best = $ratings[$event_att->id]['best'][$FCID] ?? false;
                $rating_average = $ratings[$event_att->id]['average'][$FCID] ?? false;
                $top_rating_best = ($rating_best->order ?? false) <= 10;
                $top_rating_average = ($rating_average->order ?? false) <= 10;
                ?>
                <?php if ($rating_best or $rating_average) { ?>
                    <tr>
                        <td>
                            <i class="<?= $event_att->image ?>"></i>
                            <?= $event_att->name ?>
                        </td>
                        <td align='center' class="<?= $top_rating_best ? 'podium' : '' ?>">
                            <?= $rating_best->order ?? false ?>
                        </td>
                        <td align='center' > <?= $rating_best->result ?? false ?></td>
                        <td align='center' > <?= $rating_average->result ?? false ?></td>
                        <td align='center' class="<?= $top_rating_average ? 'podium' : '' ?>">
                            <?= $rating_average->order ?? false ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

        </tbody>
    </table>
</div>

<div>
    <h2><?= t('Results', 'Результаты') ?></h2>
    <table class="table_new" data-showing>
        <thead>
            <tr>
                <td><?= t('Event', 'Дисциплина') ?></td>
                <td><?= t('Competition', 'Соревнование') ?></td>
                <td><?= t('Round', 'Раунд') ?></td>
                <td><?= t('Place', 'Место') ?></td>
                <td class="attempt">
                    <?= t('Single', 'Лучшая') ?>
                </td>
                <td class="attempt">
                    <?= t('Average', 'Среднее') ?>
                </td>
                <td class="table_new_center" colspan="5">
                    <?= t('Solves', 'Сборки') ?>
                </td>
            <tr>
        </thead>
        <tbody>
            <?php
            foreach ($events_dict as $event_dict) {
                foreach ($results_events[$event_dict->id] ?? [] as $result) {
                    $record_best = in_array($result->result_id, $competitor_history_record[$competitor->FCID]['best'] ?? []);
                    $record_average = in_array($result->result_id, $competitor_history_record[$competitor->FCID]['average'] ?? []);
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $result->event_image ?>"></i>
                            <?= $result->event_name ?>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/$result->secret" ?>">
                                <?= $result->competition_name ?>
                            </a> 
                        </td>
                        <td>
                            <?= $rounds_dict[$result->final ? 0 : $result->round]->smallName; ?>
                        </td>
                        <td align='center' class="<?= $result->podium ? 'podium' : '' ?>">
                            <?= $result->place ?> 
                        </td>
                        <td class='attempt <?= $record_best ? 'record' : '' ?>' style="font-weight: bold">
                            <?= strtoupper($result->best); ?>
                        </td>
                        <td class='attempt <?= $record_average ? 'record' : '' ?>' style="font-weight: bold">
                            <?= strtoupper(str_replace(['dns', '-cutoff'], ['', 'dnf'], $result->average)); ?>
                            <?= strtoupper(str_replace(['dns', '-cutoff'], ['', 'dnf'], $result->mean)); ?>
                        </td>
                        <?php foreach (range(1, 5) as $i) { ?>
                            <td class='attempt'>
                                <?= strtoupper(str_replace('dns', '', $result->{"attempt$i"})); ?>
                            </td>
                        <?php } ?>
                    </tr>    
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php $competitions = unofficial\getRankedCompetitionsbyCompetitor($competitor->FCID); ?>
<div>
    <h2>
        <?= t('Competitions', 'Соревнования') ?>
    </h2>

    <table class='table_new'>
        <thead>
            <tr>
                <td>
                    <?= t('Competition', 'Наименование') ?>
                </td>
                <td>
                    <?= t('Judge', 'Судья') ?>
                </td>
                <td>
                    <?= t('Organizer', 'Организатор') ?>
                </td>
                <td/>
                <td>
                    <?= t('Date', 'Дата') ?>
                </td>
                <td>
                    <?= t('Web site', 'Сайт') ?>
                </td>
            </tr>    
        </thead>
        <tbody>
            <?php foreach ($competitions as $competition) { ?>
                <tr>   
                    <td>                    
                        <a href="<?= PageIndex() ?>competitions/<?= $competition->secret ?>"><?= $competition->name ?> </a>
                    </td>
                    <td><?php
                        $judges = [];
                        if ($competition->judgeSenior_name) {
                            $judges[] = $competition->judgeSenior_name;
                        }
                        if ($competition->judgeJunior_name) {
                            $judges[] = $competition->judgeJunior_name;
                        }
                        ?>
                        <?= implode(', ', $judges); ?>
                    </td>      
                    <td>
                        <?= $competition->competitor_name ?>
                    </td>      
                    <td>
                        <?php if ($competition->upcoming) { ?>
                            <i style='color:var(--gray)' class="fas fa-hourglass-start"></i>
                        <?php } ?>
                        <?php if ($competition->run) { ?>
                            <i style='color:var(--green)' class="fas fa-running"></i>
                        <?php } ?>
                    </td>
                    <td>
                        <?= dateRange($competition->date, $competition->date_to) ?>
                    </td>
                    <td>
                        <?php unofficial\getFavicon($competition->website) ?>
                    </td>
                    <td>
                        <a target="_blank" href="<?= PageIndex() . "competitions/competitor/{$competitor_ids[$competition->id]}?action=certificate" ?>">
                            <i class="fas fa-certificate"></i>
                            <?= t('certificate', 'сертификат') ?>
                        </a>
                    </td>     
                </tr>
            <?php } ?>
        </tbody>
    </table> 
</div>

<?php if (unofficial\admin()) { ?>
    <form method="POST" action="?ranking_competitor">    
        Name <input required name="name" value="<?= $competitor->name ?>"><br>
        FC ID<input required pattern="[A-Z][A-Z]" maxlength="2" name="FCID" value="<?= substr($competitor->FCID, 0, 2) ?>"><br>
        <input hidden name="current_name" value="<?= $competitor->name ?>">
        <input hidden name="current_FCID" value="<?= $competitor->FCID ?>">
        <input hidden name="current_ID" value="<?= $competitor->ID ?>">
        <button>
            <i class="fas fa-user"></i>
            Rename
        </button>
    </form>

<?php } ?>