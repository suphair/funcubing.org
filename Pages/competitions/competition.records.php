<h2>
    <i class="fas fa-trophy"></i> 
    <?= t('Updated records', 'Обновлённые рекорды') ?></h2>
<table class="table_new">
    <thead>
        <tr>
            <td></td>
            <td><?= t('Event', 'Дисциплина') ?></td>
            <td><?= t('Type', 'Тип') ?></td>
            <td class='attempt'><?= t('Result', 'Результат') ?></td>
            <td><?= t('Competitor', 'Имя') ?></td>
            <td colspan='5' align='center'><?= t('Solves', 'Сборки') ?></td>
            <td></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events_dict as $event_id => $event) { ?>
            <?php
            if ($records[$event_id] ?? false) {
                foreach ($records[$event_id] as $record) {
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $event->image ?>"></i>
                        </td>
                        <td>
                            <?= $event->name ?>
                        </td>
                        <td>
                            <?= ['best' => t('Single', 'Лучшая'), 'average' => t('Average', 'Среднее')][$record->type] ?>
                        </td>
                        <td class='record'>
                            <?= $record->result ?>
                        </td>
                        <td>
                            <?php
                            $link = $record->FCID ? "rankings/competitor/$record->FCID" : false;
                            if ($link) {
                                ?>
                                <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $record->competitor_name ?></a>
                            <?php } else { ?>
                                <?= $record->competitor_name ?>
                            <?php } ?>
                        </td>
                        <?php foreach (range(1, 5) as $i) { ?>
                            <td class='attempt'>
                                <?= $record->{"attempt$i"} ?? false ?>
                            </td>
                        <?php } ?>
                        <td>
                            <a href="<?= PageIndex() ?>competitions/rankings/<?= $event->code ?>/<?= $record->type ?>">
                                <?= $ranked_icon ?> <?= t('Rankings', 'Рейтинг') ?>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>