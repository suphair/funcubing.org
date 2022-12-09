<?php
$points_data = unofficial\getCompetitionPoints($comp->id);
?>
<h2>
    <div style='display: inline-block;width:50%;padding:0px;margin:0px;'>
        <i class="fas fa-star"></i>
        <?= t('Overall standings', 'Общий зачёт') ?>
    </div>
</h2>
<div>
    <?=
    t('Points are awarded to the 12 best participants of the final rounds.
        <br>There are N points for 1st place, N-1 point for 2nd place and so on. Nth place - 1 point.
        <br>N is a minimum of 12 and the number of participants in the final.',
            'Очки получает топ-12 финальных раундов.
             <br>За 1-е место N очков, за 2-е место N-1 очков и так далее. За N-е место 1 очко. 
             <br>Где N это минимум из 12 и количества участников финала.')
    ?>
</div>
<table class="table thead_stable">
    <thead>
        <tr>
            <th class="center"><?= t('Place', 'Место') ?></th>
            <th></th>
            <th class="center" style="color:green">
                <i class="fas fa-star"></i>
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
