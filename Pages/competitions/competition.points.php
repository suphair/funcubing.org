
<h2>
    <div style='display: inline-block;width:50%;padding:0px;margin:0px;'>
        <i class="fas fa-star"></i>
        <?= t('Overall standings', 'Общий зачёт') ?>
    </div>
</h2>
<div>
    <?=
    t('Only the final rounds are considered. The difference between the number of participants in the final plus one and the participant\'s place is summed up.',
            'Учитываются только финальные раунды. Суммируется разница между количеством участников в финале плюс один и местом участника.')
    ?>
</div>
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
            <th>#</th>
            <th></th>
            <?php
            $round_next_exists = false;
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                if ($event_round->round > 1) {
                    $round_next_exists = true;
                }
            }
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                if ($event_round->round != $rounds) {
                    continue;
                }
                ?>
                <th class="center">
                    <a  href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
                        <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                    </a>
                </th>
            <?php } ?>
            <th class="center" style="color:green">
                <i class="fas fa-star"></i>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td>
                <?= t('Competitors', 'Участники') ?>
            </td>
            <?php
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                if ($event_round->round != $rounds) {
                    continue;
                }
                ?>
                <td class="center">
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
            <td></td>
        </tr>
        <?php
        $c = 1;
        $rows_out = [];
        foreach ($comp_data->competitors as $competitor_id => $competitor) {
            $point = 0;
            $final_count = 0;
            $c++;
            $show = false;
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $event = unofficial\getEventByEventround($event_round_id);
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                $competitors_round = unofficial\getCompetitorsByEventround($event_round_id, $event);
                $result = $competitors_round[$competitor_id] ?? FALSE;
                if ($result->place ?? false and $event_round->round == $rounds) {
                    $show = true;
                }
            }
            if (!$show) {
                continue;
            }
            ob_start();
            if (sizeof($comp_data->competitors[$competitor_id]->events) > 0) {
                ?>
                <tr>
                    <td>$POS</td>
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
                        if ($event_round->round != $rounds) {
                            continue;
                        }
                        $event = unofficial\getEventByEventround($event_round_id);
                        $competitors_round = unofficial\getCompetitorsByEventround($event_round_id, $event);
                        $result = $competitors_round[$competitor_id] ?? FALSE;
                        $point_event = (($result->place ?? false) and ($event_round->round == $rounds)) ? (sizeof($competitors_round) - $result->place + 1) : 0;
                        $point += $point_event;
                        $final_count += ($result->place ?? 0) > 0;
                        ?>
                        <td class="center" >
                            <?= $result->place ?? false ?>
                            <sup style="color:green">
                                <?= $point_event ? $point_event : '' ?>
                            </sup>
                        </td>
                    <?php } ?>
                    <td class="attempt" style="color:green">
                        <?= $point ? $point : ''; ?>
                    </td>
                </tr>
                <?php
            }
            $rows_out[$point * 1000000 + $competitor_id * 100 + $final_count] = ob_get_contents();
            ob_end_clean();
        }
        ksort($rows_out, false);
        $rows_out = array_reverse($rows_out);
        $pos = 0;
        foreach ($rows_out as $row_out) {
            $pos++;
            ?>
            <?= str_replace('$POS', $pos, $row_out) ?>
            <?php
        }
        ?>
    </tbody>
</table>