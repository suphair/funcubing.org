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
    return $a->card > $b->card;
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
            <td align="center">
                <?= t('Result', 'Результат') ?>
            </td>
            <td class="attempt">
                <?= t('Number', 'Заявлено') ?>
            </td>
            <td class="attempt">
                <?= t('Solved', 'Собрано') ?>
            </td>
            <td align="center">
                <?= t('Time', 'Время') ?>
            </td>
            <td>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($competitors as $competitor) {
            $attempt1 = $competitor->attempt1;
            if ($competitor->attempts) {
                $number = explode(' ', $competitor->attempts)[0] ?? null;
                $solved = explode(' ', $competitor->attempts)[1] ?? null;
                $time = explode(' ', $competitor->attempts)[2] ?? 0;
            } else {
                $solved = false;
                $number = false;
                $time = false;
            }
            ?>
        <form action="?results_mbf_add" method="POST">
            <tr class='competitor_result'>
                <td style="color:<?= $competitor->podium ? 'darkgreen' : '' ?>"  align="center">
                    <input hidden value="true" name='return_refer'>
                    <input hidden name="competitor_round" value="<?= $competitor->competitor_round ?>">
                    <?= $competitor->place ?>
                    <?= $competitor->podium ? '*' : '' ?>
                </td>
                <td><?= $competitor->name ?></td>
                <td><?= $attempt1 ?></td>
                <td><input required autocomplete="off" style="width:60px; font-family:monospace;text-align:center;font-size:24px" name="number" value="<?= $number ?>"></td>
                <td><input required autocomplete="off" style="width:60px; font-family:monospace;text-align:center;font-size:24px" name="solved" value="<?= $solved ?>"></td>
                <td><input autocomplete="off" style="width:120px; font-family:monospace;text-align:center;font-size:24px" name="time"  value="<?= MBF\time_to_str($time) ?>"></td>
                <td>
                    <button><?= t('Save', 'Сохранить') ?></button>
                </td>
                <td class="attempt">
                    <?= $competitor->order ?>
                </td>
            </tr>
        </form>
    <?php } ?>
</tbody>
</table>