<?php
$competitions = unofficial\getRankedCompetitions();

$comp_histoty_record = [];
foreach ($events_dict as $event) {
    foreach (['best', 'average'] as $type_att) {
        $current_record = true;
        foreach (array_reverse($history[$event->id][$type_att] ?? []) as $r => $row) {
            if ($current_record) {
                $comp_histoty_record[$row->competition_id][$type_att][$event->id] = true;
                $current_record = false;
            } else {
                $comp_histoty_record[$row->competition_id][$type_att][$event->id] = false;
            }
        }
    }
}
?>
<h2>
    <i title='Competitors' class="fas fa-cubes"></i>
    <?= t('Competitions', 'Соревнования') ?>  (<?= count($competitions) ?>)
</h2>
<table class='table thead_stable'>
    <thead>
        <tr>
            <th>
                <?= $ranked_icon ?>
            </th>
            <th>
                <?= t('Competition', 'Наименование') ?>
            </th>
            <th></th>
            <th>
                <?= t('Date', 'Дата') ?>
            </th>
            <th>
                <?php
                foreach ($events_dict as $event) {
                    if (!$event->special) {
                        $event = $events_dict[$event->id];
                        ?>
                        <i style='color:gray' title='<?= $event->name ?>' class="<?= $event->image ?>"></i>
                        <?php
                    }
                }
                ?>  
            </th>
            <th>
                <?= t('Competitors', 'Участники') ?>
            </th>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($competitions as $competition) { ?>
            <tr>   
                <td>
                    <?php if ($competition->approved) { ?>
                        <i title="Подтверждено Федерацией Спидкубинга" class="message fas fa-check"></i>
                    <?php } ?>
                </td>
                <td>                    
                    <a href="<?= PageIndex() ?>competitions/<?= $competition->secret ?>"><?= $competition->name ?> </a>
                </td>
                <td>

                    <?php if ($competition->upcoming) { ?>
                        <i class="fas fa-hourglass-start"></i>
                    <?php } ?>
                    <?php if ($competition->run) { ?>
                        <i style='color:var(--green)' class="fas fa-running"></i>
                    <?php } ?>
                <td>
                    <?= dateRange($competition->date, $competition->date_to) ?>
                </td>
                <td>
                    <?php
                    foreach ($events_dict as $event) {
                        if (!$event->special) {
                            if (isset($comp_histoty_record[$competition->id]['best'][$event->id]) or
                                    isset($comp_histoty_record[$competition->id]['average'][$event->id])) {
                                $event = $events_dict[$event->id];
                                ?>
                                <i title=' <?= $event->name ?> - <?= t('Record', 'Рекорд') ?>'class="td_record <?= $event->image ?>"></i>
                                <?php
                            } else {
                                if (unofficial\existsCompetitionEvent($competition->id, $event->id)) {
                                    ?>
                                    <i title='<?= $event->name ?>' class="<?= $events_dict[$event->id]->image ?>"></i>    
                                <?php } else { ?>
                                    <i style="visibility: hidden" class="<?= $events_dict[$event->id]->image ?>"></i>
                                <?php } ?>

                                <?php
                            }
                        }
                    }
                    ?>
                </td>
                <td align="center">
                    <?= $competition->competitors + 0 ?>
                </td>      
            </tr>
        <?php } ?>
    </tbody>
</table> 