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
<table class='table_new'>
    <thead>
        <tr>
            <td>
                <?= t('Competition', 'Наименование') ?>
            </td>
            <td>
                <i title='<?= t('Competitors', 'Участники') ?>' class="fas fa-users"></i>
            </td>
            <td></td>
            <td>
                <?= t('Date', 'Дата') ?>
            </td>
            <td><?= t('Single Record', 'Рекорд лучшая') ?></td>
            <td><?= t('Average Record', 'Рекорд среднее') ?></td>

        </tr>    
    </thead>
    <tbody>
        <?php foreach ($competitions as $competition) { ?>
            <tr>   
                <td>                    
                    <a href="<?= PageIndex() ?>competitions/<?= $competition->secret ?>"><?= $competition->name ?> </a>
                </td>
                <td align="center">
                    <?= $competition->competitors + 0 ?>
                </td>      
                <td>
                    <?php if ($competition->upcoming) { ?>
                        <i style='color:var(--gray)' class="fas fa-hourglass-start"></i>
                    <?php } ?>
                    <?php if ($competition->run) { ?>
                        <i style='color:var(--green)' class="fas fa-running"></i>
                    <?php } ?>
                <td>
                    <?= dateRange($competition->date, $competition->date_to) ?>
                </td>
                <?php foreach (['best', 'average'] as $type) { ?>
                    <td>

                        <?php
                        foreach ($events_dict as $event) {
                            if (!$event->special) {
                                if (isset($comp_histoty_record[$competition->id][$type][$event->id])) {
                                    $current = $comp_histoty_record[$competition->id][$type][$event->id];
                                    if ($current) {
                                        ?>
                                        <i title='<?= t('Current Record', 'Текущий рекорд') ?>' style="color:rgb(0,100,0)" class="<?= $events_dict[$event->id]->image ?>"></i>
                                    <?php } else { ?>
                                        <i title='<?= t('History Record', 'Рекорд в истории') ?>' style="color:var(--black)" class="<?= $events_dict[$event->id]->image ?>"></i>
                                        <?php
                                    }
                                } else {
                                    if (unofficial\existsCompetitionEvent($competition->id, $event->id)) {
                                        ?>
                                        <i style="color:var(--light_gray)" class="<?= $events_dict[$event->id]->image ?>"></i>    
                                    <?php } else { ?>
                                        <i style="visibility: hidden" class="<?= $events_dict[$event->id]->image ?>"></i>
                                    <?php } ?>

                                    <?php
                                }
                            }
                        }
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table> 