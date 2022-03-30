<?php $competitors = unofficial\getRankedCompetitors(); ?>
<h2>
    <i class="fas fa-trophy"></i> 
    Record holders
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

<table class="table_new">
    <thead>
        <tr>
            <td>Name</td>
            <td>FC ID</td>
            <td align="center">Single</td>
            <td align="center">Average</td>
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
                        <?= $competitor->FCID ?>
                    </td>
                    <td align="center">
                        <?php foreach ($holder_records_best as $record) { ?>
                            <i class="<?= $events_dict[$record->event_id]->image ?>"></i>
                        <?php } ?>
                    </td>
                    <td align="center">
                        <?php foreach ($holder_records_average as $record) { ?>
                            <i class="<?= $events_dict[$record->event_id]->image ?>"></i>
                        <?php } ?>
                    </td>
                    <td align="center">

                    </td>
                </tr>

                <?php
            }
        }
        ?>
    </tbody>
</table>

<h2>
    <i title='Competitors' class="fas fa-users"></i>
    Competitors (<?= count($competitors) ?>)
</h2>
<table class="table_new">
    <thead>
        <tr>
            <td>Name</td>
            <td>FC ID</td>
            <td>Competitions</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td>
                    <a href="<?= PageIndex() . "competitions/rankings/competitor/$competitor->FCID" ?>">
                        <?= $competitor->name ?>
                    </a>
                </td>
                <td>
                    <?= $competitor->FCID ?>
                </td>
                <td align="center">
                    <?= $competitor->competitions ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
