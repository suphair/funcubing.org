<?php
$code = request(3);
$round = request(4);

if ($event_round_this) {
    $event_dict = $comp_data->event_dict->by_code[$code]->id ?? FALSE;
    if (!$round) {
        $round = 1;
    }
    $event_round_this = $comp_data->rounds[$event_dict][$round]->round->id ?? null;
    if ($event_round_this == null) {
        include 'competition.event.wrong.php';
        $section = false;
    } else {
        $event = unofficial\getEventByEventround($event_round_this);
        $competitors = unofficial\getCompetitorsByEventround($event_round_this, $event);
        $next_round_competitors = 0;
        if (!$event->final) {
            foreach ($competitors as $competitor) {
                if ($competitor->next_round) {
                    $next_round_competitors++;
                }
            }
        }
        $formats = array_unique([$event->format, 'best']);
    }
}
$records = unofficial\getRankedRecordbyCompetition($comp->id);
?>
<h1 style="background-color: rgba(0,0,0,.1);padding:5px;margin-bottom: 10px; margin-top: 10px; border-radius: 10px;">
    <a 
        class="<?= $section == 'info' ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret" ?>"
        ><i title="<?= t('General info', 'Информация'); ?>" class="fas fa-info-circle"></i></a>
    <a 
        class="<?= $section == 'events' ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret/events" ?>"
        ><i title='<?= t('Events', 'Дисцпилины'); ?>' class="fas fa-newspaper"></i></a>
    <a 
        class="<?= $section == 'competitors' ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret/competitors" ?>"
        ><i title='<?= t('Competitors', 'Участники'); ?>' class="fas fa-users"></i></a>
        <?php if (sizeof($records)) { ?>
        <a 
            class="<?= $section == 'records' ? 'select' : '' ?>"
            href="<?= PageIndex() . "competitions/$secret/records" ?>"
            ><i title='<?= t('Records', 'Рекорды'); ?>' class="fas fa-trophy"></i></a>    
        <?php } ?>

    <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
        <a class="<?= $event_round_this == $event_round_id ? 'select' : '' ?>"
           title="<?= $comp_data->events[$event_round->event_dict]->name ?>, <?= $rounds_dict[$event_round->round == $event_round->rounds ? 0 : $event_round->round]->fullName ?>"
           href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> "
           ><i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i></a>
       <?php } ?>
</h1>   
<?php
if ($section == 'info') {
    include 'competition.info.php';
} elseif ($section == 'events') {
    include 'competition.events.php';
} elseif ($section == 'records') {
    include 'competition.records.php';
} else {
    if (($comp->my or $comp->organizer) and $section == 'result') {
        include 'competition.event.index.php';
    } elseif ($event_round_this and ($comp->my or $comp->organizer) and $section == 'event_competitors') {
        include 'competition.event.index.php';
    } elseif ($section == 'event') {
        include 'competition.event.index.php';
    } elseif (!$event_round_this and $section == 'competitors') {
        include 'competition.competitors.php';
    }
}
?>