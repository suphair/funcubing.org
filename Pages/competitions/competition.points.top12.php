<?php
$points_data = unofficial\getCompetitionPointsTop12($comp->id);
?>
<div>
    <?= $points->description ?>
</div>
<table class="table thead_stable">
    <thead>
        <tr>
            <th class="center"><?= t('Place', 'Место') ?></th>
            <th></th>
            <th class="center" style="color:green">
                <i class="<?= $points->icon ?>"></i>
            </th>
            <?php
            foreach ($points_data->head as $event_id => $head) {
                $event = $events_dict[$event_id];
                ?>
                <th class="center" title="<?= $event->name ?>">
                    <a  href="<?= PageIndex() . "competitions/$secret/event/$event->code/$head->rounds" ?> ">
                        <i class="<?= $event->image ?>"></i>
                    </a>
                    <sup><?= $head->count ?></sup>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>  
        <?php
        $pos = 0;
        $pos_cash = 0;
        $pos_points = false;
        foreach ($points_data->competitors as $competitor) {
            $pos_cash++;
            if (($competitor->points < $pos_points or $pos_points === false)) {
                $pos = $pos_cash;
                $pos_points = $competitor->points;
            }
            ?>
            <tr>
                <td class="center"><?= $competitor->points ? $pos : 'X'; ?></td>
                <td style='white-space:nowrap '>
                    <?php
                    if ($competitor->FCID) {
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
                <td class="center" style="color:green">
                    <?= $competitor->points ?>
                </td>                    
                <?php foreach (array_keys($points_data->head) as $event_id) { ?>
                    <td class="center" >
                        <?php
                        if ($competitor->events[$event_id] ?? false) {
                            $result = $competitor->events[$event_id];
                            ?>
                            <?= $result->place ?>
                            <sup style="color:green">
                                <?= $result->point ? $result->point : 'x' ?>
                            </sup>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<?php if (!sizeof($points_data->competitors)) { ?>
    <div><?= t('No data', 'Нет данных') ?></div>
    <?php
}
?>
