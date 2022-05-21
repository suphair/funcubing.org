<?php
$record_attempts = [];
foreach ($records[$event->event_dict] ?? [] as $record) {
    $record_attempts[$record->type][] = $record->round_id;
}
?>

<table class="table thead_stable">
    <thead>
        <tr>
            <th width="10px"><?= t('Place', 'Место') ?></th>
            <th><?= t('Competitor', 'Имя') ?></th>
            <?php foreach ($formats as $format) { ?>
                <th class="attempt">
                    <?= t(ucfirst($format), str_replace(['mean', 'average', 'best'], ['Среднее', 'Среднее', 'Лучшая'], $format)) ?>
                </th>
                <th>

                </th>
            <?php } ?>
            <th>
                <?= t('Solves', 'Сборки') ?>
                &nbsp;
                &nbsp;
                &nbsp;
                <a target='blank' href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=projector" ?>">
                    <i class="fas fa-tv"></i>
                    <?= t('Projector', 'Проектор') ?>
                </a>
                &nbsp;
                <a target="_blank" href="?action=result">
                    <i class="fas fa-print"></i> 
                    <?= t('Print', 'Печать') ?>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td class=" center
                <?= $competitor->podium ?? false ? 'td_podium' : '' ?> 
                <?= $competitor->next_round ?? false ? 'td_next_round' : '' ?> 
                    " >
                        <?= $competitor->place ?> 
                </td>
                <td>
                    <?php
                    if ($comp->ranked) {
                        $link = $competitor->FCID ? "rankings/competitor/$competitor->FCID" : false;
                    } else {
                        $link = "competitor/$competitor->id";
                    }
                    ?>
                    <?php if ($link) { ?>
                        <a href="<?= PageIndex() . "competitions/$link" ?>">
                        <?php } ?>
                        <?= $competitor->name ?>
                        <?php if ($link) { ?>
                        </a>
                    <?php } ?>
                </td>
                <?php
                foreach ($formats as $format) {
                    $record = in_array($competitor->competitor_round, $record_attempts[str_replace('mean', 'average', $format)] ?? []);
                    ?>
                    <td class='attempt <?= $record ? 'td_record' : '' ?>'>
                        <?= strtoupper(str_replace(["dns", "-cutoff"], ["dnf", ""], $competitor->$format)) ?>
                    </td>
                    <td>
                        <?= $record ? 'R' : '' ?>
                    </td>
                <?php } ?>    
                <?php
                $attempt_prev = false;
                $cutoff_resolve = false;
                $solves = [];
                foreach (range(1, $event->attempts) as $i) {
                    $attempt = strtoupper(str_replace("dns", "", $competitor->{"attempt$i"}));

                    $solves[] = $attempt;
                    if ($event->cutoff and $i == $event->cutoff_attempts + 1 and $attempt_prev and!$attempt) {
                        $cutoff_resolve = '<i class="fas fa-cut"></i>';
                    }
                    $attempt_prev = $attempt;
                }
                ?>
                <td class='solves' colspan="2">
                    <?= implode(' ', $solves) . $cutoff_resolve; ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if (!sizeof($competitors)) { ?>
    <p><?= t('No competitors', 'Нет участников') ?></p>
<?php } ?>   
