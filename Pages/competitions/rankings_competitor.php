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
$wca = unofficial\get_wca($FCID);
?>
<h1>
    <i class="fas fa-user"></i>
    <?= $competitor->name ?> [<?= $competitor->FCID ?>]
</h1> 

<?php if ($wca->id ?? false) { ?>
    <p>
        <?php
        $wca_person = \db2::row("
            select name, countryId, id, gender from Persons where lower(id) = lower('$wca->id')
            order by subid desc limit 1");
        $wca_results = [];
        foreach (\db2::rows("
            select eventId, best, 'single' type from RanksSingle where lower(personId) = lower('$wca->id')
            union
            select eventId, best, 'average' type from RanksAverage where lower(personId) = lower('$wca->id')") as $row) {
            if ($row->eventId == '333fm' and $row->type == 'single') {
                $row->format = $row->best;
            } else {
                $row->format = santiceconds_to_string($row->best);
            }
            $wca_results[$row->eventId][$row->type] = $row;
        }
        if ($wca_person) {
            ?>
            <?= $wca->id ?>:
            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $wca->id ?>'>
                <?= $wca_person->name ?>
                <i class="fas fa-external-link-alt"></i>
            </a>
            <?= $wca_person->countryId ?> (<?= $wca_person->gender ?>)
        <?php } else { ?>
            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $wca->id ?>'>
                <?= $wca->id ?>
                <i class="fas fa-external-link-alt"></i>
            </a>
        <?php } ?>
    </p>
<?php } elseif ($wca->nonwca ?? false) { ?>
    <p>
        <?= t('No WCA ID', 'Нет WCA ID') ?>
    </p>
<?php } ?>
<?php
$results = unofficial\getResutsByCompetitorRankings($competitor->FCID);
$results_events = [];
$competitor_ids = [];
foreach ($results as $result) {
    $competitor_ids[$result->competition_id] = $result->competitor_id;
    $results_events[$result->event_dict][] = $result;
}
?>
<?php if (unofficial\federation()) { ?>
    <h2>Управление</h2>
    <form method="POST" action="?ranking_competitor">    
        <table class='table_info'>
            <tr>
                <td>
                    Имя
                </td>
                <td>
                    <input required name="name" value="<?= $competitor->name ?>">
                </td>
            </tr>
            <tr>
                <td>
                    Префикс FC ID
                </td>
                <td>
                    <input required pattern="[A-Z][A-Z]" maxlength="2" name="FCID" value="<?= substr($competitor->FCID, 0, 2) ?>"><br>
                </td>
            </tr>
            <tr>
                <td>
                    WCA ID
                </td>
                <td>
                    <input pattern="[0-9]{4}[A-Za-z]{4}[0-9]{2}" maxlength="10" title='NNNNXXXXNN' name="WCAID" value="<?= $wca->id ?? false ?>"><br>
                </td>
                <td>
                    <input type='checkbox' name='nonwca' <?= $wca->nonwca ?? false ? 'checked' : '' ?> >
                    Нет на WCA
                </td>
            </tr>
            <tr>
                <td>

                </td>
                <td>
                    <button>
                        <i class="fas fa-user"></i>
                        Изменить
                    </button>
                </td>
            </tr>
        </table>   
        <input hidden name="current_name" value="<?= $competitor->name ?>">
        <input hidden name="current_FCID" value="<?= $competitor->FCID ?>">
        <input hidden name="current_ID" value="<?= $competitor->ID ?>">
    </form>
    <br>
<?php } ?>
<h2>
    <?= t('Personal Records', 'Текущие личные рекорды') ?>
</h2>
<table class="table_new" data-showing>
    <thead>
        <tr>
            <td><?= t('Event', 'Дисциплина') ?></td>
            <td><?= t('Rank', 'Рейтинг') ?></td>
            <td><?= t('Single', 'Лучшая') ?></td>
            <?php if ($wca->id ?? false) { ?>
                <td  style='color:var(--gray)'><?= t('WCA Single', 'WCA Лучшая') ?></td>
                <td  style='color:var(--gray)'><?= t('WCA Average', 'WCA Среднее') ?></td>
            <?php } ?>
            <td><?= t('Average', 'Среднее') ?></td>
            <td><?= t('Rank', 'Рейтинг') ?></td>
        <tr>
    </thead>
    <tbody>
        <?php
        foreach ($events_dict as $event_att) {
            if (!in_array($ratings[$event_att->id]['best'][$FCID]->competition_id ?? false, explode(',', \config::get('MISC', 'competition_exclude')))) {
                $rating_best = $ratings[$event_att->id]['best'][$FCID] ?? false;
                $rating_average = $ratings[$event_att->id]['average'][$FCID] ?? false;
                $top_rating_best = ($rating_best->order ?? false) <= 10;
                $top_rating_average = ($rating_average->order ?? false) <= 10;
                ?>
                <?php
                if ($rating_best or $rating_average or $wca_results[$event_att->code] ?? false) {
                    if ($wca->id ?? false) {
                        $wca_single_beat = false;
                        $wca_average_beat = false;
                        $wca_record_single = $wca_results[$event_att->code]['single']->best ?? false;
                        $fc_record_single = string_to_santiceconds($rating_best->result ?? false);
                        if ($fc_record_single > 0 and ($fc_record_single < $wca_record_single or $wca_record_single <= 0)) {
                            $wca_single_beat = true;
                        }
                        $wca_record_average = $wca_results[$event_att->code]['average']->best ?? false;
                        $fc_record_average = string_to_santiceconds($rating_average->result ?? false);
                        if ($fc_record_average > 0 and ($fc_record_average < $wca_record_average or $wca_record_average <= 0)) {
                            $wca_average_beat = true;
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $event_att->image ?>"></i>
                            <?= $event_att->name ?>
                        </td>
                        <td align='center' class="<?= $top_rating_best ? 'podium' : '' ?>">
                            <?= $rating_best->order ?? false ?>
                        </td>
                        <td align='center' >
                            <?= $rating_best->result ?? false ?>
                        </td>
                        <?php if ($wca->id ?? false) { ?>
                            <td align='center' class='<?= $wca_single_beat ? 'wca_beat' : '' ?>'>
                                <?= $wca_results[$event_att->code]['single']->format ?? false ?>
                            </td>
                            <td align='center' class='<?= $wca_average_beat ? 'wca_beat' : '' ?>'>
                                <?= $wca_results[$event_att->code]['average']->format ?? false ?>
                            </td>
                        <?php } ?>
                        <td align='center' > <?= $rating_average->result ?? false ?></td>
                        <td align='center' class="<?= $top_rating_average ? 'podium' : '' ?>">
                            <?= $rating_average->order ?? false ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php
            }
        }
        ?>

    </tbody>
</table>
<br>
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
<br>

<?php $competitions = unofficial\getRankedCompetitionsbyCompetitor($competitor->FCID); ?>
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
                    <?php if ($competitor_ids[$competition->id] ?? false) { ?>
                        <a target="_blank" href="<?= PageIndex() . "competitions/competitor/" . $competitor_ids[$competition->id] . "?action=certificate" ?>">
                            <i class="fas fa-certificate"></i>
                            <?= t('certificate', 'сертификат') ?>
                        </a>
                    <?php } ?>
                </td>     
            </tr>
        <?php } ?>
    </tbody>
</table> 

<?php

function santiceconds_to_string($input) {
    if ($input) {
        $minute = floor($input / 6000);
        $second = floor(($input - $minute * 6000) / 100);
        $centisecond = $input - $minute * 6000 - $second * 100;
        $format = '';
        if ($minute) {
            $format .= $minute;
        }
        if ($format) {
            $format .= ":" . substr('0' . $second, -2);
        } else {
            $format .= $second;
        }
        $format .= "." . substr('0' . $centisecond, -2);
        return $format;
    } else {
        return "DNF";
    }
}

function string_to_santiceconds($input) {
    $input = str_replace(['(', ' ', '(', 'dnf', 'dns', '-', 'cutoff', '.', ':'], '', strtolower($input));
    $input = substr('000000' . $input, -6);
    $minute = substr($input, 0, 2);
    $second = substr($input, 2, 2);
    $centisecond = substr($input, 4, 2);
    return $minute * 60 * 100 + $second * 100 + $centisecond;
}
?>