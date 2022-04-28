<h2>
    <i class="<?= $event_select->image ?>"></i> <?= $event_select->name ?>
</h2>
<h2>
    <?php if (isset($ratings[$event_select->id]['best'])) { ?>
        <a 
            class='<?= $type == 'best' ? 'select' : '' ?>' 
            href='<?= PageIndex() ?>competitions/rankings/<?= $event_select->code ?>/best'>
            <?= t('Single', 'Лучшая') ?></a>
    <?php } ?>
    <?php if (isset($ratings[$event_select->id]['average'])) { ?>
        <a 
            class='<?= $type == 'average' ? 'select' : '' ?>' 
            href='<?= PageIndex() ?>competitions/rankings/<?= $event_select->code ?>/average'>
            <?= t('Average', 'Среднее') ?></a>
    <?php } ?>
</h2>
<table class='table_new'>
    <thead>
        <tr>
            <td>#</td>
            <td><?= t('Name', 'Имя') ?></td>
            <td class="attempt"><?= t('Result', 'Результат') ?></td>
            <td><?= t('Competition', 'Соревнование') ?></td>
            <?php if ($type == 'average') { ?>
                <td colspan='5' align='center'><?= t('Solves', 'Сборки') ?></td>
            <?php } ?>
            <td align="center">WCA ID</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ratings[$event_select->id][$type] ?? [] as $rating) { ?>
            <tr>
                <td >
                    <?= $rating->order; ?>
                </td>
                <td>
                    <?php if ($rating->FCID) { ?>
                        <a href="<?= PageIndex() . "competitions/rankings/competitor/$rating->FCID" ?>">
                            <?= $rating->competitor_name ?>
                        </a>
                    <?php } else { ?>
                        <?= $rating->competitor_name ?>
                    <?php } ?>
                </td>
                <td class="<?= $rating->order == 1 ? 'record' : 'attempt' ?>">
                    <b><?= $rating->result ?></b>
                </td>
                <td>
                    <a href="<?= PageIndex() ?>competitions/<?= $rating->competition_secret ?>">
                        <?= $rating->competition_name ?>
                    </a>
                </td>
                <?php if ($type == 'average') { ?>
                    <?php foreach (range(1, 5) as $i) { ?>
                        <td class='attempt'>
                            <?= $rating->{"attempt$i"} ?? false ?>
                        </td>
                    <?php } ?>
                <?php } ?>
                <td align="center">
                    <?php if ($rating->wcaid) { ?>
                        <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $rating->wcaid ?>'>
                            <?= $rating->wcaid ?>
                        </a>
                    <?php } elseif ($rating->nonwca) { ?>
                            <?= t('none','нет') ?>
                        <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>