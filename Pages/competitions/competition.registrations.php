<link rel="stylesheet" href="<?= PageIndex() ?>Styles/event_check.css?1" type="text/css"/>
<?php include 'competition.setting.menu.php' ?>
<h2> 
    <i class="far fa-list-alt"></i>
    <?= t('Add registrations', 'Добавить регистрации') ?>
</h2>
&bull; Enter competitors separated by a newline or comma:<br>
<b>[Competitor Alfa<br>Competitor Beta] &#8658; Competitor Alfa & Competitor Beta</b><br>
<b>[Competitor Delta, Competitor Gamma] &#8658; Competitor Delta & Competitor Gamma</b><br>
&bull; You can enter the name in any case the first letters will become large 
<b>[comPETitor epSiLon] &#8658; Competitor Epsilon</b><br>
&bull; When you create a competitor, you can register it for the event by adding a tag 
(also you can click on the event on the right)<br>
<b>[Competitor Dseta 222 333] &#8658; Competitor Dseta (2x2x2 Cube  & 3x3x3 Cube)</b><br>
<br>
&bull; You can register a competitor in the events later;
<br>
&bull; You can add registrations in several parts;
<?php if($comp->ranked){ ?>
<br><br>
<p>Для добавления участника с тем же именем добавьте "*". <br><b>[Competitor Beta<br>Competitor Beta *<br>Competitor Beta *] &#8658; Competitor Beta & Competitor Beta CB02 & Competitor Beta CB03</b><br> </p>
<?php } ?>
<form method="POST" action="?competitors_add">
    <table width='100%'>
        <tr class="no_border">
            <td width='50%'>    
                <textarea  name="competitors" style="width: 400px; height: 350px; font-size:20px;"></textarea>
            </td>
            <td width='50%' valign="top">
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
        <?= t('Add', 'Добавить') ?>
    </button>    
</form>

