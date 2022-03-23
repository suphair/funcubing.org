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
</head>
<h2> <?= $event->name ?> / <?= $round->fullName ?> / <?= $comp->name ?> </h2>

<?php
$competitors = unofficial\getCompetitorsByEventround($round_id);

$competitors_sort = $competitors;
usort($competitors_sort, function($a, $b) {
    return $a->card > $b->card;
});
?>
<table width="100%">
    <tr>
        <td width="10%" valign='top'>
            <span data-event-attempts='<?= $event->attempts ?>' data-event-result='<?= $event->result ?>'></span>
            <?php if (sizeof($competitors)) { ?>
                <form 
                    action='?results_add' method='POST' data-results data-results-competitor-id> 
                    <select
                        data-competitor-chosen
                        data-placeholder="Choose a competitor"
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
                    <?php foreach (range(1, $event->attempts) as $i) { ?>
                        <font style='font-family:monospace;font-size:40px'><?= $i ?> </font>
                        <input autocomplete=off data-results-attempt="<?= $i ?>" name='attempt[<?= $i ?>]' id='attempt_<?= $i ?>' style="width:200px; font-family:monospace;text-align:right;font-size:40px"><br>
                    <?php } ?>
                    &nbsp;<button hidden style='font-size:40px' id='submit_results'>
                        <i class="fas fa-save"></i>
                        Submit
                    </button>
                    <?php foreach ($formats as $f => $format) { ?>
                        <input hidden name='attempt[<?= $format ?>]' data-results-attempts-<?= $format ?>></span>
                    <?php } ?>
                </form>
            <?php } ?>

            <p><?= $event->comment ?></p>
            <p><?= $event->cutoff ? ('<i class="fas fa-cut"></i> Cutoff ' . $event->cutoff ) : '' ?></p>
            <p><?= ($event->time_limit and!$event->cumulative) ? ('<i class="fas fa-stop-circle"></i> Time limit ' . $event->time_limit ) : '' ?></p>
            <p><?= ($event->time_limit and $event->cumulative) ? ('<i class="fas fa-plus-circle"></i> Time limit ' . $event->time_limit . ' cumulative' ) : '' ?></p>

        </td>
        <td width="50%" valign='top'>
            <table class="table_new">
                <thead>
                    <tr>
                        <td>
                            Card
                        </td>
                        <td>
                            Name
                        </td>
                        <?php foreach (range(1, $event->attempts) as $i) { ?>
                            <td class="attempt">
                                <?= $i ?>
                            </td>
                        <?php } ?>
                        <td>
                            #
                        </td>
                        <?php foreach ($formats as $format) { ?>  
                            <td class="attempt">
                                <?= ucfirst($format) ?>
                            </td>  
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competitors as $competitor) { ?>
                        <tr class='competitor_result' 
                            data-competitor 
                            data-competitor-id='<?= $competitor->competitor_round ?>' 
                            data-competitor-name='<?= $competitor->card . ' ' . $competitor->name ?>' 
                            data-competitor-attempts='<?= $competitor->attempts ?>'
                            <?php foreach (range(1, $event->attempts) as $i) { ?>
                                data-competitor-attempt<?= $i ?>='<?= $competitor->{"attempt$i"} ?>'
                            <?php } ?>>
                            <td><?= $competitor->card ?> </td>
                            <td><?= $competitor->name ?></td>
                            <?php foreach (range(1, $event->attempts) as $i) { ?>
                                <td class="attempt">
                                    <?= $competitor->{"attempt$i"} ?>
                                </td>
                            <?php } ?>
                            <td><?= $competitor->place ?></td>
                            <?php foreach ($formats as $format) { ?>  
                                <td class="attempt table_new_bold">
                                    <?= $competitor->$format ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <form method="POST" action="?results_delete"
                  onsubmit="return confirm('Remove ALL registrations without results?') && confirm('Remove ALL registrations without results? You are sure? ')">
                <button class="delete">
                    <i class="fas fa-trash"></i>
                    Remove registrations without results
                </button>
                <input hidden value="true" name='return_refer'>
            </form>
            <br><a target="_blank" href="?action=result">Print the results</a>
        </td>
    </tr>
</table>
<script>
<?php include 'action.scoketaker.js' ?>
</script>