<?php $broken = filter_input(INPUT_GET, 'broken') === ''; ?>
<h2>
    <i class="fas fa-trophy"></i> 
    <a href="?" class="<?= $broken ? '' : 'select' ?>"><?= t('Records', 'Рекорды') ?></a> | 
    <a href="?broken" class="<?= $broken ? 'select' : '' ?>"><?= t('Broken WCA Records', 'Побитые рекорды WCA') ?></a>
</h2>
<table class='table thead_stable'>
    <thead>
        <tr>
            <th><?= t('Event', 'Дисциплина') ?></th>
            <th><?= t('Type', 'Тип') ?></th>
            <th><?= t('Competitor', 'Участник') ?></th>
            <th class="attempt"><?= t('Result', 'Результат') ?></th>
            <th><?= t('Competition', 'Соревнование') ?></th>
            <th><?= t('Solves', 'Сборки') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $broken_out = [];
        $record_code_types = ['best', 'average'];
        foreach ($events_dict as $event) {
            if ($event->special) {
                continue;
            }
            $event_record = $ratings[$event->id] ?? [];
            $record_types = [
                t('Single', 'Лучшая') => current($event_record['best'] ?? []),
                t('Average', 'Среднее') => current($event_record['average'] ?? [])];
            $r = -1;
            foreach ($record_types as $type => $record) {
                $r++;
                if ($record) {
                    if ($broken and in_array($record->competition_id, explode(',', config::get('MISC', 'competition_exclude')))) {
                        continue;
                    } else {
                        $broken_out[$event->code][$record_code_types[$r]] = true;
                    }
                    ?>
                    <tr>
                        <td>
                            <i class='<?= $event->image ?>'></i>
                            <?= $event->name ?>
                        </td>
                        <td><?= $type ?>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/rankings/competitor/$record->FCID" ?>">
                                <?= $record->competitor_name ?>
                            </a>
                        </td>
                        <td class='attempt td_record'>
                            <?= $record->result ?>
                        </td>
                        <td>
                            <?php if (in_array($record->competition_id, explode(',', config::get('MISC', 'competition_exclude')))) { ?>
                                <?= $record->competition_name ?>
                            <?php } else { ?>
                                <a href="<?= PageIndex() ?>competitions/<?= $record->competition_secret ?>">
                                    <?= $record->competition_name ?>
                                </a>
                            <?php } ?>
                        </td>
                        <?php
                        $solves = [];
                        foreach (range(1, 5) as $i) {
                            $solves[] = $record->{"attempt$i"} ?? false;
                        }
                        ?>
                        <td class='solves'>
                            <?= implode(' ', $solves) ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </tbody>
</table>
<hr>
<h2>
    <i class="fas fa-history"></i>
    <?= t('History of records', 'История рекордов') ?>
</h2>
<table class='table'>
    <thead>
        <tr>
            <th><?= t('Event', 'Дисциплина') ?></th>
            <th class="attempt"><?= t('Single', 'Лучшая') ?></th>
            <th class="attempt"><?= t('Average', 'Среднее') ?></th>            
            <th><?= t('Name', 'Имя') ?></th>
            <th><?= t('Competition', 'Соревнование') ?></th>
            <th><?= t('Solves', 'Сборки') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($events_dict as $event) {
            if ($event->special) {
                continue;
            }
            foreach ($record_code_types as $type_att) {
                foreach (array_reverse($history[$event->id][$type_att] ?? []) as $r => $row) {
                    if ($broken and!($broken_out[$event->code][$type_att] ?? false)) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td>
                            <i class='<?= $event->image ?>'></i>
                            <?= $event->name ?>
                        </td>
                        <td class="attempt <?= (!$r and$type_att == 'best' ) ? 'td_record' : '' ?>">
                            <?= $type_att == 'best' ? $row->result : '' ?>
                        </td>
                        <td class="attempt <?= (!$r and$type_att == 'average' ) ? 'td_record' : '' ?>">
                            <?= $type_att == 'average' ? $row->result : '' ?>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/rankings/competitor/$row->FCID" ?>">
                                <?= $row->competitor_name ?>
                            </a>
                        </td>
                        <td>
                            <?php if (in_array($row->competition_id, explode(',', config::get('MISC', 'competition_exclude')))) { ?>
                                <?= $row->competition_name ?>
                            <?php } else { ?>
                                <a href="<?= PageIndex() ?>competitions/<?= $row->competition_secret ?>">
                                    <?= $row->competition_name ?>
                                </a>
                            <?php } ?>
                        </td>
                        <?php
                        $solves = [];
                        foreach (range(1, 5) as $i) {
                            $solves[] = $row->{"attempt$i"} ?? false;
                        }
                        ?>
                        <td class='solves'>
                            <?= implode(' ', $solves) ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </tbody>
</table>