<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $(function () {
        $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
    });
</script>
<div class="shadow2" >
    <h2>
        <i class="fas fa-cog"></i>
        Setting
    </h2>
    <form method="POST" action="?setting">
        Name 
        <input required type="text" name="name" value="<?= $comp->name ?>" />
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
        Details 
        <input type="text" name="details" value="<?= $comp->details ?>" />
        <br>
        Date            
        <input required  style="width:140px" required type="text" id="datepicker" name="date" value="<?= date('d.m.Y', strtotime($comp->date)) ?>">
        <br>
        Website
        <input type="url" placeholder="https://example.com" pattern="http[s]?://.*" name="website" value="<?= $comp->website ?>">
        <br>
        <input type="checkbox" <?= $comp->secretRegistration ? 'checked' : ''; ?> name="registration">
        <i class="fas fa-user-plus"></i> Open self-registration (competitors can register themselves)
        <?php
        if ($comp->secretRegistration) {
            $link = PageIndex() . "unofficial/$comp->secret/registration/$comp->secretRegistration";
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
</div>

<?php
$events = unofficial\getEvents($comp->id);
$eventsRounds = unofficial\getEventsRounds($comp->id);
if ($comp_data->competition->events) {
    ?> 
    <div class="shadow2">
        <h2>Comments for competitors cards</h2>
        <form method="POST" action="?comments">
            <table class="table_new">
                <thead>
                    <tr>
                        <td></td>
                        <td>
                            Event
                        </td>  
                        <td>
                            Format
                        </td>
                        <?php foreach (range(1, $comp_data->competition->rounds_max) as $round) { ?>
                            <td align="center">
                                Round <?= $round ?>
                            </td>
                        <?php } ?>    
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event) { ?>
                        <tr>
                            <td>
                                <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                            </td>
                            <td>
                                <?= $event->name ?>
                            </td>
                            <td>
                                <?= $formats_dict[$event->format_dict]->name ?>
                            </td>
                            <?php foreach (range(1, $event->rounds) as $round) { ?>
                                <td align="center">
                                    <input name="comments[<?= $event->event_dict ?>][<?= $round ?>]" value="<?= $eventsRounds[$event->id][$round]->comment ?>">
                                </td>
                            <?php } ?>        
                        </tr>    
                    <?php } ?>   
                <tbody>
            </table>
            <button>
                <i class="far fa-save"></i>
                Set comments
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
                    <td class="table_new_center" colspan='<?= sizeof($rounds_dict) + 1 ?>'>Rounds</td>
                    <td class="table_new_center" colspan='2'>Event</td>
                    <td class="table_new_center" colspan='<?= sizeof($formats_dict) ?>'>Format</td>
                    <td class="table_new_center" colspan='2'>Result</td>
                </tr>
                <tr>
                    <td>
                        <i class="fas fa-times"></i>
                    </td>
                    <?php foreach ($rounds_dict as $round) { ?>
                        <td><?= $round->name ?></td>
                    <?php } ?>
                    <td/>
                    <td></td>
                    <?php foreach ($formats_dict as $format) { ?>
                        <td><?= $format->code ?></td>
                    <?php } ?>

                    <?php foreach ($results_dict as $resultId => $result_dict) { ?>
                        <td><?= $result_dict->name ?></td>
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
