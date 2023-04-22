<?php
$event_dict = null;
if (sizeof($comp_data->events)) {
    $event_select = request(3);
    if (!$event_select) {
        $event_select = $events_dict[array_values($comp_data->events)[0]->event_dict]->code;
    }

    if (in_array($event_select, ['333bf', '333fm', '444bf', '555bf', '666', '777'])) {
        $format_best = true;
    } else {
        $format_best = false;
    }
    ?>
    <div class="menu">
        <?php
        foreach ($comp_data->events as $event_a) {
            if (!$event_a->special) {
                $event_itt = $events_dict[$event_a->event_dict];
                if ($event_itt->code == $event_select) {
                    $event_dict = $comp_data->events[$event_a->event_dict];
                }
                if ($event_itt->code !== '333mbf') {
                    ?>
                    <a  class="<?= $event_itt->code == $event_select ? 'select' : '' ?>"
                        href="<?= PageIndex() . "competitions/$secret/psychsheet/$event_itt->code" ?> ">
                        <i title="<?= $event_itt->name ?>"
                           class="<?= $event_itt->image ?>"></i></a>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
    </div>
    <?php if ($event_dict) { ?>
        <h2>
            <i class = "fas fa-spa"></i>
            Psych Sheet
            <i class = "<?= $event_dict->image ?>"></i>
            <?= $event_dict->name ?>
        </h2>


        <table class="table thead_stable">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?= t('Competitor', 'Имя') ?></th>
                    <?php if ($format_best) { ?>
                        <th><?= t('Best', 'Лучшая') ?></th>
                        <th><?= t('Average', 'Среднее') ?></th>
                    <?php } else { ?>
                        <th><?= t('Average', 'Среднее') ?></th>
                        <th><?= t('Best', 'Лучшая') ?></th>
                    <?php } ?>
                    <th>FC ID</th>
                    <th>WCA ID <i class="fas fa-external-link-alt"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $best_wca = db::rows("SELECT wca_id, single, average FROM wca_best WHERE event='$event_select'");
                $best_fc = db::rows("SELECT fc_id, wca_id, single, average FROM fc_best WHERE event='$event_select'");

                $best_wca_id = [];
                foreach ($best_wca as $b) {
                    $best_wca_id[$b->wca_id] = (object) ['single' => $b->single, 'average' => $b->average];
                }
                $best_fc_id = [];
                foreach ($best_fc as $b) {
                    $best_fc_id[$b->fc_id] = (object) ['single' => $b->single, 'average' => $b->average];
                }

                $registrations = api\get_registrations(request(1));
                $competitors = [];
                foreach ($registrations as $registration) {
                    if (in_array($event_select, $registration->event_ids)) {
                        $from_wca_average = false;
                        $from_wca_single = false;
                        $average = false;
                        $single = false;
                        if ($registration->fc_id ?? false) {
                            $average = $best_fc_id[$registration->fc_id]->average ?? false;
                            $single = $best_fc_id[$registration->fc_id]->single ?? false;

                            if ($best_wca_id[$registration->wca_id] ?? false) {

                                $wca_average = $best_wca_id[$registration->wca_id]->average ?? false;
                                $wca_single = $best_wca_id[$registration->wca_id]->single ?? false;
                                if ($wca_average and ($wca_average < $average or!$average)) {
                                    $average = $wca_average;
                                    $from_wca_average = true;
                                }
                                if ($wca_single and ($wca_single < $single or!$single)) {
                                    $single = $wca_single;
                                    $from_wca_single = true;
                                }
                            }
                        }
                        $competitors[] = (object) [
                                    'fc_id' => $registration->fc_id ?? '-',
                                    'wca_id' => $registration->wca_id ?? false,
                                    'name' => $registration->name,
                                    'average' => $average,
                                    'single' => $single,
                                    'from_wca_single' => $from_wca_single,
                                    'from_wca_average' => $from_wca_average
                        ];
                    }
                }

                function sort_best_average($a, $b) {
                    if ($a->average and!$b->average) {
                        return false;
                    }
                    if (!$a->average and $b->average) {
                        return true;
                    }
                    if ($a->average == $b->average) {
                        if ($a->single and!$b->single) {
                            return false;
                        }
                        if (!$a->single and $b->single) {
                            return true;
                        }
                        return $a->single > $b->single;
                    }
                    return $a->average > $b->average;
                }

                function sort_best_single($a, $b) {
                    if ($a->single and!$b->single) {
                        return false;
                    }
                    if (!$a->single and $b->single) {
                        return true;
                    }
                    if ($a->single == $b->single) {
                        if ($a->average and!$b->average) {
                            return false;
                        }
                        if (!$a->average and $b->average) {
                            return true;
                        }
                        return $a->average > $b->average;
                    }
                    return $a->single > $b->single;
                }

                usort($competitors,
                        $format_best ? 'sort_best_single' : 'sort_best_average');

                $p = 0;
                foreach ($competitors as $competitor) {
                    $p++;
                    $competitor->pos=$p;
                }
                foreach ($competitors as $competitor) {
                    ?>
                    <tr>
                        <td>
                            <?= $competitor->pos ?> 
                        </td>
                        <td style='white-space:nowrap '>
                            <?php if ($competitor->fc_id) { ?>
                                <a href="<?= PageIndex() . "competitions/rankings/competitor/$competitor->fc_id" ?>/">
                                    <?= str_replace($competitor->fc_id, '', $competitor->name); ?>
                                </a>
                            <?php } else { ?>
                                <?= $competitor->name ?>
                            <?php } ?>
                        </td>
                        <?php if (!$format_best) { ?>
                            <td>
                                <?= santiceconds_to_string($competitor->average) ?>
                                <sub style="color:gray">
                                    <?= $competitor->from_wca_average ? ' WCA' : '' ?>
                                </sub>
                            </td>
                        <?php } ?>
                        <td>
                            <?php
                            $santiceconds = santiceconds_to_string($competitor->single);
                            if ($competitor->single and $event_select === '333fm') {
                                $santiceconds = $competitor->single;
                            }
                            ?>
                            <?= $santiceconds ?>
                            <sub style="color:gray">
                                <?= $competitor->from_wca_single ? ' WCA' : '' ?>
                            </sub>
                        </td>
                        <?php if ($format_best) { ?>
                            <td>
                                <?= santiceconds_to_string($competitor->average) ?>
                                <sub style="color:gray">
                                    <?= $competitor->from_wca_average ? ' WCA' : '' ?>
                                </sub>
                            </td>
                        <?php } ?>
                        <td>
                            <?= $competitor->fc_id ?>
                        </td>
                        <td>
                            <?php if ($competitor->wca_id === null) { ?>
                                ?
                            <?php } elseif ($competitor->wca_id === false) { ?>
                                -
                            <?php } else { ?>
                                <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $competitor->wca_id ?>">
                                    <?= $competitor->wca_id ?>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

    <?php } else { ?>
        <p><?= t('Wrong event', 'Нет такой дисциплины') ?></p>
        <?php
    }
} else {
    ?>
    <p><?= t('No events', 'Нет дисциплин') ?></p>
    <?php
}

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
        return "-";
    }
}
