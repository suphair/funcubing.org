<style>
    .competitor_result:hover td{
        cursor: pointer;
        background-color: var(--light_green);
    }
    .competitor_select td{
        cursor: pointer;
        background-color: var(--green);
    }
    .chosen-container{
        padding:0px;
    }
</style>
<?php
$event = unofficial\getEventByEventround($event_round_this);
$competitors = unofficial\getCompetitorsByEventround($event_round_this);
$formats = array_unique([$event->format, 'best']);
?>    

<h2>
    <i class="<?= $event->image ?>"></i>
    <?= $event->name ?>
    <?php if ($event->final and $event->rounds > 1) { ?>    
        , final
    <?php } ?>
    <?php if (!$event->final and $event->rounds > 1) { ?>   
        , round <?= $event->round ?>
    <?php } ?>    
</h2> 

<span data-event-attempts='<?= $event->attempts ?>' data-event-result='<?= $event->result ?>'></span>
<p>
    <?= $event->comment ?>
    <?= $event->cutoff ? ('<i class="fas fa-cut"></i> Cutoff ' . $event->cutoff ) : '' ?>
    <?= ($event->time_limit and!$event->cumulative) ? ('<i class="fas fa-stop-circle"></i> Time limit ' . $event->time_limit ) : '' ?>
    <?= ($event->time_limit and $event->cumulative) ? ('<i class="fas fa-plus-circle"></i> Time limit ' . $event->time_limit . ' cumulative' ) : '' ?>
    <span class="data_tooltip" 
          data-tooltip="
          <font style='var(--green)'>▪</font> Click on competitor row in table to enter attempts<br>
          <font style='var(--green)'>▪</font> Entered attempt without delimiters<font style='color:var(--green)'>:</font> [11122] <font style='color:var(--green)'>&#8658;</font> 1:11.22<br>
          <font style='var(--green)'>▪</font> Attempts are separated by spaces<font style='color:var(--green)'>:</font> [1322 2243] <font style='color:var(--green)'>&#8658;</font> <b>1)</b> 0:13.22 <b>2)</b> 0:22.43<br>
          <font style='var(--green)'>▪</font> DNF is entered as <b>-</b> or <b>DNF</b> or <b>F</b><font style='color:var(--green)'>:</font> [1134 -] <font style='color:var(--green)'>&#8658;</font> <b>1)</b> 0.11:34 <b>2)</b> DNF<br>
          <font style='var(--green)'>▪</font> DNS is entered as <b>0</b> or <b>DNS</b> or <b>S</b><font style='color:var(--green)'>:</font> [232 0 454] <font style='color:var(--green)'>&#8658;</font> <b>1)</b> 0.02:32 <b>2)</b> DNS <b>3)</b> 0:04.54<br>
          <font style='var(--green)'>▪</font> Press enter to save attempts">
        <i class="fas fa-info-circle"></i> 
        Instruction
    </span>
