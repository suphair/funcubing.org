<h1>
    <a 
        class="<?= !$event_round_this ? 'select' : '' ?>"
        href="<?= PageIndex() . "unofficial/$secret" ?>">
        <i title='All events' class="fas fa-border-all"></i>
    </a>
    <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
        <a class="<?= $event_round_this == $event_round_id ? 'select' : '' ?>"
           title="<?= $comp_data->events[$event_round->event_dict]->name ?> / round <?= $event_round->round ?>"
           href="<?= PageIndex() . "unofficial/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
            <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
        </a>
    <?php } ?>
</h1>   
<div class="shadow2" >
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
                        Total <?= sizeof($comp_data->competitors) ?>
                    </td>
                    <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
                        <td class="table_new_center" style='vertical-align: bottom'>
                            <font size='1'>
                            <?php $rounds = $comp_data->events[$event_round->event_dict]->event_rounds; ?>
                            <?php if ($event_round->round == $rounds) { ?>
                                <i class='far fa-star'></i>
                            <?php } else { ?>
                                <i class="<?= $rounds_dict[$event_round->round]->image ?>"></i>
                            <?php } ?>
                            </font>
                            <br>
                            <a  href="<?= PageIndex() . "unofficial/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
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
                                <td align='center'>
                                    <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                                </td>
                            <?php } ?>
                        </tr>       
                    <?php } ?>

                    <tr>
                        <td>
                            <a href="<?= PageIndex() . "unofficial/competitor/$competitor->id" ?>">
                                <?= $competitor->name ?>
                            </a>
                        </td>
                        <?php
                        foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                            $result = unofficial\getCompetitorsByEventround($event_round_id)[$competitor_id] ?? FALSE;
                            ?>
                            <td align="center"  style="border-right: 0px;" >
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
                    </td>
                    <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
                        <td align='center' style="vertical-align:bottom;">
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
</div>