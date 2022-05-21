<h2>
    <i class="fas fa-trophy"></i> 
    <?= t('Updated records of Speedcubing Federation', 'Обновлённые рекорды Федерации Спидкубинга') ?></h2>
<table class="table">
    <thead>
        <tr>
            <th><?= t('Event', 'Дисциплина') ?></th>
            <th><?= t('Type', 'Тип') ?></th>
            <th class='attempt'><?= t('Record', 'Рекорд') ?></th>
            <th><?= t('Competitor', 'Участник') ?></th>
            <th><?= t('Solves', 'Сборки') ?></th>
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
                            <?= $event->name ?>
                        </td>
                        <td>
                            <?= ['best' => t('Single', 'Лучшая'), 'average' => t('Average', 'Среднее')][$record->type] ?>
                        </td>
                        <td class='attempt td_record'>
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
                        <?php
                        $solves = [];
                        foreach (range(1, 5) as $i) {
                            $solves[] = $record->{"attempt$i"} ?? false;
                        }
                        ?>
                        <td class='solves'>
                            <?= implode(' ', $solves); ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>