</p>
<?php if (sizeof($competitors)) { ?>
    <p>
        <select
            data-competitor-chosen
            data-placeholder="Choose a competitor"
            class="chosen-select" multiple>
                <?php foreach ($competitors as $competitor) { ?>
                <option value="<?= $competitor->competitor_round ?>">
                    <?= $competitor->name ?>
                    <?= $competitor->place ? '' : ' ?' ?>
                </option>
            <?php } ?>
        </select>
        <span style="color:var(--red);font-weight: bold; font-size: 20px;" data-results-name></span>
    </p>
    <form action='?results_add' method='POST' data-results data-results-competitor-id> 
        <input hidden data-save-competitor-id name='competitor_round'>
        <input disabled autocomplete=off data-results-attempts name='attempts' placeholder="Enter results" style="width:400px; font-size:18px">
        <button hidden>
            <i class="fas fa-save"></i>
            [Enter]
        </button>
        <div style="text-align:left; width:100%; font-size:18px; padding:2px; color:var(--gray);" id="ParseName">
        </div> 
        <?php foreach (range(1, $event->attempts) as $i) { ?>
            <input hidden name='attempt[<?= $i ?>]' id='attempt_<?= $i ?>'>
            <div style=" width:100px; display: inline-block; text-align:center;font-size:18px; border:1px solid var(--white); padding:2px;" 
                 class="Attempt_edit" data-event-attempt-<?= $i ?>>
            </div> 
        <?php } ?>
        <?php foreach ($formats as $f => $format) { ?>
            <input hidden name='attempt[<?= $format ?>]' id='attempt_<?= $format ?>'>
            <div style="width:200px; display: inline-block; text-align:center; font-size:18px; border:1px solid var(--white); padding:2px;" 
                 class="Attempt_edit">
                <span data-results-attempts-<?= $format ?>></span>
            </div>
        <?php } ?>
    </form>
<?php } ?>
<table class="table_new">
    <thead>
        <tr>
            <td>
                #
            </td>
            <td>
                Competitor [<?= sizeof($competitors) ?>]
            </td>
            <td></td>
            <td align='center' colspan ="<?= $event->attempts ?>">
                Solves
            </td>
            <?php foreach ($formats as $format) { ?>  
                <td align='center'>
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
                data-competitor-name='<?= $competitor->name ?>' 
                data-competitor-attempts='<?= $competitor->attempts ?>'>
                <td class="<?= $competitor->podium ? 'podium' : '' ?> <?= $competitor->next_round ? 'next_round' : '' ?>">
                    <?= $competitor->place ?>
                </td>
                <td >
                    <?= $competitor->name ?>
                </td>
                <td>
                    <?php if (!$competitor->attempts) { ?>
                        <form method="POST" action="?result_delete"
                              onsubmit="return confirm('Remove {<?= $competitor->name ?>}?');">
                            <input hidden name="competitor_round" value="<?= $competitor->competitor_round ?>">
                            <button " style="margin:0px;padding:1px 2px;" class="delete">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    <?php } ?>
                </td>
                <?php foreach (range(1, $event->attempts) as $i) { ?>
                    <td class="attempt">
                        <?= $competitor->{"attempt$i"} ?>
                    </td>
                <?php } ?>
                <?php foreach ($formats as $format) { ?>  
                    <td class="attempt" style='font-weight:bold'>
                        <?= $competitor->$format ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php if (sizeof($competitors)) { ?>
    <form method="POST" action="?results_delete"
          onsubmit="return confirm('Remove ALL registrations without results?')">
        <button class="delete">
            <i class="fas fa-trash"></i>
            Remove registrations without results
        </button>
    </form>     
<?php } ?>
<?php if ($event->round == 1) { ?>
    <?php
    $competitors_first = $comp_data->competitors;
    foreach ($competitors_first as $competitor_id => $competitor_first) {
        if ($competitors[$competitor_id] ?? FALSE) {
            unset($competitors_first[$competitor_id]);
        }
    }
    ?>
    </div>
    <div class="shadow2" >
        <form action='?resuts_registration_add' method='POST'>        
            Create new competitor
            <input name='name'>
            <button>
                <i class="fas fa-plus-square"></i>
                Create
            </button>
        </form>
        <?php if (sizeof($competitors_first)) { ?>

            <h2>
                <i class="fas fa-users"></i>
                Competitors
            </h2>
            <form method="POST" action="?resuts_registrations_add_first">
                <table class="table_new">
                    <thead>
                        <tr>
                            <td>Competitor</td>
                            <td>Add</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competitors_first as $competitor_first) { ?>
                            <tr>
                                <td><?= $competitor_first->name ?> </td>
                                <td align="center">
                                    <?php ?>
                                    <input name="competitors[<?= $competitor_first->id ?>]"  type="Checkbox">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <button>
                    <i class="fas fa-user-plus"></i>
                    Add competitors
                </button>
            </form>
        <?php } ?>

    <?php } else { ?>
        <?php
        $competitors_prev = unofficial\getCompetitorsByEventdictRound($comp->id, $event_dict, $round - 1);
        foreach ($competitors_prev as $competitor_id => $competitor_first) {
            if ($competitors[$competitor_id] ?? FALSE) {
                $competitors_prev[$competitor_id]->this_register = TRUE;
                $competitors_prev[$competitor_id]->this_place = $competitors[$competitor_id]->place;
            } else {
                $competitors_prev[$competitor_id]->this_register = FALSE;
                $competitors_prev[$competitor_id]->this_place = FALSE;
            }
        }

        if (sizeof($competitors_prev)) {
            ?>
            <h2>
                <i class="fas fa-users"></i>
                Competitors
            </h2>
            <form method="POST" action="?resuts_registrations_add_next">
                <table class="table_new">
                    <thead>
                        <tr>
                            <td>
                                Place in the preview round
                            </td>
                            <td>
                                Competitor
                            </td>
                            <td>
                                Add
                            </td>
                            <td>
                                Bulk select
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competitors_prev as $competitor) { ?>
                            <tr>
                                <td align="right">
                                    <?= $competitor->place ?> 
                                </td>
                                <td>
                                    <label for='competitors[<?= $competitor->id ?>]'>
                                        <?= $competitor->name ?>
                                    </label>
                                </td>
                                <td align="center">
                                    <?php if ($competitor->this_place) { ?>
                                        <i class="fas fa-user-lock"></i>
                                    <?php } else { ?>
                                        <input hidden name="competitors[<?= $competitor->id ?>]" value='off'>
                                        <input 
                                        <?= $competitor->this_register ? 'checked' : '' ?>
                                            data-competitor-place = '<?= $competitor->place ?>' id ='competitors[<?= $competitor->id ?>]' name="competitors[<?= $competitor->id ?>]"  type="Checkbox">
                                        <?php } ?>
                                </td>
                                <td>
                                    <?php if ($competitor->place >= 1) { ?>
                                        <a href='#' data-competitor-place-select = '<?= $competitor->place ?>'>
                                            up to <?= $competitor->place ?> places
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>    
                <button>
                    <i class="fas fa-user-plus"></i>
                    Add competitors
                </button>
            </form>  
        <?php } ?>
    <?php } ?>
</div>

<script>
<?php include 'competition.event.result.js' ?>
</script>


