<link rel="stylesheet" href="<?= PageIndex() ?>Styles/event_check.css?1" type="text/css"/>
<h2>
    <i class="fas fa-user-plus"></i>
    <?= t('Self-registration', 'Самостоятельная регистрация') ?>    
</h2>
<?php
$competitors = unofficial\getCompetitorsSession($comp->id, session_id());
if (sizeof($competitors)) {
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    <?= t('Your registerations', 'Ваши регистрации') ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competitors as $competitor) { ?>
                <tr>
                    <td data-register-select-name data-competitor_list='<?= $competitor->id ?>'><?= $competitor->name ?></td>
                    <td>
                        <?php foreach ($comp_data->competitors[$competitor->id]->events as $event_id => $event) { ?>
                            <font size='5'>
                            <i data-register-event='<?= $event_id ?>' class="<?= $events_dict[$event_id]->image ?>"></i>
                            </font>
                        <?php } ?>
                    </td>
                    <td>
                        <button data-register-select>
                            <i class="fas fa-user-edit"></i>
                            <?= t('Change', 'Изменить') ?>
                        </button>
                    </td>
                    <td>
                        <form method="POST" action="?registration_delete">
                            <input hidden name="competitor" value="<?= $competitor->id ?>">
                            <button class="delete" onclick="return confirm('Delete registration <?= $competitor->name ?>?')">
                                <i class="fas fa-user-times"></i>
                                <?= t('Delete', 'Удалить') ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <hr>
<?php } ?>
<form method="POST" action="?registration_add">
    <input hidden data-competitor name="competitor" value="0">
    <h3>
        1.
        <?= t('Select your events', 'Выберите дисциплины') ?>
    </h3>
    <script>
        var events = [];
    </script>
    <?php foreach ($comp_data->events as $event) { ?>
        <span class='event_icon event_unchecked'>
            <i title="<?= $event->name ?>" 
               class="<?= $events_dict[$event->event_dict]->image ?>">
            </i>
            <font size='6'>
            <?= $event->name ?> 
            </font>   
            <input 
                data-check-event='<?= $event->event_dict ?>'
                hidden 
                name='events[<?= $event->id ?>]' 
                value='off'>
        </span>
        <br>
    <?php } ?>
    <p style="color:green" id='DescriptionDiscipline'></p>
    <h3>2. <?= t('Enter your name and surname', 'Введите ваше имя и фамилию') ?></h3>
    <input data-register-name required="" placeholder="<?= t('Name Surname', 'Имя Фамилия') ?>" style="font-size:24px; width:500px">
    <p style="font-size:24px;">
        <span style="color:green;" data-register-name-parse-ok ></span>
        <span style="color:red;" data-register-name-parse-fail ></span>
        <span style="color:green;" data-register-events ></span>
        &nbsp;
    </p>
    <input hidden name="name" data-register-name-parse>
    <span data-register-button>
        3. 
        <button disabled data-register_add data-register-button-disabled>
            <i class="fas fa-user-plus"></i>
            <?= t('Register', 'Зарегистрироваться') ?>
        </button>
        <button hidden disabled data-register_edit data-register-button-disabled>
            <i class="fas fa-save"></i>
            <?= t('Change', 'Изменить') ?>
        </button>    
        <button hidden class="delete" data-register_cancel>
            <i class="fas fa-backspace"></i>
            <?= t('Cancel', 'Не изменять') ?>
        </button>
    </span>
</form>

<p style='color:var(--red)'>
    <?= postGet('error') ?>
</p>
<script>
    $('[data-check-event]').change(function () {
        console.log($(this).data('check-event'));
        var events = '';
        $('[data-check-event]').each(function () {
            events = events + $(this).closest('tr').find()

        })
        $('[data-register-events]').html('');
        $('[data-register-name]').trigger('input');
    });

    $('[data-register-select]').click(function () {
        $('[data-register-button] button').hide();
        $('[data-register_edit]').show();
        $('[data-register_cancel]').show();
        reset();
        var name = $(this).closest('tr').find('[data-register-select-name]').html();
        var id = $(this).closest('tr').find('[data-competitor_list]').data('competitor_list');
        $('[data-competitor]').val(id);

        $('[data-check-event]').prop('checked', false);
        $(this).closest('tr').find('[data-register-event]').each(function () {
            el_event = $('[data-check-event=' + $(this).data('register-event') + ']')
            el_event.val('on');
            el_event.parent().removeClass('event_unchecked');
            el_event.parent().addClass('event_checked');

        });
        var el_name = $('[data-register-name]');
        el_name.val(name);
        el_name.trigger('input');
        return false;
    });

    $('[data-register_cancel]').click(function () {
        reset();
        $('[data-register-button] button').show();
        $('[data-register_edit]').hide();
        $('[data-register_cancel]').hide();
        return false;
    });

    $('[data-register-name]').on('input', function () {
        check_register();
    });

    function check_name() {
        var name = $("[data-register-name]").val();
        name = name.replace(/[^A-zА-яЁё\- ]/gim, '');
        name = name.replace(/ {1,}/g, " ").trim();
        name = name.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase());
        if (name.indexOf(' ') > -1) {
            $('[data-register-name-parse-ok]').html(name);
            $('[data-register-name-parse]').val(name);
            $('[data-register-name-parse-fail]').html('');
            console.log('check_name: true');
            return true;
        } else {
            $('[data-register-name-parse-ok]').html('');
            $('[data-register-name-parse]').val('');
            $('[data-register-name-parse-fail]').html(name);
            console.log('check_name: false');
            return false;
        }
    }
    ;
    $('.event_icon').click(function () {
        if ($(this).hasClass('event_checked')) {
            $(this).removeClass('event_checked');
            $(this).addClass('event_unchecked_new');
            $(this).find('input').val('off');
        } else if ($(this).hasClass('event_unchecked')) {
            $(this).removeClass('event_unchecked');
            $(this).addClass('event_checked_new');
            $(this).find('input').val('on');
        } else if ($(this).hasClass('event_checked_new')) {
            $(this).removeClass('event_checked_new');
            $(this).addClass('event_unchecked');
            $(this).find('input').val('off');
        } else if ($(this).hasClass('event_unchecked_new')) {
            $(this).removeClass('event_unchecked_new');
            $(this).addClass('event_checked');
            $(this).find('input').val('on');
        }
        check_register();
    });

    function check_register() {
        check_register_button('register_add');
        check_register_button('register_edit');
    }

    function check_register_button(data) {
        var button = $('[data-' + data + ']');
        if (button.is(':hidden')) {
            return;
        }
        if (!check_name()) {
            button.prop("disabled", true);
            return;
        }
        var event = 0;
        $('input').each(function () {
            if ($(this).val() == 'on') {
                event = event + 1;
            }
        });
        if (event == 0) {
            button.prop("disabled", true);
            return;
        }
        button.prop("disabled", false);
    }

    function reset() {
        $('[data-register-name]').val('');
        $('[data-register-name-parse-ok]').html('');
        $('[data-register-name-parse]').val('');
        $('[data-register-name-parse-fail]').html('');
        $('[data-check-event]').val('off');
        event = $('[data-check-event]').prev('span');
        event.removeClass('event_checked');
        event.removeClass('event_unchecked_new');
        event.removeClass('event_checked_new');
        event.addClass('event_unchecked');
        console.log('reset');
    }
    reset();
</script>