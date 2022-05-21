<h2>
    <i class="<?= $event_select->image ?>"></i> <?= $event_select->name ?>
    &bull; 
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
<table class='table thead_stable'>
    <thead>
        <tr>
            <th>[<?= count($ratings[$event_select->id][$type] ?? []); ?>]</th>
            <th><?= t('Name', 'Имя') ?></th>
            <th class="attempt">
                <?= t('Result', 'Результат') ?>
            </th>
            <th><?= t('Competition', 'Соревнование') ?></th>
            <?php if ($type == 'average') { ?>
                <th><?= t('Solves', 'Сборки') ?></th>
            <?php } ?>
            <th>
                WCA ID <i class="fas fa-external-link-alt"></i>
            </th>
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
                <td class="attempt <?= $rating->order == 1 ? 'td_record' : '' ?>">
                    <?= $rating->result ?>
                </td>
                <td>
                    <?php if (in_array($rating->competition_id, explode(',', config::get('MISC', 'competition_exclude')))) { ?>
                        <?= $rating->competition_name ?>
                    <?php } else { ?>
                        <a href="<?= PageIndex() ?>competitions/<?= $rating->competition_secret ?>">
                            <?= $rating->competition_name ?>
                        </a>
                    <?php } ?>
                </td>
                <?php if ($type == 'average') { ?>
                    <?php
                    $solves = [];
                    foreach (range(1, 5) as $i) {
                        $solves[] = $rating->{"attempt$i"} ?? false;
                    }
                    ?>
                    <td class='solves'>
                        <?= implode(' ', $solves) ?>
                    </td>
                <?php } ?>
                <td align="center">
                    <?php if ($rating->wcaid) { ?>
                        <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $rating->wcaid ?>'>
                            <?= $rating->wcaid ?>
                        </a>
                    <?php } elseif ($rating->nonwca) { ?>
                        <?= t('none', 'нет') ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>