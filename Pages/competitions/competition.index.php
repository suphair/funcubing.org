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
        if ($event->attempts == 3 and $event->format == 'best') {
            $formats[] = 'mean';
        }
    }
}
$records = unofficial\getRankedRecordbyCompetition($comp->id);
?>
<table width='100%'>
    <tr>
        <td class="navigator_event">

            <a 
                class="<?= $section == 'info' ? 'select' : '' ?>"
                href="<?= PageIndex() . "competitions/$secret" ?>"
                ><i title="<?= t('General info', 'Информация'); ?>" class="fas fa-info-circle"></i></a>
            <a 
                class="<?= $section == 'events' ? 'select' : '' ?>"
                href="<?= PageIndex() . "competitions/$secret/events" ?>"
                ><i title='<?= t('Events', 'Дисциплины'); ?>' class="fas fa-newspaper"></i></a>
            <a 
                class="<?= $section == 'competitors' ? 'select' : '' ?>"
                href="<?= PageIndex() . "competitions/$secret/competitors" ?>"
                ><i title='<?= t('Competitors', 'Участники'); ?>' class="fas fa-users"></i></a>
                <?php if (strtotime($competition->start_date) <= strtotime(date('Y-m-d')) and $competition->points) {
                    ?>
                <a 
                    class="<?= $section == 'points' ? 'select' : '' ?>"
                    href="<?= PageIndex() . "competitions/$secret/points" ?>"
                    ><i title='<?= t('Overall standings', 'Общий зачёт'); ?>' class="<?= $points_dict[$competition->points]->icon ?>"></i></a>
                <?php } ?>
                <?php if ($competition->is_ranked and!$competition->is_approved) { ?>
                <a 
                    class="<?= $section == 'psychsheet' ? 'select' : '' ?>"
                    href="<?= PageIndex() . "competitions/$secret/psychsheet" ?>">
                    <i title="Psych Sheet" class="fas fa-spa"></i> 
                </a>
            <?php } ?>
            <?php if ($competition->is_approved and $json_scrambles) { ?>
                <a 
                    class="<?= $section == 'scrambles' ? 'select' : '' ?>"
                    href="<?= PageIndex() . "competitions/$secret/scrambles" ?>">
                    <i title="Scrambles" class="fas fa-random"></i> 
                </a>
            <?php } ?>
            <?php if (sizeof($records)) { ?>
                <a 
                    class="<?= $section == 'records' ? 'select' : '' ?>"
                    href="<?= PageIndex() . "competitions/$secret/records" ?>"
                    ><i title='<?= t('Records', 'Рекорды'); ?>' class="fas fa-trophy"></i></a>    
                <?php } ?>
                <?php if ($mobile) { ?>
                <a href="<?= PageIndex() . "competitions/$secret/?action=mobile" ?>">
                    <i title="<?= t('Mobile', 'Смартфон') ?>" class="fas fa-mobile-alt"></i>
                </a>
            <?php } ?>
            <hr>
            <?php if ($grand->edit ?? false and $competition->wrong_attempts) { ?>
                <span style="background-color:orange">
                    <a 
                        class="<?= $section == 'wrongresults' ? 'select' : '' ?>"
                        href="<?= PageIndex() . "competitions/$secret/wrongresults" ?>">
                        <i title="Wrong Results" class="fas fa-bug"></i>
                    </a>
                </span>
                <?php
            }
            $ee = true;
            foreach ($comp_data->events as $event_a) {

                if ($events_dict[$event_a->event_dict]->extraevents and $ee) {
                    $ee = false;
                    ?><hr><?php }
                ?>
                <a class="<?= ($event_dict ?? false) == $event_a->event_dict ? 'select' : '' ?>"
                   title="<?= $comp_data->events[$event_a->event_dict]->name ?>"
                   href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_a->event_dict]->code}/1" ?> "
                   ><i class="<?= $events_dict[$event_a->event_dict]->image ?>"></i></a>
               <?php } ?>
        </td>
        <td style="padding-left:10px;vertical-align:top;">
            <?php
            if ($section == 'info') {
                include 'competition.info.php';
            } elseif ($section == 'scrambles') {
                include 'competition.scrambles.php';
            } elseif ($section == 'events') {
                include 'competition.events.php';
            } elseif ($section == 'setting') {
                include 'competition.setting.php';
            } elseif ($section == 'setting_events') {
                include 'competition.setting.events.php';
            } elseif ($section == 'setting_sheets') {
                include 'competition.setting.sheets.php';
            } elseif ($section == 'registrations') {
                include 'competition.registrations.php';
            } elseif ($section == 'records') {
                include 'competition.records.php';
            } elseif ($section == 'psychsheet' and $competition->is_ranked) {
                include 'competition.event.psychsheet.php';
            } elseif ($section == 'wrongresults') {
                include 'competition.wrongresults.php';
            } else {
                if (($comp->my or $comp->organizer) and $section == 'result') {
                    include 'competition.event.index.php';
                } elseif ($event_round_this and ($grand->edit ?? false) and $section == 'event_competitors') {
                    include 'competition.event.index.php';
                } elseif ($section == 'event') {
                    include 'competition.event.index.php';
                } elseif (!$event_round_this and $section == 'competitors') {
                    include 'competition.competitors.php';
                } elseif (!$event_round_this and $section == 'points') {
                    include 'competition.points.php';
                }
            }
            ?>
        </td>
    </tr>
</table>