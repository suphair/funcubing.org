<?php
$only_podium = isset($_GET['podium']);
?>
<h2>
    <div style='display: inline-block;width:50%;padding:0px;margin:0px;'>
        <i class="fas fa-users"></i>
        <?= t('Competitors', 'Участники') ?>
    </div>
    <div style='display: inline-block;width:49%;padding:0px;margin:0px;text-align:right'>
        <?php if (!$only_podium) { ?>
            <a  href='?podium'><?= t('Only podium', 'Только подиум') ?></a>
        <?php } else { ?>
            <a  href='?'><?= t('All', 'Все') ?></a>
        <?php } ?>
    </div>
</h2>
<div>
    <?php
    foreach ($comp_data->events as $event) {
        if ($event->special and !$event->extraevents) {
            ?>
            <i class="<?= $event->image ?>"></i>
            <?= $event->name ?>&nbsp;&nbsp;&nbsp;
            <?php
        }
    }

    foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
        $competitior_count_round[$event_round_id] = sizeof($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors ?? []);
        if (!$competitior_count_round[$event_round_id]) {
            unset($comp_data->event_rounds[$event_round_id]);
        }
    }
    ?>
</div>
<?php
$count_competitors = 0;
foreach ($comp_data->competitors as $competitor_id => $competitor) {
    if (sizeof($comp_data->competitors[$competitor_id]->events) > 0) {
        $count_competitors++;
    }
}
?>
<table class="table thead_stable">
    <thead>
        <tr>
            <th>
                <?= !$only_podium ? ("[$count_competitors]") : '' ?>
            </th>
            <?php
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                if ($only_podium and $event_round->round != $rounds) {
                    continue;
                }
                $event = $events_dict[$event_round->event_dict];
                ?>
                <th
                    title="<?= $event->name ?>, <?= $rounds_dict[$rounds == $event_round->round ? 0 : $event_round->round]->fullName; ?>"
                    class="center <?= ($event_round->round == 1) ? 'border_left' : '' ?>">
                    <a 
                        href="<?= PageIndex() . "competitions/$secret/event/{$event->code}/$event_round->round" ?> "
                        ><i  class="<?= $event->image ?>"></i></a>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $c = 0;
        foreach ($comp_data->competitors as $competitor_id => $competitor) {

            if ($only_podium) {
                $show = false;
                foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                    $event = unofficial\getEventByEventround($event_round_id);
                    $result = unofficial\getCompetitorsByEventround($event_round_id, $event)[$competitor_id] ?? FALSE;
                    if ($result->podium ?? false) {
                        $show = true;
                    }
                }
            } else {
                $show = true;
            }
            if (!$show) {
                continue;
            }
            if (sizeof($comp_data->competitors[$competitor_id]->events) > 0) {
                $c++
                ?>
                <tr>
                    <td style='white-space:nowrap '>
                        <?php
                        if ($comp->ranked and!$competitor->non_resident) {
                            $link = $competitor->FCID ? "rankings/competitor/$competitor->FCID" : false;
                        } else {
                            $link = "competitor/$competitor->id";
                        }
                        $name = t(transliterate($competitor->name), $competitor->name);
                        if ($link) {
                            ?>
                            <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $name ?></a>
                        <?php } else { ?>
                            <?= $name ?>
                            <?php
                        }
                        if ($comp->ranked and $competitor->non_resident) {
                            ?>
                            <i title = '<?= t('Non-resident', 'Нерезидент') ?>' class='fas fa-globe'></i>
                        <?php }
                        ?>
                    </td>
                    <?php
                    foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                        $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                        if ($only_podium and $event_round->round != $rounds) {
                            continue;
                        }
                        $event = unofficial\getEventByEventround($event_round_id);
                        $result = unofficial\getCompetitorsByEventround($event_round_id, $event)[$competitor_id] ?? FALSE;
                        ?>
                        <td class="
                            center 
                            <?= $result->podium ?? false ? 'td_podium' : '' ?> 
                            <?= $result->next_round ?? false ? 'td_next_round' : '' ?> 
                            <?= ($event_round->round == 1) ? 'border_left' : '' ?>
                            " >
                                <?php if ($result and (!$only_podium or $result->podium ?? false)) { ?>
                                    <?php if ($result->place ?? FALSE) { ?>
                                        <?= $result->place ?>
                                    <?php } else { ?>
                                    <i style='color:var(--light_gray)' class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
    <?php if (!$only_podium) { ?>
        <tfoot>
            <tr class="<?= ($c % 2 == 1) ? 'gray' : '' ?>">
                <td>
                    <?= t('Total', 'Всего') ?>
                </td>
                <?php
                foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                    $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                    if ($only_podium and $event_round->round != $rounds) {
                        continue;
                    }
                    ?>
                    <td class="center <?= ($event_round->round == 1) ? 'border_left' : '' ?>">
                        <?= $competitior_count_round[$event_round_id] ?>
                        <?php
                        $results_count = 0;
                        foreach ($comp_data->competitors as $competitor_id => $competitor) {
                            if ($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors[$competitor_id]->place ?? FALSE) {
                                $results_count++;
                            }
                        }
                        ?>
                    </td>
                <?php } ?>
            </tr>
        </tfoot>
    <?php } ?>
</table>

<?php if (!sizeof($comp_data->event_rounds)) { ?>
    <div><?= t('No competitors', 'Нет участников') ?></div>
    <?php
}
?>