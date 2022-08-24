<?php $competitors = unofficial\getRankedCompetitors(); ?>
<!--
<h2>
    <i class="fas fa-trophy"></i> 
    <?= t('Record holders', 'Держатели рекордов') ?> 
</h2>

<?php
$holder_records = [];
foreach ($competitors as $c => $competitor) {
    $holder_records[$competitor->FCID] = [];
    $holder_records[$competitor->FCID]['best'] = $competitor_current_record[$competitor->FCID]['best'] ?? [];
    $holder_records[$competitor->FCID]['average'] = $competitor_current_record[$competitor->FCID]['average'] ?? [];
    $competitors[$c]->holder_records = sizeof($holder_records[$competitor->FCID]['best']) + sizeof($holder_records[$competitor->FCID]['average']);
}

$competitors_holder_records = $competitors;
usort($competitors_holder_records, function($a, $b) {
    return $a->holder_records < $b->holder_records;
});
?>

<table class="table">
    <thead>
        <tr>
            <th><?= t('Name', 'Имя') ?> </th>
            <th><?= t('Single', 'Лучшая') ?></th>
            <th><?= t('Average', 'Среднее') ?></th>
            <th>
                WCA ID <i class="fas fa-external-link-alt"></i>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($competitors_holder_records as $competitor) {
            
            $holder_records_best = $holder_records[$competitor->FCID]['best'];
            $holder_records_average = $holder_records[$competitor->FCID]['average'];
            if ($holder_records_best or $holder_records_average) {
                ?>
                <tr>
                    <td>
                        <a href="<?= PageIndex() . "competitions/rankings/competitor/$competitor->FCID" ?>">
                            <?= $competitor->name ?>
                        </a>
                    </td>
                    <td>
                        <?php
                        foreach ($holder_records_best as $record) {
                            $event = $events_dict[$record->event_id];
                            if ($event->special) {
                                continue;
                            }
                            ?>
                            <a title='<?= $event->name ?>' href='<?= PageIndex() ?>competitions/rankings/<?= $event->code ?>/best'>
                                <nobr><i class="<?= $event->image ?>"></i> <?= $event->name ?></nobr>
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php
                        foreach ($holder_records_average as $record) {
                            $event = $events_dict[$record->event_id];
                            if ($event->special) {
                                continue;
                            }
                            ?>
                            <a title='<?= $event->name ?>' href='<?= PageIndex() ?>competitions/rankings/<?= $event->code ?>/average'>
                                <nobr><i class="<?= $event->image ?>"></i> <?= $event->name ?></nobr>
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($competitor->wcaid) { ?>
                            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $competitor->wcaid ?>'>
                                <?= $competitor->wcaid ?>
                            </a>
                        <?php } elseif ($competitor->nonwca) { ?>
                            <?= t('none', 'нет') ?>
                        <?php } ?>
                    </td>
                </tr>

                <?php
            }
        }
        ?>
    </tbody>
</table>
<hr>
-->
<h2>
    <i title='Competitors' class="fas fa-users"></i>
    <?= t('Competitors', 'Участники') ?> (<?= count($competitors) ?>)
</h2>
<table class="table thead_stable">
    <thead>
        <tr>
            <th><?= t('Name', 'Имя') ?></th>
            <th><?= t('Competitions', 'Соревнования') ?></th>
            <th>WCA ID <i class="fas fa-external-link-alt"></i></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td>
                    <a href="<?= PageIndex() . "competitions/rankings/competitor/$competitor->FCID" ?>">
                        <?php if ($competitor->name != $competitor->name_other) { ?>
                            <?= $competitor->name ?> (<?= $competitor->name_other ?>)
                        <?php } else { ?>    
                            <?php if (t(true, false)) { ?>
                                <?= transliterate($competitor->name) ?> <?= $competitor->name ?>)
                            <?php } else { ?>
                                <?= $competitor->name ?> (<?= transliterate($competitor->name) ?>)
                            <?php } ?>
                        <?php } ?>
                    </a>
                </td>
                <td align="center">
                    <?php
                    $competitions = [];
                    foreach (explode(',', $competitor->competitions_secret) as $c => $secret) {
                        if (!in_array($secret, explode(",", config::get('MISC', 'competition_exclude_secret')))) {
                            $competitions[] = "<a href='" . PageIndex() . "/competitions/$secret'>" . (explode(',', $competitor->competitions_name)[$c] ?? '???') . "</a>";
                        }
                    }
                    ?>
        <nobr><?= implode(',</nobr> <nobr>', $competitions) ?></nobr>
    </td>
    <td>
        <?php if ($competitor->wcaid) { ?>
            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $competitor->wcaid ?>'>
                <?= $competitor->wcaid ?>
            </a>
        <?php } elseif ($competitor->nonwca) { ?>
            <?= t('none', 'нет') ?>
        <?php } ?>
    </td>
    </tr>
<?php } ?>
</tbody>
</table>
