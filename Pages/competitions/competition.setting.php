<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $(function () {
        $("#datepicker_from").datepicker({dateFormat: "dd.mm.yy"});
        $("#datepicker_to").datepicker({dateFormat: "dd.mm.yy"});
    });
</script>
<div class="shadow2" >
    <h2>
        <i class="fas fa-cog"></i>
        Setting
    </h2>
    <form method="POST" action="?setting">
        <p>
            <input <?= $comp->show ? 'checked' : '' ?> type="radio" name="show" value="1">
            <i class="fas fa-eye"></i>
            <b>Public</b>
            </input> 
            : displayed in the competition list
        </p>
        <p>
            <input <?= !$comp->show ? 'checked' : '' ?> type="radio" name="show" value="0">
            <i class="far fa-eye-slash"></i>
            <b>Private</b>
            </input>
            : сompetitions are only visible via the link (for your testing or fun)
        </p>
        <table class="table_info">
            <tr>
                <td>Name</td> 
                <td><input required style="width:500px" type="text" name="name" value="<?= $comp->name ?>" /></td>
            </tr>
            <tr>
                <td>
                    Details
                </td>
                <td>
                    <textarea name="details" style="width:500px; height:100px; " ><?= htmlspecialchars($comp->details) ?></textarea>
                    support <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>
                </td>
            </tr>
            <tr>
                <td>Logo</td>
                <td>
                    <?php if ($comp->logo) { ?>
                        <img src="<?= $comp->logo ?>" width="50px"><br>
                    <?php } ?>
                    <input style="width:500px" type="url" name="logo" placeholder="Link to the picture" pattern="http[s]?://.*" value="<?= htmlspecialchars($comp->logo) ?>" /></td>
            </tr>
            <tr>
                <td>Date</td>            
                <td>
                    <input required  style="width:140px" type="text" id="datepicker_from" name="date" value="<?= date('d.m.Y', strtotime($comp->date)) ?>">
                    -
                    <input  style="width:140px" type="text" id="datepicker_to" name="date_to" value="<?= ($comp->date_to ?? false) ? date('d.m.Y', strtotime($comp->date_to)) : false ?>">
                </td>
            </tr>
            <tr>
                <td>Website</td>
                <td>
                    <input style="width:500px" type="url" placeholder="https://example.com" pattern="http[s]?://.*" name="website" value="<?= $comp->website ?>">
                    <?= unofficial\getFavicon($comp->website, false) ?>
                </td>
            </tr>
        </table>
        <input type="checkbox" <?= $comp->secretRegistration ? 'checked' : ''; ?> name="registration">
        <i class="fas fa-user-plus"></i> Open self-registration (competitors can register themselves)
        <?php
        if ($comp->secretRegistration) {
            $link = PageIndex() . "competitions/$comp->secret/registration/$comp->secretRegistration";
            ?>
            <br>      
            <a target='_blank' href="<?= $link ?>">link for self-registration</a>
        <?php } ?>
        <br>
        <input type="checkbox" <?= $comp->shareRegistration ? 'checked' : ''; ?> name="shareRegistration">
        Publish a link for self-registration (this link will be published on the competition page)
        <br>
        <button>
            <i class="fas fa-save"></i>
            Save
        </button>
    </form>
    <?php
    if ($comp_data->competition->delete) {
        ?>
        <form method="POST" action="?delete" onsubmit="return confirm('Delete competition?')">
            <button class="delete">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </form>    
    <?php } ?>
