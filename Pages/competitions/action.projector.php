<?php
$event_code = request(3);
$round = request(4);
$event_dict = $comp_data->event_dict->by_code[$event_code]->id ?? FALSE;
$event = $comp_data->rounds[$event_dict][$round]->round->id ?? FALSE;
$event_round = $comp_data->event_rounds[$event] ?? false;
if (!$event_round) {
    die("Round not found [$event_code] [$round]");
}
$event = unofficial\getEventByEventround($event_round->id);
$round_name = $rounds_dict[$round == $event_round->rounds ? 0 : $round]->fullName;
$competitors = unofficial\getCompetitorsByEventround($event_round->id, $event);
?>
<head>
    <title><?= $event->name ?>, <?= $round_name ?></title>
    <link rel="icon" href="<?= PageLocal() ?>Pages/competitions/projector.png" >
    <link rel="stylesheet" href="<?= PageLocal() ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
    <script src="<?= PageLocal() ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="<?= PageLocal() ?>Styles/index.css?4" type="text/css"/>
</head>
<style>
    table.projector, table.fixed{
        width: 100%;
        font-size:1.5rem;
    }
    table.projector tr td{
        height: 67px;
        text-align:right;
    }

    table#table_data td{
        font-family: Roboto, Helvetica, Arial, sans-serif;
        padding:16px;
        border-bottom:1px solid var(--light_gray);
        vertical-align: inherit;
    }

    table#table_data thead td{
        font-weight: bold;
        background-color: rgb(220,220,220);
    }

    body{
        margin:0px;
        width: auto;
        overflow: hidden;
    }

    .next_round{
        background-color: lightgreen;
    }

    .podium{
        background-color: darkgreen;
        color: white;
        border:1px solid #e9e9e9;
    }

    .back{
        background-color: var(--light_gray);
        color:var(--black);
        padding:7px;
        font-size:20px;
    }
</style>
<table weight='100%' id ='table_data' class='projector'>
    <thead>
        <tr id='tr_data'>
            <td  weight='5%' id='place_data' weight='10'>
                <button class='back' onclick="window.location.href = location.protocol + '//' + location.host + location.pathname;">X</button>
            </td>
            <td  weight='20%' id='event_data' style='text-align:left'>
                <?= $event->name; ?>, <?= $round_name ?>
            </td> 
            <td  weight='10%' id='attempt_data' style='text-align:center' colspan="<?= $event->attempts ?>">
                <?= t('Solves', 'Сборки'); ?>
            </td>
            <?php if ($event->format == 'average') { ?>
                <td  weight='10%' id='average_data' style='font-size: 1.3rem'>
                    <?= t('Average', 'Среднее'); ?>
                </td>
            <?php } ?>
            <?php if ($event->format == 'mean') { ?>
                <td  weight='10%' id='mean_data'  style='font-size: 1.3rem'>
                    <?= t('Mean', 'Среднее'); ?>
                </td>
            <?php } ?>
            <td  weight='10%' id='best_data'  style='font-size: 1.3rem'>
                <?= t('Best', 'Лучшая'); ?> 
            </td>            
    </thead>
    <tbody>
        <?php
        $c = 0;
        foreach ($competitors as $competitor) {
            $c++;
            ?>
            <tr data-row_competitor='<?= $c ?>'>
                <td class=" <?= $competitor->podium ? 'podium' : '' ?> <?= $competitor->next_round ? 'next_round' : '' ?>">
                    <?= $competitor->place; ?>
                </td>
                <td  style='text-align:left'>
                    <?= $competitor->name_full; ?>
                </td>
                <?php for ($i = 1; $i <= $event->attempts; $i++) { ?>
                    <td>
                        <?= str_replace(['(', ')'], '', $competitor->{"attempt$i"}); ?>
                    </td>
                <?php } ?>
                <?php if ($event->format == 'average') { ?>
                    <td class='table_new_bold'>
                        <?= $competitor->average ?? false ?>
                    </td>
                <?php } ?>
                <?php if ($event->format == 'mean') { ?>
                    <td class=' table_new_bold'>
                        <?= $competitor->mean ?? false ?>
                    </td>
                <?php } ?>
                <td class=' <?= $event->format == 'best' ? 'table_new_bold' : '' ?>'>
                    <?= $competitor->best; ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<script>
<?php include 'action.projector.js' ?>
</script>