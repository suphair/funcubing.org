<meta name="viewport" content="width=device-width">
<head>
    <link rel="icon" href="<?= PageLocal() ?>Pages/competitions/mobile.png" >
    <link rel="stylesheet" href="<?= PageLocal() ?>Pages/competitions/action.competitor.css" type="text/css"/>
</head>
<?php
$competitor_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$competitor_id) {
    die('Competitor id not set');
}
$results = unofficial\getResutsByCompetitor($competitor_id,
        "unofficial_events_dict.order, unofficial_events_rounds.round");
if (!$results) {
    die('Competitor not found');
}
$data = $results[0];
?>
<table>
    <thead>
        <tr>
            <th colspan="10" style="font-size:20px">
                <?= $data->competitor_name ?>
                &nbsp;&nbsp;&nbsp; 
                <a href="<?= PageIndex() . "competitions/$data->secret/competitors" ?>">
                    <?= $data->competition_name ?></a>
            </th>
        </tr>
        <tr>
            <th><?= t('Position', 'Место') ?></th>
            <th style="text-align: center;"><?= t('Solves', 'Сборки') ?></th>
            <th><?= t('Best', 'Лучшая') ?></th>
            <th><?= t('Average', 'Среднее') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $event = null;
        foreach ($results as $result) {
            $solves = [];
            for ($i = 1; $i <= 5; $i++) {
                if ($result->{"attempt$i"}) {
                    $solves[] = $result->{"attempt$i"};
                }
            }
            ?>
            <tr class="<?= $event != $result->event_dict ? 'top_border' : '' ?>">
                <td colspan="4">
                    <i class="<?= $result->event_image ?>"></i>
                    <a href="<?= PageIndex() . "competitions/$data->secret/event/$result->event_code/$result->round" ?>"><?= $result->event_name; ?></a>,
                    <?= $result->round_full_name; ?></td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold">
                    &nbsp;<?= $result->place; ?>&nbsp;
                </td>
                <td style="text-align: center;"><?= implode('&nbsp;&nbsp;&nbsp;', $solves) ?></td>
                <td style="text-align: center; font-weight: bold"" ><?= $result->best; ?></td>
                <td style="text-align: center;"><?= $result->average ?? $result->mean; ?></td>
            </tr>
            <?php
            $event = $result->event_dict;
        }
        ?>
    </tbody>
</table>