</div>
<?php if (unofficial\admin()) { ?>
    <div class="shadow2" >
        <?php
        $JudgeSenior_name = db::row("SELECT name FROM dict_competitors where dict_competitors.wcaid = '$comp->rankedJudgeSenior' and dict_competitors.wcaid<>''")->name ?? false;
        $JudgeJunior_name = db::row("SELECT name FROM dict_competitors where dict_competitors.wcaid = '$comp->rankedJudgeJunior' and dict_competitors.wcaid<>''")->name ?? false;
        ?>
        <h3> <i class="fas fa-mountain"></i> FunCubing Rankings</h3> 
        <form method="POST" action="?rankings_settings">
            Ranked
            <input name="ranked" type="checkbox" <?= $comp->ranked ? 'checked' : '' ?>><br>
            ID
            <input name="rankedID" value="<?= $comp->rankedID ?>"><br>
            Judge Senior
            <input name="rankedJudgeSenior" value="<?= $comp->rankedJudgeSenior ?>">
            <b><?= $JudgeSenior_name ?></b><br>
            Junior
            <input name="rankedJudgeJunior" value="<?= $comp->rankedJudgeJunior ?>"> 
            <b><?= $JudgeJunior_name ?></b><br>
            <input hidden name="return_refer" value="<?= PageIndex() ?>competitions/<?= $comp->secret_base ?>/setting">
            <button>
                <i class="fas fa-mountain"></i>
                Save
            </button>
        </form>
    </div>
<?php } ?>
<div class="shadow2" >
    <h3> <i class="fas fa-user-secret"></i> Additional organizers (all action except the settings)</h3>
    <?php
    $organizers = db::rows("SELECT "
                    . " dict_competitors.name,"
                    . " unofficial_organizers.wcaid"
                    . " FROM unofficial_organizers"
                    . " LEFT OUTER JOIN dict_competitors on unofficial_organizers.wcaid = dict_competitors.wcaid "
                    . " WHERE competition=$comp->id");
    foreach ($organizers as $organizer) {
        ?>
        <form method="POST" action="?organizer_remove">
            <input type="hidden" name="wcaid" value="<?= $organizer->wcaid ?>">
            <button class="delete">
                <i class="fas fa-user-minus"></i>
                <?= $organizer->wcaid ?>
            </button>
            <b><?= $organizer->name ?></b>
        </form>
    <?php } ?>
    <form method="POST" action="?organizer_add">
        WCAID
        <input name="wcaid" required="" value="">
        <button>
            <i class="fas fa-user-plus"></i>
            Add
        </button>    
    </form>
    <?php
    $events = unofficial\getEvents($comp->id);
    $eventsRounds = unofficial\getEventsRounds($comp->id);
    if ($comp_data->competition->events) {
        ?> 
        <div class="shadow2">
            <h2>Round settings</h2>
            <form method="POST" action="?comments">
                <table class="table_new">
                    <thead>
                        <tr>
                            <td></td>
                            <td>
                                Event
                            </td>  
                            <td>
                                Round
                            </td>  
                            <td>
                                Format
                            </td>
                            <td hidden data-event-comment>
                                Comment on competitor card
                            </td>
                            <td>
                                Cutoff
                            </td>
                            <td>
                                Time limit<br>
                                (check if cumulative)
                            </td>
                            <td>
                                Next round<br>
                                (check if %)
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event) { ?>
                            <?php
                            foreach (range(1, $event->rounds) as $round) {
                                $cutoff = $eventsRounds[$event->id][$round]->cutoff;
                                $time_limit = $eventsRounds[$event->id][$round]->time_limit;
                                $cumulative = $eventsRounds[$event->id][$round]->cumulative;
                                $nextRoundValue = $eventsRounds[$event->id][$round]->next_round_value;
                                $nextRoundProcent = $eventsRounds[$event->id][$round]->next_round_procent;
                                $format_dict = $formats_dict[$event->format_dict];
                                ?>
                                <tr>
                                    <td>
                                        <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                                    </td>
                                    <td>
                                        <?= $event->name ?>
                                    </td>
                                    <td>
                                        <?= $round ?>: 
                                        <?php if ($round == $event->rounds) { ?>
                                            Final
                                        <?php } else { ?>
                                            <?= $round == 1 ? 'First' : 'Second' ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?= $cutoff ? "$format_dict->cutoff_name / " : '' ?>
                                        <?= $format_dict->name ?>
                                    </td>
                                    <td align="left" hidden data-event-comment>
                                        <?php $comment = $eventsRounds[$event->id][$round]->comment; ?>
                                        <input style="width: 200px"  name="comments[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $comment ?>">
                                        <?= $comment ? '<i class="fas fa-comment-dots"></i>' : '' ?>
                                    </td>
                                    <td align="left">
                                        <?php if ($format_dict->cutoff_attempts) { ?>
                                            <input style="width: 50px" name="cutoff[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $cutoff ?>">
                                            <?= $cutoff ? '<i class="fas fa-cut"></i>' : '' ?>
                                        <?php } ?>
                                    </td>
                                    <td align="left">
                                        <input style="width: 50px" name="time_limit[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $time_limit ?>">
                                        <input type="checkbox" <?= $cumulative ? 'checked' : '' ?> name="cumulative[<?= $event->event_dict ?>][<?= $round ?>]">
                                        </input>
                                        <?= ($time_limit and!$cumulative) ? '<i class="fas fa-stop-circle"></i>' : '' ?>
                                        <?= ($time_limit and $cumulative) ? '<i class="fas fa-plus-circle"></i>' : '' ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($round != $event->rounds) {
                                            ?>
                                            <input type="number" name="next_round_value[<?= $event->event_dict ?>][<?= $round ?>]" required min="1" max="100" value="<?= $nextRoundValue ?>" style="width: 50px"></input>
                                            <input type="checkbox" name="next_round_procent[<?= $event->event_dict ?>][<?= $round ?>]" <?= $nextRoundProcent ? 'checked' : '' ?>></input>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a target="_blank" href="<?= PageIndex() ?>competitions/<?= $comp->secret ?>/event/<?= $events_dict[$event->event_dict]->code ?>/<?= $round ?>?action=cards&blank">
                                            Example card
                                        </a>
                                    </td>
                                </tr>    
                            <?php } ?>   
                        <?php } ?>   
                    <tbody>
                </table>
                <button>
                    <i class="far fa-save"></i>
                    Save settings
                </button>
            </form>
        <?php } ?>
        <br>
    </div>

    <div class="shadow2">
        <h2>Events</h2>
        <?php if (!$comp_data->competition->events) { ?>
            <span class='error'><br>Please select the events!</span>
        <?php } ?>
        <form method="POST" action="?rounds">
            <table class='table_new'>
                <thead>
                    <tr>
                        <td class="table_new_center" colspan='<?= sizeof($rounds_dict) ?>'>Rounds</td>
                        <td class="table_new_center" colspan='2'>Event</td>
                        <td class="table_new_center" colspan='<?= sizeof($formats_dict) ?>'>Format</td>
                        <td class="table_new_center" colspan='2'>Result</td>
                    </tr>
                    <tr>
                        <td>
                            <i class="fas fa-times"></i>
                        </td>
                        <?php
                        unset($rounds_dict[0]);
                        foreach ($rounds_dict as $round) {
                            ?>
                            <td><?= $round->name ?></td>
                        <?php } ?>
                        <td/>
                        <td></td>
                        <?php foreach ($formats_dict as $format) { ?>
                            <td><?= $format->code ?></td>
                        <?php } ?>

                        <?php foreach ($results_dict as $resultId => $result_dict) { ?>
                            <td><?= $result_dict->smallName ?></td>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($events_dict as $eventId => $event_dict) {

                        $event_data = $comp_data->events[$eventId] ?? FALSE;
                        $withResult = $event_data->withResult ?? FALSE;
                        $rounds = sizeof($event_data->rounds ?? []);
                        $format = $event_data->format_dict ?? reset($formats_dict)->id;
                        $result = $event_data->result_dict ?? $event_dict->result_dict;
                        $name = $event_data->name ?? $event_dict->name;
                        ?>
                        <tr>
                            <?php foreach (array_merge(['0' => (object) ['id' => 0, 'name' => 0]], $rounds_dict) as $roundId => $round) { ?>
                                <td align='center'>
                                    <?php $competitors = $comp_data->rounds[$eventId][$roundId + 1]->competitors ?? FALSE ?>
                                    <?php if (!$competitors) { ?>
                                        <input <?= $rounds == $roundId ? 'checked' : '' ?> type="radio" value="<?= $roundId ?>" name="events[<?= $eventId ?>][rounds]">
                                    <?php } else { ?>
                                        <i class="fas fa-lock"></i>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                            <td>
                                <i class="<?= $event_dict->image ?>"></i>
                            </td>
                            <td>
                                <?php if (!$event_dict->special) { ?>
                                    <?= $name ?>
                                <?php } else { ?>
                                    <input name="events[<?= $eventId ?>][name]" value='<?= $name ?>'>
                                <?php } ?>
                            </td>
                            <?php foreach ($formats_dict as $format_dict) { ?>
                                <td align="center">
                                    <?php if ($withResult) { ?>
                                        <?php if ($eventFormat == $format_dict->id) { ?> 
                                            <i class="far fa-dot-circle"></i>
                                            <input type="hidden" value="<?= $format_dict->id ?>" name="events[<?= $eventId ?>][format]">
                                        <?php } ?>
                                    <?php } else { ?>
                                        <input 
                                        <?= $format == $format_dict->id ? 'checked' : '' ?> 
                                            type="radio" value="<?= $format_dict->id ?>" name="events[<?= $eventId ?>][format]">
                                        <?php } ?>    
                                </td>
                            <?php } ?>
                            <?php foreach ($results_dict as $resultId => $result_dict) { ?>
                                <td align="center">
                                    <?php if (!$event_dict->special or $withResult) { ?>
                                        <?php if ($resultId == $result) { ?>
                                            <i class="far fa-dot-circle"></i>
                                        <?php } ?>
                                    <?php } else { ?>            
                                        <input <?= $resultId == $result ? 'checked' : '' ?> type="radio" value="<?= $resultId ?>" name="events[<?= $eventId ?>][result]">
                                    <?php } ?>  
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button>
                <i class="far fa-save"></i>
                Set events
            </button>
        </form>
    </div>
