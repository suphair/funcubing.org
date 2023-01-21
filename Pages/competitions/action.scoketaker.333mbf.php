<?php
require_once 'function.mbf.php';

$event_code = request(3);
$round_number = request(4);

$events = [];
$event_dict_id = $comp_data->event_dict->by_code[$event_code]->id ?? FALSE;
$round_id = $comp_data->rounds[$event_dict_id][$round_number]->round->id ?? FALSE;
if (!($comp_data->event_rounds[$round_id]->id ?? FALSE)) {
    die("For competition [$comp->name] event [$event_code] with round [$round_number] not found");
}
$events = $comp_data->event_rounds;
$event = unofficial\getEventByEventround($round_id);
$round = $rounds_dict[$round_number];
$formats = array_unique([$event->format, 'best']);
?>
<head>
    <title><?= $event->name ?> / <?= $round_number ?></title>
    <link rel="icon" href="<?= PageLocal() ?>Pages/competitions/icon.png" >
    <link rel="stylesheet" href="<?= PageLocal() ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
    <script src="<?= PageLocal() ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <script src="<?= PageLocal() ?>jQuery/chosen_v1/chosen.jquery.js?2" type="text/javascript"></script>
    <script src="<?= PageLocal() ?>jQuery/tooltip.js?2" type="text/javascript"></script>
    <link rel="stylesheet" href="<?= PageLocal() ?>Styles/index.css?4" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex() ?>Styles/fontawesome-free-5.13.0-web/css/all.css" type="text/css"/>
</head>
<h2> 

    <button style="background-color: var(--light_gray)" onclick="window.location.href = location.protocol + '//' + location.host + location.pathname;">X</button>

    <?= $event->name ?> / <?= $round->fullName ?> / <?= $comp->name ?> </h2>

<?php
$competitors = unofficial\getCompetitorsByEventround($round_id, $event);
$competitors_sort = $competitors;
usort($competitors_sort, function($a, $b) {
    return $a->name > $b->name;
});
?>
<table class="table_new">
    <thead>
        <tr>
            <td>
                <?= t('Place', 'Место') ?>
            </td>
            <td>
                <?= t('Name', 'Имя') ?> 
            </td>
            <td></td>
            <td align="center">
                <?= t('Result', 'Результат') ?>
            </td>
            <td class="attempt">
                <?= t('Solved', 'Собрано') ?>
            </td>
            <td class="attempt">
                <?= t('Number', 'Заявлено') ?>
            </td>
            <td align="center">
                <?= t('Time', 'Время') ?>
            </td>
            <td align="center">
                DNF
            </td>
            <td>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($competitors_sort as $competitor) {
            $details = explode(';', ";" . $competitor->attempts);
            ?>
            <tr><td colspan="10"></td></tr>
        <form action="?results_mbf_add" method="POST">
            <input hidden value="true" name='return_refer'>
            <input hidden name="competitor_round" value="<?= $competitor->competitor_round ?>">
            <?php
            for ($a = 1; $a <= $event->attempts; $a++) {
                if ($details[$a] ?? false) {
                    $number = explode(' ', $details[$a])[0] ?? null;
                    $solved = explode(' ', $details[$a])[1] ?? null;
                    $time = explode(' ', $details[$a])[2] ?? 0;
                } else {
                    $solved = false;
                    $number = false;
                    $time = false;
                }
                ?>
                <tr class='competitor_result'>
                    <?php if ($a == 1) { ?>
                        <td rowspan="<?= $event->attempts ?>" style="border:2px solid #e9e9e9; color:<?= $competitor->podium ? 'darkgreen' : '' ?>"  align="center">
                            <?= $competitor->place ?>
                            <?= $competitor->podium ? '*' : '' ?>
                        </td>
                        <td rowspan="<?= $event->attempts ?>" style="border:2px solid #e9e9e9">
                            <?= $competitor->name ?>
                            <sup><?= $competitor->FCID ?? false ?></sup>
                        </td>
                    <?php } ?>
                    <td><b><?= $a ?></b></td>
                    <td><?= $competitor->{"attempt$a"} ?></td>
                    <td><input autocomplete="off" style="width:60px; font-family:monospace;text-align:center;font-size:24px" name="solved[<?= $a ?>]" value="<?= $solved ?>"></td>
                    <td><input autocomplete="off" style="width:60px; font-family:monospace;text-align:center;font-size:24px" name="number[<?= $a ?>]" value="<?= $number ?>"></td>
                    <td><input autocomplete="off" style="width:120px; font-family:monospace;text-align:center;font-size:24px" name="time[<?= $a ?>]"  value="<?= MBF\time_to_str($time) ?>"></td>
                    <td><input type="checkbox" name="is_dnf[<?= $a ?>]"></td>
                    <?php if ($a == 1) { ?>
                        <td rowspan="<?= $event->attempts ?>" style="border:2px solid #e9e9e9">
                            <button><?= t('Save', 'Сохранить') ?></button>
                        </td>
                        <td rowspan="<?= $event->attempts ?>" style="border:2px solid #e9e9e9">
                            <?= $competitor->order ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </form>
    <?php } ?>
</tbody>
</table>

<div>
    Время можно вводить без раздилителей<br>
    HMMSS -> H:MM:SS<br>
    MMSS -> MM:SS
</div>