<?php
$record_attempts = [];
foreach ($records[$event->event_dict] ?? [] as $record) {
    $record_attempts[$record->type][] = $record->round_id;
}
?>

<table class="table_new">
    <thead>
        <tr>
            <td><?= t('Place', 'Место') ?></td>
            <td><?= t('Competitor', 'Имя') ?></td>
            <?php foreach (range(1, $event->attempts) as $i) { ?>
                <td class="attempt"><?= $i ?></td>
            <?php } ?>
            <?php foreach ($formats as $format) {?>
                <td class="attempt"><?= t(ucfirst($format), str_replace(['mean', 'average', 'best'], ['Среднее', 'Среднее', 'Лучшая'], $format)) ?></td>
            <?php } ?>
        <tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td class=" table_new_center <?= $competitor->podium ? 'podium' : '' ?> <?= $competitor->next_round ? 'next_round' : '' ?>">
                    <?= $competitor->place ?> 
                    <?= $competitor->next_round ? '&bull;' : '' ?>
                    <?= $competitor->podium ? '*' : '' ?>
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
                $attempt_prev = false;
                foreach (range(1, $event->attempts) as $i) {
                    $attempt = strtoupper(str_replace("dns", "", $competitor->{"attempt$i"}));
                    ?>
                    <td class="<?= $i == $event->attempts ? 'border-right-solid' : '' ?> attempt">
                        <?= $attempt ?>
                        <?php if ($event->cutoff and $i == $event->cutoff_attempts + 1 and $attempt_prev and!$attempt) { ?>
                            <i class="fas fa-cut"></i>
                        <?php } ?>
                    </td>
                    <?php
                    $attempt_prev = $attempt;
                }
                ?>

                <?php
                foreach ($formats as $format) {
                    $record = in_array($competitor->competitor_round, $record_attempts[str_replace('mean', 'average', $format)] ?? []);
                    ?>
                    <td class="<?= $record ? 'record' : 'attempt' ?>">
                        <b>
                            <?= strtoupper(str_replace(["dns", "-cutoff"], ["dnf", ""], $competitor->$format)) ?>
                        </b>
                    </td>
                <?php } ?>    
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if (!sizeof($competitors)) { ?>
    <p>No competitors</p>
<?php } ?>   
