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
        if ($event->special) {
            ?>
            <i class="<?= $event->image ?>"></i>
            <?= $event->name ?>&nbsp;&nbsp;&nbsp;
            <?php
        }
    }
    ?>
</div>
<table class="table thead_stable">
    <thead>
        <tr>
            <th>
                <?= !$only_podium ? sizeof($comp_data->competitors) : '' ?>
            </th>
            <?php
            $round_next_exists = false;
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                if ($event_round->round > 1) {
                    $round_next_exists = true;
                }
            }
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                if ($only_podium and $event_round->round != $rounds) {
                    continue;
                }
                ?>
                <th class="center <?= ($event_round->round == $rounds) ? 'border_right' : '' ?>">
                    <a  href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
                        <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                    </a>
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
                        <?php if ($comp->ranked and $competitor->non_resident) { ?>
                            <i class='fas fa-globe'></i>
                            <?php
                        }
                        if ($comp->ranked and!$competitor->non_resident) {
                            $link = $competitor->FCID ? "rankings/competitor/$competitor->FCID" : false;
                        } else {
                            $link = "competitor/$competitor->id";
                        }
                        if ($link) {
                            ?>
                            <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $competitor->name ?></a>
                        <?php } else { ?>
                            <?= $competitor->name ?>
                        <?php } ?>
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
                            <?= ($event_round->round == $rounds) ? 'border_right' : '' ?>
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
                    <td class="center <?= ($event_round->round == $rounds) ? 'border_right' : '' ?>">
                        <?php $competitors_count = sizeof($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors ?? []) ?>
                        <?= $competitors_count ?>
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