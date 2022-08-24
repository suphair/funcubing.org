<?php
$events = unofficial\getEvents($comp->id);
$eventsRounds = unofficial\getEventsRounds($comp->id);
$bestAttempts = unofficial\getBestAttempts($comp->id);
if ($comp_data->competition->events) {
    ?> 
    <h2>
        <i title='Events' class="fas fa-newspaper"></i>
        <?= t('Events', 'Дисциплины') ?>
    </h2>
    <table class="table thead_stable">
        <thead>
            <tr>
                <th>
                    <?= t('Event', 'Дисциплина') ?>
                </th>  
                <th>
                    <?= t('Round', 'Раунд') ?>
                </th>  
                <th>
                    <?= t('Res / Comp', 'Рез / Уч') ?>
                </th>
                <th>
                    <?= t('Format', 'Формат') ?>
                </th>
                <th>
                    <?= t('Cutoff', 'Катофф') ?>
                </th>
                <th>
                    <?= t('Limit', 'Лимит') ?>
                </th>
                <th>
                    <?= t('Advance to next round', 'Проходят дальше') ?>
                </th>
                <?php if ($comp->ranked) { ?>
                    <th class="attempt">
                        <?= t('Best average', 'Лучшее среднее') ?>
                    </th>
                    <th class="attempt">
                        <?= t('Best single', 'Лучшая сборка') ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($events as $event) {
                $event_code = $events_dict[$event->event_dict]->code;
                ?>
                <?php
                foreach (range(1, $event->rounds) as $round) {
                    $event_round = $eventsRounds[$event->id][$round];
                    $cutoff = $event_round->cutoff;
                    $results_count = 0;
                    foreach ($comp_data->competitors as $competitor_id => $competitor) {
                        if ($comp_data->rounds[$event->event_dict][$event_round->round]->competitors[$competitor_id]->place ?? FALSE) {
                            $results_count++;
                        }
                    }
                    $competitors_count = sizeof($comp_data->rounds[$event->event_dict][$event_round->round]->competitors ?? []);
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                            <a href="<?= PageIndex() . "competitions/$secret/event/$event->code/$round" ?>">
                                <?= $event->name ?>
                            </a>
                        </td>
                        <td>
                            <?= $rounds_dict[$event->rounds == $round ? 0 : $round]->smallName; ?>
                        </td>
                        <td><nobr>
                            <?php if ($results_count and $competitors_count == $results_count and $event->rounds == $round) { ?>
                                <i style='color:var(--green)' class="fas fa-flag-checkered"></i>
                            <?php } ?>
                            <?php if ($results_count and $competitors_count == $results_count and $event->rounds != $round) { ?>
                                <i style='color:var(--green)' class="fas fa-arrow-alt-circle-down"></i>
                            <?php } ?>
                            <?php if ($results_count and $competitors_count != $results_count) { ?>
                                <i   class="fas fa-running"></i>
                            <?php } ?>
                            <?php if (!$competitors_count and!$results_count) { ?>
                                <i class="fas fa-hourglass-start"></i>
                            <?php } ?>
                            <?php if ($competitors_count and!$results_count) { ?>
                                <i class="fas fa-hourglass-half"></i>
                            <?php } ?>
                            <?= $results_count > 0 ? $results_count : '.' ?> / <?= $competitors_count > 0 ? $competitors_count : '.' ?>
                        </nobr></td>
                        <td> <?php $format_dict = $formats_dict[$event->format_dict] ?>
                            <nobr><?= $cutoff ? "$format_dict->cutoff_name /<br> " : '' ?></nobr>
                            <?= $format_dict->name ?>
                            <?php
                            if ($events_dict[$event->event_dict]->special) {
                                $result_dict = $results_dict[$event->result_dict];
                                if ($result_dict->code == 'amount_asc') {
                                    ?>
                                    <br>
                                    <i class="fas fa-sort-numeric-down"></i>
                                    <?= $result_dict->name ?>
                                    <?php
                                }
                                if ($result_dict->code == 'amount_desc') {
                                    ?>
                                    <br>
                                    <i class="fas fa-sort-numeric-down-alt"></i>
                                    <?= $result_dict->name ?>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                        <td class="attempt">
                            <?= $cutoff ? $cutoff : '-' ?>
                        </td>
                        <td class="attempt">
                            <?php $time_limit = $eventsRounds[$event->id][$round]->time_limit; ?>
                            <?php $cumulative = $eventsRounds[$event->id][$round]->cumulative; ?>
                            <?= $time_limit ? ($time_limit . ($cumulative ? t(' in total', ' суммарно') : '')) : '' ?>
                        </td>
                        <td class="attempt">
                            <?php if ($event->rounds > $round) { ?>
                                <?php if ($event_round->next_round_procent) { ?>
                                    <?= t('Top', 'Лучшие'); ?> <?= $event_round->next_round_value ?>%
                                <?php } else { ?>
                                    <?= t('Top', 'Лучшие'); ?> <?= $event_round->next_round_value ?>
                                <?php } ?>   
                            <?php } ?>   
                        </td>
                        <?php if ($comp->ranked) { ?>
                            <td class="attempt">
                                <?php
                                $record_exists = false;
                                foreach ($records[$event->event_dict] ?? [] as $record) {
                                    if ($record->type == 'average' and $record->round == $round) {
                                        $record_exists = true;
                                        ?>
                                        <span class="td_record">
                                            <?= $record->result ?> R
                                        </span>
                                        <?php
                                    }
                                }
                                if (!$record_exists and $bestAttempts[$event_code][$event_round->round]['average'] ?? false) {
                                    ?>
                                    <?= $bestAttempts[$event_code][$event_round->round]['average'] ?>
                                <?php } ?>
                            </td>
                            <td class="attempt">
                                <?php
                                $record_exists = false;
                                foreach ($records[$event->event_dict] ?? [] as $record) {
                                    if ($record->type == 'best' and $record->round == $round) {
                                        $record_exists = true;
                                        ?>
                                        <span class="td_record">
                                            <?= $record->result ?> R
                                        </span>
                                        <?php
                                    }
                                }
                                if (!$record_exists and $bestAttempts[$event_code][$event_round->round]['best'] ?? false) {
                                    ?>
                                    <?= $bestAttempts[$event_code][$event_round->round]['best'] ?>
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>    
                <?php } ?>   
            <?php } ?>   
        <tbody>
    </table>
<?php } ?>