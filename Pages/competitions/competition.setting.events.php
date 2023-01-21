<?php include 'competition.setting.menu.php' ?>
<?php
$events = unofficial\getEvents($comp->id);
$eventsRounds = unofficial\getEventsRounds($comp->id);
if ($comp_data->competition->events) {
    ?> 
    <h2><?= t('Round settings', 'Настройка раундов') ?></h2>
    <form method="POST" action="?comments">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>
                        <?= t('Event', 'Дисциплина') ?>
                    </th>  
                    <th>
                        <?= t('Round', 'Раунд') ?>
                    </th>  
                    <th>
                        <?= t('Format', 'Формат') ?>
                    </th>
                    <th>
                        <?= t('Cutoff', 'Катофф') ?>
                    </th>
                    <th style="text-align:right">
                        <?= t('Time limit', 'Лимит') ?>
                    </th>
                    <th>
                        - <?= t('cumulative?', 'накопительный') ?>
                    </th>
                    <th style="text-align:right">
                        <?= t('Next round', 'Проходят дальше') ?>
                    </th>
                    <th>
                        <?= t('in percent?', 'в процентах?') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event) { ?>
                    <?php
                    foreach (range(1, $event->rounds) as $round) {
                        $cutoff = $eventsRounds[$event->id][$round]->cutoff;
                        $time_limit = $eventsRounds[$event->id][$round]->time_limit;
                        $time_limit_cumulative = $eventsRounds[$event->id][$round]->time_limit_cumulative;
                        $nextRoundValue = $eventsRounds[$event->id][$round]->next_round_value;
                        $nextRoundProcent = $eventsRounds[$event->id][$round]->next_round_procent;
                        $format_dict = $formats_dict[$event->format_dict];
                        $round_dict = $rounds_dict[$round == $event->rounds ? 0 : $round];
                        ?>
                        <tr>
                            <td>
                                <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                            </td>
                            <td>
                                <?= $event->name ?>
                            </td>
                            <td>
                                <?= $round . ": " . $round_dict->smallName ?>
                            </td>
                            <td>
                                <?= $cutoff ? "$format_dict->cutoff_name / " : '' ?>
                                <?= $format_dict->name ?>
                            </td>
                            <td align="left">
                                <?php if ($format_dict->cutoff_attempts) { ?>
                                    <input style="width: 50px" pattern='[0-9]+:[0-9]{2}' title="m:ss" name="cutoff[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $cutoff ?>">
                                <?php } ?>
                            </td>
                            <td style="text-align:right">
                                <input style="width: 50px" pattern='[0-9]+:[0-9]{2}' title="m:ss" name="time_limit[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $time_limit ?>">
                            </td>
                            <td style="text-align:left">
                                <input style="width: 50px" pattern='[0-9]+:[0-9]{2}' title="m:ss" name="time_limit_cumulative[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $time_limit_cumulative ?>">
                            </td>

                            <?php
                            if ($round != $event->rounds) {
                                ?>
                                <td style="text-align:right">
                                    <input name="next_round_value[<?= $event->event_dict ?>][<?= $round ?>]"  pattern='[1-9][0-9]?|100' title="1-100" required value="<?= $nextRoundValue ?>" style="width: 50px"></input>
                                </td>
                                <td style="text-align:left">
                                    <input type="checkbox" name="next_round_procent[<?= $event->event_dict ?>][<?= $round ?>]" <?= $nextRoundProcent ? 'checked' : '' ?>></input>
                                </td>
                            <?php } else { ?>
                                <td></td><td></td>
                            <?php } ?>

                        </tr>    
                    <?php } ?>   
                <?php } ?>   
            <tbody>
        </table>
        <br>
        <button>
            <i class="far fa-save"></i>
            <?= t('Save', 'Сохранить') ?>
        </button>
    </form>
    <br>
