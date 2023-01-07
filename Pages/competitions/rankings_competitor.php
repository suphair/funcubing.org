<?php
$FCID = strtoupper(db::escape(request(3)));
$competitor = unofficial\getCompetitorRankings($FCID);
$current_event = filter_input(INPUT_GET, 'event');
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
$wca_name = $competitor->wca_name ? $competitor->wca_name : transliterate($competitor->name);
change_title(t($wca_name, $competitor->name));
$competitor_name = trim(str_replace($FCID, '', $competitor->name));
$wca_name = trim(str_replace($FCID, '', $wca_name));
$wca = unofficial\get_wca($FCID);
?>
<h1>
    <i class="fas fa-user"></i>
    <?= t($wca_name, $competitor_name) ?> (<?= t($competitor_name, $wca_name) ?>) <?= $competitor->FCID ?> 
</h1> 

<?php if ($wca->id ?? false) { ?>
    <p>
        <?php
        db2::set($get(2));
        $wca_person = \db2::row("
            select name, countryId, id, gender from Persons where lower(id) = lower('$wca->id')
            order by subid desc limit 1");
        $outer_results = [];
        foreach (\db2::rows("
            select 'WCA' source, eventId, best, 'single' type from RanksSingle where lower(personId) = lower('$wca->id')
            union
            select 'WCA' source, eventId, best, 'average' type from RanksAverage where lower(personId) = lower('$wca->id')") as $row) {
            if ($row->eventId == '333fm' and $row->type == 'single') {
                $row->format = $row->best;
            }if ($row->eventId == '333mbf') {
                $row->format = multiblind_to_string($row->best);
            } else {
                $row->format = santiceconds_to_string($row->best);
            }
            $outer_results[$row->eventId][$row->type] = $row;
        }

        if ($wca_person) {
            ?>
            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $wca->id ?>'>
                <?= t('WCA profile', 'Профиль WCA') ?> <?= $wca->id ?> <i class="fas fa-external-link-alt"></i></a>
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
<?php if (api\get_me()->is_federation ?? false or api\get_me()->is_admin ?? false) { ?>
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
                    Имя EN
                </td>
                <td>
                    <input required name="wca_name" value="<?= $competitor->wca_name ?>">
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
        <input hidden name="current_wca_name" value="<?= $competitor->wca_name ?>">
        <input hidden name="current_FCID" value="<?= $competitor->FCID ?>">
        <input hidden name="current_ID" value="<?= $competitor->ID ?>">
    </form>
    <br>
<?php } ?>
<h2>
    <?= t('Personal Records', 'Текущие личные рекорды') ?>
</h2>
<table class="table">
    <thead>
        <tr>
            <th><?= t('Event', 'Дисциплина') ?></th>
            <th class='attempt'><?= t('Rank', 'Рейтинг') ?></th>
            <th class='attempt'><?= t('Single', 'Лучшая') ?></th>
            <?php if ($wca->id ?? false) { ?>
                <th class='attempt' style='color:gray'>WCA<sup>*</sup></th>
                <th class='attempt' style='color:gray'>WCA<sup>*</sup></th>
            <?php } ?>
            <th class='attempt'><?= t('Average', 'Среднее') ?></th>
            <th class='attempt'><?= t('Rank', 'Рейтинг') ?></th>
        <tr>
    </thead>
    <tbody>
        <?php
        foreach ($events_dict as $event_att) {
            if ($event_att->special) {
                continue;
            }
            if (!in_array($ratings[$event_att->id]['best'][$FCID]->competition_id ?? false, explode(',', \config::get('MISC', 'competition_exclude')))) {
                $rating_best = $ratings[$event_att->id]['best'][$FCID] ?? false;
            } else {
                $rating_best = false;
            }
            if (!in_array($ratings[$event_att->id]['average'][$FCID]->competition_id ?? false, explode(',', \config::get('MISC', 'competition_exclude')))) {
                $rating_average = $ratings[$event_att->id]['average'][$FCID] ?? false;
            } else {
                $rating_average = false;
            }
            $top_rating_best = ($rating_best->order ?? false) <= 10;
            $top_rating_average = ($rating_average->order ?? false) <= 10;
            ?>
            <?php
            if ($rating_best or $rating_average or $outer_results[$event_att->code] ?? false) {
                if ($wca->id ?? false) {
                    $wca_single_beat = false;
                    $wca_average_beat = false;
                    $wca_record_single = $outer_results[$event_att->code]['single']->best ?? false;
                    if ($event_att->code == '333mbf') {
                        $fc_record_single = $rating_best->order_raw ?? false;
                    } else {
                        $fc_record_single = string_to_santiceconds($rating_best->result ?? false);
                    }
                    if ($fc_record_single > 0 and ($fc_record_single < $wca_record_single or $wca_record_single <= 0)) {
                        $wca_single_beat = true;
                    }
                    $wca_record_average = $outer_results[$event_att->code]['average']->best ?? false;
                    $fc_record_average = string_to_santiceconds($rating_average->result ?? false);
                    if ($fc_record_average > 0 and ($fc_record_average < $wca_record_average or $wca_record_average <= 0)) {
                        $wca_average_beat = true;
                    }
                }
                ?>
                <tr>
                    <td 
                    <?php if (sizeof($results_events[$event_att->id] ?? [])) { ?>
                            data-event-record="<?= $event_att->code ?>"
                        <?php } ?>>
                        <i class="<?= $event_att->image ?>"></i>
                        <?= $event_att->name ?>
                    </td>
                    <td class='attempt <?= $top_rating_best ? 'podium' : '' ?>'">
                        <?= $rating_best->order ?? false ?>
                    </td>
                    <td class='attempt' >
                        <?= $rating_best->result ?? '-' ?>
                    </td>
                    <?php if ($wca->id ?? false) { ?>
                        <td class='attempt <?= $wca_single_beat ? 'wca_beat' : '' ?>'>
                            <?php $r = $outer_results[$event_att->code]['single'] ?? false ?>
                            <?php if ($r) { ?>
                                <?= $r->format ?? false ?>
                            <?php } ?>
                        </td>
                        <td class='attempt <?= $wca_average_beat ? 'wca_beat' : '' ?>'>
                            <?php $r = $outer_results[$event_att->code]['average'] ?? false ?>
                            <?php if ($r) { ?>
                                <?= $r->format ?? false ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td class='attempt' > <?= $rating_average->result ?? '-' ?></td>
                    <td class='attempt <?= $top_rating_average ? 'podium' : '' ?>'>
                        <?= $rating_average->order ?? false ?>
                    </td>
                </tr>
            <?php } ?>
            <?php
        }
        ?>

    </tbody>
</table>
<br>
<h2><?= t('Results', 'Результаты') ?>
    <font size='5'>
    <?php
    $need_results_scroll = (bool) $current_event;
    $ee = true;
    foreach (array_keys($results_events) as $event_id) {
        $event_dict = $events_dict[$event_id];
        if ($event_dict->special and!$event_dict->extraevents) {
            continue;
        }
        if (!$current_event) {
            $current_event = $event_dict->code;
        }
        ?>
        <?php if ($event_dict->extraevents and $ee) { ?>
            |
            <?php
            $ee = false;
        }
        ?>
        <a data-event-select="<?= $event_dict->code ?>" href="?event=<?= $event_dict->code ?>">
            <i title='<?= $event_dict->name ?>' 
               class='<?= $current_event == $event_dict->code ? 'select' : '' ?> <?= $event_dict->image ?>'></i></a>
        <?php } ?>
    </font>
</h2>
<span  data-results-scroll="<?= $need_results_scroll ?>"></span>
<?php
foreach ($events_dict as $event_dict) {
    if ($event_dict->special and!$event_dict->extraevents) {
        continue;
    }
    ?>    
    <table class="table" 
           data-results-event="<?= $event_dict->code ?>" 
           <?= $event_dict->code != $current_event ? 'hidden' : '' ?>>
        <thead>
            <tr>
                <th>
                    <i class="<?= $event_dict->image ?>"></i>
                    <?= $event_dict->name ?>
                </th>
                <th><?= t('Round', 'Раунд') ?></th>
                <th class="attempt">
                    <?= t('Single', 'Лучшая') ?>
                </th>
                <th class="attempt">
                    <?= t('Average', 'Среднее') ?>
                </th>
                <th class='center'><?= t('Place', 'Место') ?></th>
                <th>
                    <?= t('Solves', 'Сборки') ?>
                </th>
            <tr>
        </thead>
        <tbody>
            <?php
            $results_events_reverse = array_reverse($results_events[$event_dict->id] ?? [], true);
            $prev_single = str_replace(['.', ':'], '', $wca_results[$event_dict->code]['single']->format ?? false);
            $prev_average = str_replace(['.', ':'], '', $wca_results[$event_dict->code]['average']->format ?? false);
            foreach ($results_events_reverse as $r => $result) {
                $current_single = str_replace(['.', ':'], '', $result->best);
                $current_average = str_replace(['.', ':'], '', $result->average . $result->mean);
                if (!is_numeric($current_single)) {
                    $current_single = false;
                }
                if (!is_numeric($current_average)) {
                    $current_average = false;
                }
                if (!$prev_single or ($current_single and $current_single < $prev_single)) {
                    $results_events[$event_dict->id][$r]->pb_single = true;
                    $prev_single = $current_single;
                }
                if (!$prev_average or ($current_average and $current_average < $prev_average)) {
                    $results_events[$event_dict->id][$r]->pb_average = true;
                    $prev_average = $result->average . $result->mean;
                }
            }

            foreach ($results_events[$event_dict->id] ?? [] as $result) {
                $record_best = in_array($result->result_id, $competitor_history_record[$competitor->FCID]['best'] ?? []);
                $record_average = in_array($result->result_id, $competitor_history_record[$competitor->FCID]['average'] ?? []);
                ?>
                <tr>
                    <td>
                        <a href="<?= PageIndex() . "competitions/$result->secret" ?>">
                            <?= $result->competition_name ?>
                        </a> 
                    </td>
                    <td>
                        <?= $rounds_dict[$result->final ? 0 : $result->round]->smallName; ?>
                    </td>
                    <td class='attempt <?= $record_best ? 'record' : '' ?>  <?= ($result->pb_single ?? false) ? 'personal_best' : '' ?>'>
                        <?= strtoupper($result->best); ?>
                    </td>
                    <td class='attempt <?= $record_average ? 'record' : '' ?> <?= ($result->pb_average ?? false) ? 'personal_best' : '' ?>'>
                        <?= $result->average; ?>
                        <?= $result->mean; ?>
                    </td>
                    <td  class="center <?= $result->podium ? 'podium' : '' ?>">
                        <?= $result->place ?> 
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
                <?php
            }
            $wca_record_single = $wca_results[$event_dict->code]['single']->format ?? false;
            $wca_record_average = $wca_results[$event_dict->code]['average']->format ?? false;
            if ($wca_record_single or $wca_record_average) {
                ?>
                <tr>
                    <td>
                        WCA Personal Best
                    </td>
                    <td/>
                    <td class="attempt personal_best">
                        <?= $wca_record_single ?>
                    </td>
                    <td class="attempt personal_best">
                        <?= $wca_record_average ?>
                    </td>
                    <td/>
                    <td/>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
<br>

<?php $competitions = unofficial\getRankedCompetitionsbyCompetitor($competitor->FCID); ?>
<h2>
    <?= t('Competitions', 'Соревнования') ?>
</h2>
<table class='table'>
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
                <?= t('Web site', 'Сайт') ?> <i class="fas fa-external-link-alt"></i>
            </th>
            <th></th>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($competitions as $competition) { ?>
            <tr>   
                <td>                    
                    <a href="<?= PageIndex() ?>competitions/<?= $competition->secret ?>"><?= $competition->name ?> </a>
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
                    <?php unofficial\getFavicon($competition->website, true) ?>
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

function multiblind_to_string($input) {
    $T = substr($input, 2, 5);
    $min = floor($T / 60);
    $sec = $T - $min * 60;
    $time = sprintf("%02d:%02d", $min, $sec);
    $difference = 99 - substr($input, 0, 2);
    $missed = substr($input, 7, 2);
    $solved = $difference + $missed;
    $attempted = $solved + $missed;

    return "$solved/$attempted $time";
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

<script>
<?php include 'rankings_competitor.js' ?>
</script>