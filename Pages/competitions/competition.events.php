<?php
$events = unofficial\getEvents($comp->id);
$eventsRounds = unofficial\getEventsRounds($comp->id);
if ($comp_data->competition->events) {
    ?> 
    <h2>
        <i title='Events' class="fas fa-newspaper"></i>
        <?= t('Events', 'Дисциплины') ?>
    </h2>
    <table class="table_new">
        <thead>
            <tr>
                <td align="center">
                    <i title='<?= t('Competitors', 'Участники') ?>' class="fas fa-users"></i>
                </td>
                <td align="center">
                    <i title='<?= t('Results', 'Результаты') ?>' class="fas fa-list-alt"></i>
                </td>
                <td></td>
                <td></td>
                <td>
                    <?= t('Events', 'Дисцплины') ?>
                </td>  
                <td>
                    <?= t('Round', 'Раунд') ?>
                </td>  
                <td>
                    <?= t('Format', 'Формат') ?>
                </td>
                <td>
                    <i class="fas fa-cut"></i> <?= t('Cutoff', 'Катофф') ?>
                </td>
                <td>
                    <i class="fas fa-stop-circle"></i> <?= t('Time limit', 'Лимит по времени') ?>
                </td>
                <td>
                    <i class="fas fa-caret-square-right"></i> <?= t('Advance to next round', 'Проходят дальше') ?>
                </td>
                <?php if ($comp->ranked) { ?>
                    <td class="attempt">
                        <i class="fas fa-trophy"></i> <?= t('Average', 'Среднее') ?>
                    </td>
                    <td class="attempt">
                        <i class="fas fa-trophy"></i> <?= t('Single', 'Лучшая') ?>
                    </td>
                <?php } ?>
                <td hidden data-event-comment>
                    <i class="fas fa-comment-dots"></i> Comment
                </td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event) { ?>
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
                        <td align="center">
                            <?= $competitors_count > 0 ? $competitors_count : '.' ?>
                        </td>
                        <td align="center">
                            <?= $results_count > 0 ? $results_count : '.' ?>
                        </td>
                        <td>
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
                                <i style='color:var(--gray)' class="fas fa-hourglass-start"></i>
                            <?php } ?>
                            <?php if ($competitors_count and!$results_count) { ?>
                                <i style='color:var(--gray)' class="fas fa-hourglass-half"></i>
                            <?php } ?>
                        </td>
                        <td>
                            <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/$secret/event/$event->code/$round" ?>">
                                <?= $event->name ?>
                            </a>
                        </td>
                        <td>
                            <?= $rounds_dict[$event->rounds == $round ? 0 : $round]->smallName; ?>
                        </td>
                        <td> <?php $format_dict = $formats_dict[$event->format_dict] ?>
                            <?= $cutoff ? "$format_dict->cutoff_name / " : '' ?>
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
                            <?= $cutoff ?>
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
                                foreach ($records[$event->event_dict] ?? [] as $record) {
                                    if ($record->type == 'average' and $record->round == $round) {
                                        ?>
                                        <span class="record">
                                            <?= $record->result ?>
                                        </span>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <td class="attempt">
                                <?php
                                foreach ($records[$event->event_dict] ?? [] as $record) {
                                    if ($record->type == 'best' and $record->round == $round) {
                                        ?>
                                        <span class="record">
                                            <?= $record->result ?>
                                        </span>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td align="left" hidden data-event-comment>
                            <?= $event_round->comment; ?>
                        </td>
                    </tr>    
                <?php } ?>   
            <?php } ?>   
        <tbody>
    </table>
<?php } ?>