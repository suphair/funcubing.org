<?php
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
<table width="100%">
    <tr>
        <td width="10%" valign='top'>
            <span data-event-attempts='<?= $event->attempts ?>'
                  data-event-result='<?= $event->result_code ?>'></span>
                  <?php if (sizeof($competitors)) { ?>
                <form 
                    action='?results_add' method='POST' data-results data-results-competitor-id> 
                    <select
                        data-competitor-chosen
                        data-placeholder="<?= t('Choose a competitor', 'Выберите участника') ?>"
                        class="chosen-select" multiple>
                            <?php foreach ($competitors_sort as $competitor) { ?>
                            <option value="<?= $competitor->competitor_round ?>">
                                <?= $competitor->card ?> <?= $competitor->name ?>
                                <?= $competitor->place ? '' : ' ?' ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input hidden data-results-name>
                    <input hidden value="true" name='return_refer'>
                    <input hidden data-save-competitor-id name='competitor_round'>

                    <input hidden data-results-attempts name='attempts'><br>
                    <input hidden data-results-exclude name='exclude'><br>
                    <?php foreach (range(1, $event->attempts) as $i) { ?>
                        <font style='font-family:monospace;font-size:40px'><?= $i ?> </font>
                        <input autocomplete=off data-results-attempt="<?= $i ?>" name='attempt[<?= $i ?>]' id='attempt_<?= $i ?>' style="width:200px; font-family:monospace;text-align:right;font-size:40px"><br>
                    <?php } ?>
                    <font style='font-family:monospace;font-size:40px'>&nbsp; </font>
                    <button hidden style='font-size:40px' id='submit_results'>
                        <?= t('Submit', 'Подтвердить') ?>
                    </button>
                    <?php foreach ($formats as $f => $format) { ?>
                        <input hidden name='attempt[<?= $format ?>]' data-results-attempts-<?= $format ?>></span>
                    <?php } ?>
                </form>
            <?php } ?>
            <br>
            <p><?= $event->comment ?></p>
            <p>
                <?php if ($event->result_code == 'amount_asc') { ?>
                    <i class="fas fa-sort-numeric-down"></i>
                    <?= $event->result_name ?><?php } ?>
                <?php if ($event->result_code == 'amount_desc') { ?>
                    <i class="fas fa-sort-numeric-down-alt"></i>
                    <?= $event->result_name ?>
                <?php } ?>

            </p>
            <p><?= $event->cutoff ? ('<i class="fas fa-cut"></i> ' . t('Cutoff', 'Катофф') . ' ' . $event->cutoff ) : '' ?></p>
            <p><?= ($event->time_limit and!$event->cumulative) ? ('<i class="fas fa-stop-circle"></i> ' . t('Time limit', 'Лимит по времени') . ' ' . $event->time_limit ) : '' ?></p>
            <p><?= ($event->time_limit and $event->cumulative) ? ('<i class="fas fa-plus-circle"></i> ' . t('Time limit', 'Лимит по времени') . ' ' . $event->time_limit . ' ' . t('cumulative', 'суммарно') ) : '' ?></p>
            <p><b>DNF</b>: f, F, /, -, d, D.</p>
            <p><b>DNS</b>: s, S, *.</p>
        </td>
        <td width="50%" valign='top'>
            <table class="table_new">
                <thead>
                    <tr>
                        <td>
                            <?= t('Place', 'Место') ?>
                        </td>
                        <td>
                            <?= t('Name', 'Имя') ?> 
                        </td>
                        <?php foreach (range(1, $event->attempts) as $i) { ?>
                            <td class="attempt">
                                <?= $i ?>
                            </td>
                        <?php } ?>
                        <?php foreach ($formats as $format) { ?>  
                            <td class="attempt">
                                <?= t(ucfirst($format), str_replace(['mean', 'average', 'best'], ['Среднее', 'Среднее', 'Лучшая'], $format)) ?>
                            </td>  
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $competitor_without_results = 0;
                    $next_round_competitors = 0;
                    $next_round_register = 0;
                    foreach ($competitors as $competitor) {
                        if ($competitor->next_round_register) {
                            $next_round_register++;
                        }
                        if (!$competitor->place) {
                            $competitor_without_results++;
                        }
                        if ($competitor->next_round and!$event->final) {
                            $next_round_competitors++;
                        }
                        ?>
                        <tr class='competitor_result' 
                            data-competitor 
                            data-competitor-id='<?= $competitor->competitor_round ?>' 
                            data-competitor-name='<?= $competitor->card . ' ' . $competitor->name_full ?>' 
                            data-competitor-attempts='<?= $competitor->attempts ?>'
                            <?php foreach (range(1, $event->attempts) as $i) { ?>
                                data-competitor-attempt<?= $i ?>='<?= str_replace(['(', ')'], '', $competitor->{"attempt$i"}) ?>'
                            <?php } ?>>
                            <td style="color:<?= ($competitor->next_round or $competitor->podium) ? 'darkgreen' : '' ?>">
                                <?= $competitor->place ?>
                                <?= $competitor->next_round ? '&bull;' : '' ?>
                                <?= $competitor->podium ? '*' : '' ?>
                            </td>
                            <td>
                                <?= $competitor->name_full ?>
                            </td>
                            <?php foreach (range(1, $event->attempts) as $i) { ?>
                                <td class="attempt">
                                    <?= $competitor->{"attempt$i"} ?>
                                </td>
                            <?php } ?>
                            <?php foreach ($formats as $format) { ?>  
                                <td class="attempt table_new_bold">
                                    <?= $competitor->$format ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <table width="100%">
                <tr>
                    <td width="50%" valign="top">
                        <a target="_blank" href="?action=result"><?= t('Print results', 'Печать результатов') ?></a>
                    </td>
                    <td width="50%" align="right">
                        <?php $complete = false; ?>
                        <?php
                        if ($competitor_without_results) {
                            $complete = true;
                            ?>
                            <p><?= t('Remove', 'Отменить') ?> <?= $competitor_without_results ?> <?= t('competitors without results', 'участников без результатов') ?></p>
                        <?php } ?>
                        <?php
                        if ($next_round_competitors and!$next_round_register) {
                            $complete = true;
                            ?>
                            <p><?= t('Register', 'Зарегистровать') ?> <?= $next_round_competitors ?> <?= t('competitors for the next round', 'участников в следующий раунд') ?></p>
                        <?php } ?>
                        <?php if ($complete) { ?>
                            <form action='?close_round' method='POST'>
                                <button>
                                    <i class="fas fa-check-double"></i> <?= t('Close this round', 'Завершить текущий раунд') ?>
                                </button>
                            </form>
                        <?php } elseif (sizeof($competitors)) { ?>
                            <i class="fas fa-check-double"></i> <?= t('This round is closed', 'Текущий раунд завершен') ?>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script>
<?php include 'action.scoketaker.js'
?>
</script>