<?php if (sizeof($comp_data->competitors)) { ?>
    <hr>
    <h2>
        <i class="fas fa-users-cog"></i>
        <?= t('Registrations', 'Регистрации') ?>
    </h2>
    <form action='?competitors_edit'  method='POST'>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <?php if ($comp->ranked) { ?>
                        <th><i class="flag-icon flag-icon-ru"></i></th>
                    <?php } ?>
                    <th style='color:var(--red)'>
                        <i class="fas fa-user-slash"></i>
                    </th>
                    <th>
                        <?= t('Competitor', 'Участник') ?>
                    </th>
                    <?php foreach ($comp_data->events as $event) { ?>
                        <th style="text-align:center">
                            <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>    
                        </th>
                    <?php } ?>
                    <?php if ($comp->ranked) { ?>
                        <th>FC ID</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comp_data->competitors as $competitor) { ?>
                    <tr data-registration>                        
                        <td data-num></td>
                        <?php if ($comp->ranked) { ?>
                            <td>
                                <i data-non_resident class="<?= $competitor->non_resident ? 'non_resident' : '' ?> flag-icon flag-icon-ru"></i>
                                <input hidden name="registrations[<?= $competitor->id ?>][non_resident]" value="<?= $competitor->non_resident ? 'off' : 'on' ?>">
                            </td>
                        <?php } ?>
                        <td>
                            <?php if ($competitor->delete) { ?>
                                <input data-competitor-delete style='background-color: red' name='registrations_delete[<?= $competitor->id ?>]' type='checkbox'>
                            <?php } else { ?>
                                <i class="fas fa-lock"></i>
                            <?php } ?>    
                        </td>
                        <td>
                            <input style="width:220px" name='registrations[<?= $competitor->id ?>][name]' value='<?= $competitor->name_original ?>' ?>
                        </td>
                        <?php foreach ($comp_data->events as $event) { ?>
                            <td style="text-align:center">
                                <?php if ($competitor->events[$event->event_dict]->result ?? FALSE) { ?>
                                    <i class="fas fa-check"></i>
                                    <?php
                                } else {
                                    $checked = $competitor->events[$event->event_dict] ?? FALSE;
                                    ?>
                                    <i data-competitor-hide title="<?= $event->name ?>" class="event_icon <?= $checked ? 'event_checked' : 'event_unchecked' ?> <?= $events_dict[$event->event_dict]->image ?>"></i>    
                                    <input hidden name='registrations[<?= $competitor->id ?>][<?= $event->event_dict ?>]' value="<?= $checked ? 'on' : 'off' ?>">
                                <?php } ?>
                            </td>    
                        <?php } ?>
                        <?php if ($comp->ranked) { ?>
                            <td>
                                <?= $competitor->FCID ? $competitor->FCID : '-' ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="<?= (sizeof($comp_data->competitors) % 2 == 1) ? 'gray' : '' ?>">
                    <td colspan="<?= $comp->ranked ? 3 : 2 ?>"></td>
                    <td><?= t('Total', 'Всего') ?></td>
                    <?php foreach ($comp_data->events as $event) { ?>
                        <td style="text-align:center">
                            <?= sizeof($comp_data->rounds[$event->event_dict][1]->competitors ?? []) ?>
                        </td>
                    <?php } ?>
                </tr>
            </tfoot>
        </table>
        <br>
        <button name="button" value="registrations"> 
            <i class="far fa-save"></i>
            <?= t('Save', 'Сохранить') ?>
        </button>
    </form>
    <?php if (!$comp->ranked) { ?>
        <form action="?competitors_delete" method="POST"
              onsubmit="return confirm('<?= t('Delete all competitors without results', 'Удалить всех участников без результатов') ?>')">   
            <div align='right'>
                <button class="delete"> 
                    <i class="fas fa-trash"></i>
                    <?= t(' Delete all competitors without results', 'Удалить всех участников без результатов') ?>
                </button>
            </div>
        </form>
    <?php } ?>
<?php } ?>
<?php if (!$comp->ranked) { ?>
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
        <hr>
        <h2>
            <i class="fas fa-users"></i>
            <?= t('Competitors of your competitions', 'Участники с ваших соревнований') ?>
        </h2>
        <form action="?competitors_select" method="post">
            <table class="table_new">
                <thead>
                    <tr>
                        <td></td>
                        <td><?= t('Competitor', 'Участник') ?></td>
                        <td><?= t('Competitions', 'Cоревнований') ?></td>
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
                <?= t('Add selected competitors', 'Добавить выбранных участников') ?>
            </button>    
        </form>
    <?php } ?>
<?php } ?>
<script>
    var num = 1;
    $('[data-registration] [data-num]').each(function () {
        $(this).html(num);
        num = num + 1;
    });
    $('[data-competitor-delete]').change(function () {
        var events = $(this).closest('tr').find('[data-competitor-hide]');
        if ($(this).prop("checked")) {
            events.hide('');
        } else {
            events.show('');
        }
    });</script>

<script>
    $('.event_icon').click(function () {
        if ($(this).hasClass('event_checked')) {
            $(this).removeClass('event_checked');
            $(this).addClass('event_unchecked_new');
            $(this).next('input').val('off');
        } else if ($(this).hasClass('event_unchecked')) {
            $(this).removeClass('event_unchecked');
            $(this).addClass('event_checked_new');
            $(this).next('input').val('on');
        } else if ($(this).hasClass('event_checked_new')) {
            $(this).removeClass('event_checked_new');
            $(this).addClass('event_unchecked');
            $(this).next('input').val('off');
        } else if ($(this).hasClass('event_unchecked_new')) {
            $(this).removeClass('event_unchecked_new');
            $(this).addClass('event_checked');
            $(this).next('input').val('on');
        }
    });


    $('[data-non_resident]').click(function () {
        if ($(this).hasClass('non_resident')) {
            $(this).removeClass('non_resident');
            $(this).next('input').val('on');
        } else {
            $(this).addClass('non_resident');
            $(this).next('input').val('off');
        }
    })
</script> 