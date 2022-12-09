<?php
$event_code = db::escape(request(2));
$select = false;
$rename_aviable = (api\get_me()->is_federation ?? false or \api\get_me()->is_admin ?? false);
if (!$event_code) {
    $select = 'records';
}
if ($event_code == 'competitors') {
    $select = 'competitors';
}
if ($event_code == 'competitor') {
    $select = 'competitor';
}
if ($event_code == 'competitions') {
    $select = 'competitions';
}
if ($event_code == 'delegates') {
    $select = 'delegates';
}
if ($event_code == 'rename') {
    $select = 'rename';
}
if (!$select) {
    $select = 'event';
}

change_title(t('Russian Speecubing Federation', 'Федерация Спидкубинга России'));

$type = db::escape(request(3)) == 'average' ? 'average' : 'best';
list('current' => $ratings, 'history' => $history) = unofficial\getRankedRatings();

foreach (['average', 'best'] as $type_att) {
    foreach ($ratings as $event_att => $a) {
        $current = current($a[$type_att] ?? []) ?? false;
        if ($current) {
            $competitor_current_record[$current->FCID][$type_att] ??= [];
            $competitor_current_record[$current->FCID][$type_att][] = $current;
        }
    }
}

function sort_history($a, $b) {
    return $a->date > $b->date;
}

foreach (['average', 'best'] as $type_att) {
    foreach (array_keys($history) as $event) {
        if ($history[$event][$type_att] ?? false) {
            usort($history[$event][$type_att], 'sort_history');
        }
    }

    foreach ($history as $event_att => $a) {
        $best = 999999;
        foreach ($a[$type_att] ?? [] as $i => $row) {
            if (strpos($row->result, ' ') !== -1) {
                $result_int = $row->order;
            } else {
                $result_int = str_replace(['.', ':'], '', $row->result) + 0;
            }
            if ($result_int >= $best) {
                unset($history[$event_att][$type_att][$i]);
            } else {
                $best = $result_int;
                $competitor_history_record[$row->FCID][$type_att] ??= [];
                $competitor_history_record[$row->FCID][$type_att][] = $row->round_id;
            }
        }
    }
}
?>
<p> <?= $ranked_icon ?> <?=
    t('In the rankings only <a href="http://CubingRF.org">Speedcubing Federation</a> competitions are included.',
            'В рейтинге участвуют только соревнования под эгидой <a href="http://CubingRF.org">Федерации Спидкубинга</a>.')
    ?>
</p>
<table width='100%'>
    <tr>
        <td class="navigator_event">
            <a href= '<?= PageIndex() ?>competitions/rankings' class='<?= $select == 'records' ? 'select' : '' ?>'><i title='<?= t('Records', 'Рекорды') ?>' class="fas fa-trophy"></i></a>
            <a href= '<?= PageIndex() ?>competitions/rankings/competitors' class='<?= in_array($select, ['competitor', 'competitors']) ? 'select' : '' ?>'><i title='<?= t('Competitors', 'Участники') ?>' class="fas fa-users"></i></a>
            <a href= '<?= PageIndex() ?>competitions/rankings/competitions' class='<?= $select == 'competitions' ? 'select' : '' ?>'><i title='<?= t('Competitors', 'Соревнования') ?>' class="fas fa-cubes"></i></a>
            <a href= '<?= PageIndex() ?>competitions/rankings/delegates' class='<?= $select == 'delegates' ? 'select' : '' ?>'><i title='<?= t('Delegates', 'Делегаты') ?>' class="fas fa-user-tie"></i></a>
            <?php if ($rename_aviable) { ?>
                <a href= '<?= PageIndex() ?>competitions/rankings/rename' class='<?= $select == 'rename' ? 'select' : '' ?>'><i title='<?= t('Rename WCA', 'Смена WCA имени') ?>' class="fas fa-user-edit"></i></a>
            <?php } ?>
            <hr>
            <?php
            $ee = true;
            $events_dict = unofficial\getEventsDict();
            foreach ($events_dict as $event_dict) {
                if ($event_code == $event_dict->code) {
                    $event_select = $event_dict;
                }
                if ($event_dict->extraevents and $ee) {
                    $ee = false;
                    ?><hr><?php
                }
                if (($event_dict->extraevents or!$event_dict->special) and isset($ratings[$event_dict->id]['best'])) {
                    ?>
                    <a href='<?= PageIndex() ?>competitions/rankings/<?= $event_dict->code ?>'><i title="<?= $event_dict->name ?>" class=" <?= $event_code == $event_dict->code ? 'select' : '' ?> <?= $event_dict->image ?>"></i></a>
                <?php }
                ?>
            <?php } ?>
        </td>
        <td style="padding-left:10px;vertical-align:top;">
            <?php
            switch ($select) {
                case 'event':
                    if ($event_select ?? false) {
                        include'rankings_event.php';
                    } else {
                        include'rankings_event_notfound.php';
                    }
                    break;
                case 'records':
                    include'rankings_records.php';
                    break;
                case 'competitors':
                    include'rankings_competitors.php';
                    break;
                case 'competitor':
                    include'rankings_competitor.php';
                    break;
                case 'competitions':
                    include'rankings_competitions.php';
                    break;
                case 'delegates':
                    include'rankings_delegates.php';
                    break;
                case 'rename':
                    if ($rename_aviable) {
                        include'rankings_rename.php';
                    } else {
                        include'rankings_rename.accessdenied.php';
                    }
                    break;
            }
            ?>
        <td>
    </tr>
</table>
<script>
<?php include 'rankings.js'; ?>
</script>