<div class="shadow2">
    <h2> 
        <i class="far fa-list-alt"></i>
        Add competitors
    </h2>
    <form method="POST" action="?competitors_add">
        <table width='100%'>
            <tr class="no_border">
                <td width='50%'>    
                    <textarea  name="competitors" style="width: 400px; height: 350px; font-size:20px;"></textarea>
                </td>
                <td width='50%' valign="top">
                    <h3>
                        <span style="border-bottom:1px dotted rgb(0,182,67); cursor: help; color:rgb(0,182,67);" 
                              data-tooltip="
                              <font style='color:rgb(0,182,67)'>▪</font> Enter competitors separated by a <b>newline</b> or <b>comma</b><font style='color:rgb(0,182,67)'>:</font><br>
                              [Competitor Alfa<br>Competitor Beta] <font style='color:rgb(0,182,67)'>&#8658;</font> <b>1)</b> Competitor Alfa <b>2)</b> Competitor Beta<br>
                              [Competitor Delta, Competitor Gamma] <font style='color:rgb(0,182,67)'>&#8658;</font> <b>1)</b> Competitor Delta <b>2)</b> Competitor Gamma<br>
                              <font style='color:rgb(0,182,67)'>▪</font> You can enter the name in any case the first letters will become large<br>
                              [comPETitor epSiLon] <font style='color:rgb(0,182,67)'>&#8658;</font> Competitor Epsilon<br>
                              <font style='color:rgb(0,182,67)'>▪</font> When you create a competitor, you can register it for the discipline by adding a tag<br>
                              (also you can click on the discipline on the right)<br>
                              [Competitor Dseta 222 333] <font style='color:rgb(0,182,67)'>&#8658;</font> Competitor Dseta +2x2x2 Cube +3x3x3 Cube<br>
                              <br>
                              <font style='color:rgb(0,182,67)'>▪</font> You can register a competitor in the disciplines later
                              <br>
                              <font style='color:rgb(0,182,67)'>▪</font> You can add competitors in several parts
                              ">Instruction</span>
                    </h3>
                    <br>
                    <?php foreach ($comp_data->events as $event) { ?>
                        <p class="registration_event" onclick="
                                var el = $('form textarea[name=competitors]');
                                var s = el[0].selectionStart;
                                var val = el.val();
                                var val_s = val.substring(0, s);
                                var val_e = val.substring(s);
                                var code = ' <?= $events_dict[$event->event_dict]->code ?> ';
                                el.val(val_s + code + val_e);
                                el.focus();
                                el[0].setSelectionRange(s + code.length, s + code.length);
                           ">
                            <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                            [<b><?= $events_dict[$event->event_dict]->code ?></b>]
                            <?= $event->name ?>
                        </p> 
                    <?php } ?>
                </td>
            </tr>
        </table>
        <button>
            <i class="fas fa-user-plus"></i>
            Add competitors and registration
        </button>    
    </form>
