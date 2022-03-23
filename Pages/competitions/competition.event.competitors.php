<?php include 'competition.event.php';?>
<?php if ($event->round == 1) { ?>
    <?php
    $competitors_first = $comp_data->competitors;
    foreach ($competitors_first as $competitor_id => $competitor_first) {
        if ($competitors[$competitor_id] ?? FALSE) {
            unset($competitors_first[$competitor_id]);
        }
    }
    ?>
    <form action='?resuts_registration_add' method='POST'>        
        Create new competitor
        <input name='name'>
        <button>
            <i class="fas fa-plus-square"></i>
            Create
        </button>
    </form>
    <?php if (sizeof($competitors_first)) { ?>

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