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
                    Comment
                </td>
                <td>
                    Cutoff
                </td>
                <td>
                    Time limit
                </td>
                <?php if ($comp->my or $comp->organizer) { ?>
                    <td>
                        Print
                    </td>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event) { ?>
                <?php
                foreach (range(1, $event->rounds) as $round) {
                    $cutoff = $eventsRounds[$event->id][$round]->cutoff;
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                        </td>
                        <td>
                            <?= $event->name ?>
                        </td>
                        <td>
                            <?= $rounds_dict[$event->rounds == $round ? 0 : $round]->smallName; ?>
                        </td>
                        <td> <?php $format_dict = $formats_dict[$event->format_dict] ?>
                            <?= $cutoff ? "$format_dict->cutoff_name / " : '' ?>
                            <?= $format_dict->name ?>
                        </td>
                        <td align="left">
                            <?php $comment = $eventsRounds[$event->id][$round]->comment; ?>
                            <?= $comment ? ('<i class="fas fa-comment-dots"></i> ' . $comment) : '' ?>
                        </td>
                        <td align="left">
                            <?= $cutoff ? ('<i class="fas fa-cut"></i> ' . $cutoff) : '' ?>
                        </td>
                        <td align="left">
                            <?php $time_limit = $eventsRounds[$event->id][$round]->time_limit; ?>
                            <?php $cumulative = $eventsRounds[$event->id][$round]->cumulative; ?>
                            <?= ($time_limit and!$cumulative) ? ('<i class="fas fa-stop-circle"></i> ' . $time_limit) : '' ?>
                            <?= ($time_limit and $cumulative) ? ('<i class="fas fa-plus-circle"></i> ' . $time_limit . ' cumulative') : '' ?>
                        </td>

                        <?php
                        if ($comp->my or $comp->organizer) {
                            $base_url = PageIndex() . "competitions/{$comp->secret}/event/{$events_dict[$event->event_dict]->code}/$round";
                            ?>
                            <td>
                                <?php if (sizeof($comp_data->competitors)) { ?>
                                    <a target="_blank" href="<?= $base_url ?>?action=cards">Cards</a> ▪
                                <?php } ?>     
                                <a target="_blank" href="<?= $base_url ?>?action=result">Results</a> ▪
                                <a target="_blank" href="<?= $base_url ?>?action=cards&blank">Blank cards</a>
                            </td>
                        <?php } ?>

                    </tr>    
                <?php } ?>   
            <?php } ?>   
        <tbody>
    </table>
<?php } ?>