</div>
<?php if (sizeof($comp_data->competitors)) { ?>
    <div class="shadow2">
        <h2>
            <i class="fas fa-users-cog"></i>
            Registrations
        </h2>
        <form action='?competitors_edit'  method='POST'>
            <table class="table_new">
                <thead>
                    <tr>
                        <td>#</td>
                        <td style='color:var(--red)'>
                            <i class="fas fa-user-slash"></i>
                        </td>
                        <td>Competitor</td>
                        <?php foreach ($comp_data->events as $event) { ?>
                            <td>
                                <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>    
                            </td>
                        <?php } ?>                           
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comp_data->competitors as $competitor) { ?>
                        <tr data-registration>                        
                            <td data-num></td>
                            <td>
                                <?php if ($competitor->delete) { ?>
                                    <input data-competitor-delete style='background-color: red' name='registrations_delete[<?= $competitor->id ?>]' type='checkbox'>
                                <?php } else { ?>
                                    <i class="fas fa-lock"></i>
                                <?php } ?>    
                            </td>
                            <td data-competitor-name>
                                <input name='registrations[<?= $competitor->id ?>][name]' value='<?= $competitor->name ?>' ?>
                                <?= $competitor->name ?>
                            </td>
                            <?php foreach ($comp_data->events as $event) { ?>
                                <td data-competitor-event>
                                    <?php if ($competitor->events[$event->event_dict] ?? FALSE) { ?>
                                        <?php if ($competitor->events[$event->event_dict]->result) { ?>
                                            <i class="fas fa-check"></i>
                                        <?php } else { ?>
                                            <span class='checked'>
                                                <input hidden name='registrations[<?= $competitor->id ?>][<?= $event->event_dict ?>]' value='off'>
                                                <input name='registrations[<?= $competitor->id ?>][<?= $event->event_dict ?>]' checked type='checkbox'>
                                            </span>
                                        <?php } ?>
                                    <?php } else { ?>    
                                        <input name='registrations[<?= $competitor->id ?>][<?= $event->event_dict ?>]' type='checkbox'>
                                    <?php } ?>    
                                </td>    
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td/><td/><td/>
                        <?php foreach ($comp_data->events as $event) { ?>
                            <td>
                                <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>    
                            </td>
                        <?php } ?>    
                    </tr> 
                    <tr>
                        <td/><td/>
                        <td>Total</td>
                        <?php foreach ($comp_data->events as $event) { ?>
                            <td>
                                <?= sizeof($comp_data->rounds[$event->event_dict][1]->competitors ?? []) ?>
                            </td>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
            <button> 
                <i class="far fa-save"></i>
                Save registrations
            </button>

        </form>
        <form action="?competitors_delete" method="POST"
              onsubmit="return confirm('Delete all competitors and registrations without results?')">   
            <div align='right'>
                <button class="delete"> 
                    <i class="fas fa-trash"></i>
                    Delete all competitors and registrations without results
                </button>
            </div>
        </form>
    </div>
<?php } ?>
<?php
$competitors = db::rows(""
                . " SELECT "
                . " CASE WHEN unofficial_competitors.id IS NOT NULL THEN true ELSE false END this,"
                . " competitors.name,"
                . "competitors.count "
                . " FROM"
                . " (SELECT "
                . " unofficial_competitors.name, count(*) count"
                . " FROM unofficial_competitors"
                . " JOIN unofficial_competitions on unofficial_competitions.id = unofficial_competitors.competition "
                . " WHERE unofficial_competitions.competitor = $comp->competitor"
                . " GROUP BY unofficial_competitors.name) competitors"
                . " LEFT JOIN  unofficial_competitors on unofficial_competitors.competition = $comp->id "
                . "AND competitors.name = unofficial_competitors.name"
                . " ORDER BY competitors.name");
if (sizeof($competitors)) {
    ?>
    <div class="shadow2">
        <h2>
            <i class="fas fa-users"></i>
            Competitors of your competitions
        </h2>
        <form action="?competitors_select" method="post">
            <table class="table_new">
                <thead>
                    <tr>
                        <td></td>
                        <td>Competitor</td>
                        <td>Competitions</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competitors as $competitor) { ?>
                        <tr >
                            <td align='center'>
                                <?php if ($competitor->this) { ?>
                                    <i class="far fa-check-square"></i>
                                <?php } else { ?>
                                    <input name='competitors[]' value='<?= $competitor->name ?>' type='checkbox'>
                                <?php } ?>    
                            </td>
                            <td>
                                <?= $competitor->name ?>
                            </td>
                            <td align='center'>
                                <?= $competitor->count ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button>
                <i class="fas fa-user-plus"></i>
                Add selected competitors
            </button>    
        </form>
    </div>
<?php } ?>
<script>
    var num = 1;
    $('[data-registration] [data-num]').each(function () {
        $(this).html(num);
        num = num + 1;
    });
    $('[data-competitor-delete]').change(function () {
        var name = $(this).closest('tr').find('[data-competitor-name]');
        var events = $(this).closest('tr').find('[data-competitor-event]');
        if ($(this).prop("checked")) {
            name.css('text-decoration', 'line-through');
            name.css('color', 'var(--red)');
            events.hide('slow');
        } else {
            name.css('text-decoration', 'none');
            name.css('color', 'var(--black)');
            events.show('slow');
        }
    });
</script>