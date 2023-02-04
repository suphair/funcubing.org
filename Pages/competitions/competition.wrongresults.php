<?php
$wrongResults = unofficial\getWrongResults($secret);
?>
<h2>
    <i title='Wrong Results' class="fas fa-bug"></i>
    Wrong Results (обновляется раз в 10 минут)
</h2>
<table class="table thead_stable">
    <thead>
        <tr>
            <th><?= t('Event', 'Дисциплина') ?>-
                <?= t('Round', 'Раунд') ?></th>
            <th><?= t('Competitior', 'Участник') ?></th>
            <th><?= t('Limit', ' Лимит') ?></th>
            <th class="attempt"><?= t('Attempt 1', ' Попытка 1') ?></th>
            <th class="attempt"><?= t('Attempt 2', ' Попытка 2') ?></th>
            <th class="attempt"><?= t('Attempt 3', ' Попытка 3') ?></th>
            <th class="attempt"><?= t('Attempt 4', ' Попытка 4') ?></th>
            <th class="attempt"><?= t('Attempt 5', ' Попытка 5') ?></th>
            <th class="attempt"><?= t('Sum', ' Сумма') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($wrongResults as $wrongResult) {
            $event = unofficial\getEvent($events_dict, $wrongResult->event);
            $round = $wrongResult->round;
            $name = $wrongResult->name;
            $fc_id = $wrongResult->fc_id;
            $type = $wrongResult->type;
            $value = $wrongResult->value;
            $type_dict = [
                'time_limit' => t('limit', 'лимит'),
                'time_limit_cumulative' => t('sum', 'сумма'),
                'cutoff' => 'cutoff'
            ];
            $type_icon = [
                'time_limit' => 'fa-stop-circle',
                'time_limit_cumulative' => 'fa-plus-circle',
                'cutoff' => 'fa-cut'
            ];
            ?>
            <tr>
                <td>
                    <i class="<?= $event->image ?>"></i>
                    <a href="<?= PageIndex() . "competitions/$secret/event/$event->code/$round" ?>">
                        <?= $event->name ?> - <?= $round ?>
                    </a>
                </td>
                <td>
                    <?= $name ?> <sub> <?= $fc_id ?></sub>
                </td>
                <td>
                    <i class="fas <?= $type_icon[$type] ?>"></i>
                    <?= $type_dict[$type] ?? $type ?> <?= result_to_string($value) ?>
                </td>
                <?php for ($a = 1; $a <= 5; $a++) { ?>
                    <td class="attempt" 
                    <?php if ($a == $wrongResult->cutoff_attempts and $type == "cutoff") { ?>
                            style=" border-right: 1px solid red"
                        <?php } ?>
                        >
                        <span class="<?= $wrongResult->{"is_wrong$a"} ? 'error' : '' ?>">
                        <?= result_to_string($wrongResult->{"attempt$a"}) ?></td>
                    </span>
                <?php } ?>
                <td class="attempt">
                    <?php if ($type == 'time_limit_cumulative') { ?>
                        <span class="error">
                            <?= result_to_string($wrongResult->attempts_sum) ?>
                        </span>
                    <?php } ?>
                </td>

            </tr>
        <?php } ?>
    </tbody>
</table>

<?php

function result_to_string($input) {
    if ($input == -1) {
        return "DNF";
    }
    if ($input == -2) {
        return "DNS";
    }
    if ($input) {
        $minute = floor($input / 6000);
        $second = floor(($input - $minute * 6000) / 100);
        $centisecond = $input - $minute * 6000 - $second * 100;
        $format = '';
        if ($minute) {
            $format .= $minute;
        }
        if ($format) {
            $format .= ":" . substr('0' . $second, -2);
        } else {
            $format .= $second;
        }
        $format .= "." . substr('0' . $centisecond, -2);
        return $format;
    } else {
        return "";
    }
}
?>