<?php $competitors = unofficial\getRankedCompetitors(); ?>
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
            <td>Current Records</td>
            <td>History Records</td>
        </tr>

    </thead>
    <tbody>
        <?php
        foreach ($competitors as $competitor) {
            $current_records = sizeof($competitor_current_record[$competitor->FCID]['best'] ?? []) +
                    sizeof($competitor_current_record[$competitor->FCID]['average'] ?? [])
            ;
            $history_records = sizeof($competitor_history_record[$competitor->FCID]['best'] ?? []) +
                    sizeof($competitor_history_record[$competitor->FCID]['average'] ?? [])
            ;
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
                    <?= $competitor->competitions ?>
                </td>
                <td align="center" class="record">
                    <?= $current_records ? $current_records : '' ?>
                </td>
                <td align="center">
                    <?= $history_records ? $history_records : '' ?>
                </td>
            </tr>

        <?php } ?>
    </tbody>
</table>
