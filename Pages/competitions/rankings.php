<?php
$event_code = db::escape(request(2));
$select = false;
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
if ($event_code == 'judges') {
    $select = 'judges';
}
if (!$select) {
    $select = 'event';
}

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
            $result_int = str_replace(['.', ':'], '', $row->result) + 0;
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

<div class="shadow" >
    <h1>
        <p>
            <?= $ranked_icon ?>
            FunCubing Rankings
        </p>
        <a href= '<?= PageIndex() ?>competitions/rankings' class='<?= $select == 'records' ? 'select' : '' ?>'><i title='Records' class="fas fa-trophy"></i></a>
        <a href= '<?= PageIndex() ?>competitions/rankings/competitors' class='<?= in_array($select, ['competitor', 'competitors']) ? 'select' : '' ?>'><i title='Competitors' class="fas fa-users"></i></a>
        <a href= '<?= PageIndex() ?>competitions/rankings/competitions' class='<?= $select == 'competitions' ? 'select' : '' ?>'><i title='Competitors' class="fas fa-cubes"></i></a>
        <a href= '<?= PageIndex() ?>competitions/rankings/judges' class='<?= $select == 'judges' ? 'select' : '' ?>'><i title='Judges' class="fas fa-user-tie"></i></a>
        <?php
        $events_dict = unofficial\getEventsDict();
        foreach ($events_dict as $event_dict) {
            if ($event_code == $event_dict->code) {
                $event_select = $event_dict;
            }
            if (!$event_dict->special and isset($ratings[$event_dict->id]['best'])) {
                ?>
                <a href='<?= PageIndex() ?>competitions/rankings/<?= $event_dict->code ?>'><i class=" <?= $event_code == $event_dict->code ? 'select' : '' ?> <?= $event_dict->image ?>"></i></a>
            <?php }
            ?>
        <?php } ?>
    </h1>  
    <div class="shadow2" >        
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
            case 'judges':
                include'rankings_judges.php';
                break;
        }
        ?>
    </div>
</div>
