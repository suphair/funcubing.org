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
            <th style="width:0px;"></th>
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
                <td style='white-space:nowrap '>
                    <?php
                    if ($comp->ranked and!$competitor->non_resident) {
                        $link = $competitor->FCID ? "rankings/competitor/$competitor->FCID" : false;
                    } else {
                        $link = "competitor/$competitor->id";
                    }
                    $name = t(transliterate($competitor->name_clear), $competitor->name_clear);
                    if ($link) {
                        ?>
                        <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $name ?></a>
                    <?php } else { ?>
                        <?= $name ?>
                        <?php
                    }
                    ?>
                </td>
                <td style='text-align:right'>
        <nobr>
            <?php $color = ($comp->ranked and $competitor->non_resident) ? 'color:gray' : ''; ?>
            <a style="<?= $color ?>"href="<?= PageIndex() . "competitions/$secret/?action=competitor&id=$competitor->id" ?>">
                <?php if ($competitor->fcid_show ?? false) { ?>
                    <font size="1"><?= $competitor->FCID ?></font>
                <?php } ?>
                <i class="far fa-arrow-alt-circle-right"></i></a>
        </nobr>
    </td>
    <?php
    foreach ($formats as $format) {
        $record = in_array($competitor->competitor_round, $record_attempts[str_replace('mean', 'average', $format)] ?? []);
        ?>
        <td class='attempt <?= $record ? 'td_record' : '' ?>'>
            <?= $competitor->$format ?>
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
