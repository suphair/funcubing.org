<h2>
    <i title='Competitors' class="fas fa-users"></i>
    Competitors
</h2>
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
                            <i style='color:var(--light_gray)' class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
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