<?php } ?>
<hr>
<h2>Настройка дисциплин</h2>
<?php if (!$comp_data->competition->events) { ?>
    <span class='error'><br><?= t('Select the events!', 'Выберите дисциплины!') ?></span>
<?php } ?>
<form method="POST" action="?rounds">
    <table class='table thead_stable'>
        <thead>
            <tr>
                <th></th>
                <th><?= t('Event', 'Дисциплина') ?></th>
                <th style="border-left:1px solid lightgray; padding:0px;margin:0px;">&nbsp;<?= t('Rounds', 'Раунды') ?></th>
                <th style='text-align:center'>
                    <i class="fas fa-times"></i>
                </th>
                <?php
                unset($rounds_dict[0]);
                foreach ($rounds_dict as $round) {
                    ?>
                    <th style='text-align:center'><?= $round->id ?></th>
                <?php } ?>
                <th style="border-left:1px solid lightgray; padding:0px;margin:0px;"></th>
                <?php foreach ($formats_dict as $format) { ?>
                    <th><?= $format->code ?></th>
                <?php } ?>
                <th style="border-left:1px solid lightgray; padding:0px;margin:0px;"></th>
                <?php foreach ($results_dict as $resultId => $result_dict) { ?>
                    <th><?= $result_dict->smallName ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $is_special = false;
            $is_etraevents = false;

            foreach ($events_dict as $eventId => $event_dict) {

                $event_data = $comp_data->events[$eventId] ?? FALSE;
                $withResult = $event_data->withResult ?? FALSE;
                $rounds = sizeof($event_data->rounds ?? []);
                $format = $event_data->format_dict ?? reset($formats_dict)->id;
                $result = $event_data->result_dict ?? $event_dict->result_dict;
                $name = $event_data->name ?? $event_dict->name;
                ?>
                <tr>
                    <td>
                        <i class="<?= $event_dict->image ?>"></i>
                    </td>
                    <td>
                        <?php if (!$event_dict->special or $event_dict->extraevents) { ?>
                            <?= $name ?>
                        <?php } else { ?>
                            <input name="events[<?= $eventId ?>][name]" value='<?= $name ?>'>
                        <?php } ?>
                    </td>
                    <td style="border-left:1px solid lightgray; padding:0px;margin:0px;"></td>
                    <?php foreach (array_merge(['0' => (object) ['id' => 0, 'name' => 0]], $rounds_dict) as $roundId => $round) { ?>
                        <td style='text-align:center'>
                            <?php $competitors = $comp_data->rounds[$eventId][$roundId + 1]->competitors ?? FALSE ?>
                            <?php if (!$competitors) { ?>
                                <input <?= $rounds == $roundId ? 'checked' : '' ?> type="radio" value="<?= $roundId ?>" name="events[<?= $eventId ?>][rounds]"'>
                            <?php } else { ?>
                                <i class="fas fa-lock"></i>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td style="border-left:1px solid lightgray; padding:0px;margin:0px;"></td>
                    <?php foreach ($formats_dict as $format_dict) { ?>
                        <td style='text-align:center'>
                            <?php if ($withResult) { ?>
                                <?php if ($eventFormat == $format_dict->id) { ?> 
                                    <i class="far fa-dot-circle"></i>
                                    <input type="hidden" value="<?= $format_dict->id ?>" name="events[<?= $eventId ?>][format]">
                                <?php } ?>
                            <?php } else { ?>
                                <input 
                                <?= $format == $format_dict->id ? 'checked' : '' ?> 
                                    type="radio" value="<?= $format_dict->id ?>" name="events[<?= $eventId ?>][format]"
                                    <?= ($event_dict->code == '333mbf' and!in_array($format_dict->code, ['Bo1', 'Bo2', 'Bo3'])) ? 'hidden' : '' ?>
                                    <?= ($event_dict->code == '333mbf' and $format_dict->code == 'Bo1') ? 'checked' : '' ?> 
                                    >
                                <?php } ?>    
                        </td>
                    <?php } ?>
                    <td style="border-left:1px solid lightgray; padding:0px;margin:0px;"></td>
                    <?php foreach ($results_dict as $resultId => $result_dict) { ?>
                        <td style='text-align:center'>
                            <?php
                            if (!$event_dict->special or $withResult or $event_dict->extraevents !== false) {
                                ?>
                                <?php if ($resultId == $result) { ?>
                                    <i class="far fa-dot-circle"></i>
                                    <input type="hidden" value="<?= $result_dict->id ?>" name="events[<?= $eventId ?>][result]">
                                <?php } ?>
                            <?php } elseif (($event_dict->code != '333mbf' and $result_dict->id != 4)) { ?>         
                                <input <?= $resultId == $result ? 'checked' : '' ?> type="radio" value="<?= $resultId ?>" name="events[<?= $eventId ?>][result]">
                            <?php } ?>  
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <br>
    <button>
        <i class="far fa-save"></i>
        <?= t('Save', 'Сохранить') ?>
    </button>
</form>