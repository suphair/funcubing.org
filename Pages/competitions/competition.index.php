<h1>
    <a 
        class="<?= (!$event_round_this and!$events_list) ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret" ?>">
        <i title='Competitors' class="fas fa-users"></i>
    </a>
    <a 
        class="<?= ($events_list) ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret/events" ?>">
        <i title='Events' class="fas fa-newspaper"></i>
    </a>    
    <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
        <a class="<?= $event_round_this == $event_round_id ? 'select' : '' ?>"
           title="<?= $comp_data->events[$event_round->event_dict]->name ?> / round <?= $event_round->round ?>"
           href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
            <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
        </a>
    <?php } ?>
</h1>   

<div class="shadow2" >
    <?php if ($events_list) { ?>

        <?php
        $events = unofficial\getEvents($comp->id);
        $eventsRounds = unofficial\getEventsRounds($comp->id);
        if ($comp_data->competition->events) {
            ?> 
            <h2>Events</h2>
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
                        <?php foreach (range(1, $event->rounds) as $round) { ?>
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
                                <td>
                                    <?= $formats_dict[$event->format_dict]->name ?>
                                </td>
                                <td align="left">
                                    <?php $comment = $eventsRounds[$event->id][$round]->comment; ?>
                                    <?= $comment ? ('<i class="fas fa-comment-dots"></i> ' . $comment) : '' ?>
                                </td>
                                <td align="left">
                                    <?php $cutoff = $eventsRounds[$event->id][$round]->cutoff; ?>
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
    <?php } else { ?>
        <?php if ($comp->my or $comp->organizer) { ?>
            <p>
                <?php if (!$event_round_this) { ?>
                    <a href="?action=certificates">Download certificates</a>  ▪
                <?php } ?>
                <?php if (sizeof($comp_data->competitors)) { ?>
                    <a target="_blank" href="?action=cards">Print competitors cards</a> ▪
                <?php } ?>     
                <a target="_blank" href="?action=result">Print the results</a> ▪
                <a target="_blank" href="?action=cards&blank">Print blank competitors cards</a> ▪
                <a target="_blank" href="?action=export">Export results</a>
                <?php if ($event_round_this) { ?>
                    ▪ <a target="_blank" href="?action=export&format=txt">TXT results</a>
                <?php } ?>
            </p>
        <?php } ?>
        <?php if ($event_round_this !== FALSE) { ?>
            <?php
            if ($event_round_this == null) {
                include 'competition.event.wrong.php';
            } elseif (($comp->my or $comp->organizer) and $section == 'result') {
                include 'competition.event.result.php';
            } else {
                include 'competition.event.php';
            }
            ?>
        <?php } else { ?>
            <table class="table_new">
                <thead>
                    <tr>
                        <td>
                            Competitor
                        </td>
                        <?php
                        foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                            $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                            ?>
                            <td class="table_new_center <?= ($event_round->round == $rounds) ? 'border_right' : '' ?>" style='vertical-align: bottom'>
                                <font size='1'>
                                <?php if ($event_round->round == $rounds) { ?>
                                    <i class="<?= $rounds_dict[3]->image ?>"></i>
                                <?php } else { ?>
                                    <i class="<?= $rounds_dict[$event_round->round]->image ?>"></i>
                                <?php } ?>
                                </font>
                                <br>
                                <a  href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
                                    <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                                </a>
                            </td>
                        <?php } ?>
                    </tr>
                </thead>
                </tbody>
                <?php
                $c = 0;
                foreach ($comp_data->competitors as $competitor_id => $competitor) {
                    if (sizeof($comp_data->competitors[$competitor_id]->events) > 0) {
                        if ($c++ == 15) {
                            $c = 0;
                            ?>
                            <tr>
                                <td/>
                                <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
                                    <td align='center' class="<?= ($event_round->round == $event_round->rounds) ? 'border_right' : '' ?>">
                                        <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                                    </td>
                                <?php } ?>
                            </tr>       
                        <?php } ?>

                        <tr>
                            <td>
                                <?php
                                if ($comp->ranked) {
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
                                $result = unofficial\getCompetitorsByEventround($event_round_id)[$competitor_id] ?? FALSE;
                                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                                ?>
                                <td align="center" class="<?= ($event_round->round == $rounds) ? 'border_right' : '' ?>" >
                                    <?php if ($result) { ?>
                                        <?php if ($result->place ?? FALSE) { ?>
                                            <font align="center" class="<?= $result->podium ? 'podium' : '' ?> <?= $result->next_round ? 'next_round' : '' ?>">
                                            <?= $result->place ?>
                                            </font>
                                        <?php } else { ?>
                                            <i style='color:var(--light_gray)' class="far fa-question-circle"></i>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            Total <?= sizeof($comp_data->competitors) ?>
                        </td>
                        <?php
                        foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                            $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                            ?>
                            <td align='center' style="vertical-align:bottom;" class="<?= ($event_round->round == $rounds) ? 'border_right' : '' ?>">
                                <?php $competitors_count = sizeof($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors ?? []) ?>
                                <?php
                                $results_count = 0;
                                foreach ($comp_data->competitors as $competitor_id => $competitor) {
                                    if ($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors[$competitor_id]->place ?? FALSE) {
                                        $results_count++;
                                    }
                                }
                                ?>
                                <?= ($results_count AND $results_count != $competitors_count) ?: FALSE ?><br><?= $competitors_count ?: FALSE ?>
                            </td>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
            <?php if (!sizeof($comp_data->competitors)) { ?>
                <p>No competitors</p>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</div>