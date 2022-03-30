<?php
$events = unofficial\getEvents($comp->id);
$eventsRounds = unofficial\getEventsRounds($comp->id);
if ($comp_data->competition->events) {
    ?> 
    <h2>
        <i title='Events' class="fas fa-newspaper"></i>
        Events
    </h2>
    <table class="table_new">
        <thead>
            <tr>
                <td align="center">
                    <i title='Competitors' class="fas fa-users"></i>
                </td>
                <td align="center">
                    <i title='Results' class="fas fa-list-alt"></i>
                </td>
                <td></td>
                <td></td>
                <td>
                    Event
                </td>  
                <td>
                    Round
                </td>  
                <td>
                    Format
                </td>
                <td>
                    <i class="fas fa-cut"></i> Cutoff
                </td>
                <td>
                    <i class="fas fa-stop-circle"></i> Time limit
                </td>
                <?php if ($comp->ranked) { ?>
                    <td class="attempt">
                        <i class="fas fa-trophy"></i> Average
                    </td>
                    <td class="attempt">
                        <i class="fas fa-trophy"></i> Single
                    </td>
                <?php } ?>
                <td>
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
                        </td>
                        <td class="attempt">
                            <?= $cutoff ?>
                        </td>
                        <td class="attempt">
                            <?php $time_limit = $eventsRounds[$event->id][$round]->time_limit; ?>
                            <?php $cumulative = $eventsRounds[$event->id][$round]->cumulative; ?>
                            <?= $time_limit ? ($time_limit . ($cumulative ? ' cumulative' : '')) : '' ?>
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
                        <td align="left">
                            <?= $eventsRounds[$event->id][$round]->comment; ?>
                        </td>
                    </tr>    
                <?php } ?>   
            <?php } ?>   
        <tbody>
    </table>
<?php } ?>