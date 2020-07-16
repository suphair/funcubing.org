<?php $en = !(filter_input(INPUT_GET, 'language') == 'ru'); ?>

<h2>
    <i class="fas fa-user-plus"></i>
    <?= $en ? 'Self-registration' : 'Самостоятельная регистрация' ?>    
</h2>
<p>
    <input hidden data-language type='radio' name='language' <?= $en ? 'checked' : '' ?> id='languge_en' value='en'>
    <label for='languge_en'>
        <span class='flag-icon flag-icon-gb'></span>
        English
    </label>
    <input hidden data-language type='radio' name='language' <?= !$en ? 'checked' : '' ?> id='languge_ru' value='ru'>
    <label for='languge_ru'>
        <span class='flag-icon flag-icon-ru'></span>
        Русский
    </label>
</p>
<div class="shadow2">
    <form method="POST" action="?registration_add" data-rgistrtion-add>
        <h3>
            <i class="fas fa-clipboard"></i>
            <?= $en ? 'Select your events' : 'Выберите дисциплины' ?>
        </h3>
        <script>
            var events = [];
        </script>
        <table class="table_new">
            <thead>
            </thead>
            <tbody>
                <?php foreach ($comp_data->events as $event) { ?>
                    <tr>
                        <td>
                            <label for="<?= $event->id ?>">
                                <i  style="cursor:pointer" class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                            </label>
                        </td>
                        <td>
                            <label data-check-event-name for="<?= $event->id ?>">
                                <?= $event->name ?>
                            </label>
                        </td>
                        <td>
                            <input hidden name='events[<?= $event->id ?>]' value='off'>
                            <input data-check-event='<?= $event->event_dict ?>'
                                   name='events[<?= $event->id ?>]' id="<?= $event->id ?>" type='checkbox'>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p style="color:var(--green)" id='DescriptionDiscipline'></p>
        <h3><?= $en ? 'Enter your name and surname' : 'Введите ваше имя и фамилию' ?></h3>
        <input data-register-name required="" placeholder="<?= $en ? 'Name Surname' : 'Имя Фамилия' ?>" style="font-size:24px; width:500px">
        <p style="font-size:24px;">
            <span style="color:var(--green);" data-register-name-parse-ok ></span>
            <span style="color:var(--red);" data-register-name-parse-fail ></span>
            <span style="color:var(--green);" data-register-events ></span>
            &nbsp;
        </p>
        <input hidden name="name" data-register-name-parse>
        <span data-register-button>
            <button disabled data-register_add data-register-button-disabled>
                <i class="fas fa-user-plus"></i>
                <?= $en ? 'Register' : 'Зарегистрироваться' ?>
            </button>
            <button hidden disabled data-register_edit data-register-button-disabled>
                <i class="fas fa-save"></i>
                <?= $en ? 'Change' : 'Изменить' ?>
            </button>    
            <button hidden class="delete" data-register_cancel>
                <i class="fas fa-backspace"></i>
                <?= $en ? 'Cancel' : 'Не изменять' ?>
            </button>
        </span>
    </form>

</div>
<p style='color:var(--red)'>
    <?= postGet('error') ?>
</p>
<?php
$competitors = unofficial\getCompetitorsSession($comp->id, session_id());
if (sizeof($competitors)) {
    ?>
    <div class="shadow2">
        <h3>
            <i style='color:var(--green)' class="fas fa-clipboard-check"></i>
            <?= $en ? 'You have registered' : 'Вы зарегистрировались' ?>
        </h3>
        <table class="table_new">
            <tbody>
                <?php foreach ($competitors as $competitor) { ?>
                    <tr>
                        <td data-register-select-name><?= $competitor->name ?></td>
                        <td>
                            <button data-register-select>
                                <i class="fas fa-user-edit"></i>
                                <?= $en ? 'Change' : 'Изменить' ?>
                            </button>
                        </td>
                        <td>
                            <form method="POST" action="?registration_delete">
                                <input hidden name="competitor" value="<?= $competitor->id ?>">
                                <button class="delete" onclick="return confirm('Delete registration <?= $competitor->name ?>?')">
                                    <i class="fas fa-user-times"></i>
                                    <?= $en ? 'Delete' : 'Удалить' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <?php foreach ($comp_data->competitors[$competitor->id]->events as $event_id => $event) { ?>
                                <font size='4'>
                                <i data-register-event='<?= $event_id ?>' class="<?= $events_dict[$event_id]->image ?>"></i>
                                </font>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('[data-check-event]').change(function () {
        var events = '';
        $('[data-check-event]').each(function () {
            events = events + $(this).closest('tr').find()

        })
        $('[data-register-events]').html('');
        $('[data-register-name]').trigger('input');
    });

    $('[data-language]').change(function () {
        if ($(this).prop("checked")) {
            document.location = '?language=' + $(this).val();
        }
    });
    $('[data-language]').each(function () {
        if ($(this).prop("checked")) {
            var label = $('label[for=' + $(this).attr('id') + ']');
            label.css('color', 'var(--red)');
            label.css('font-weight', 'bold');
        }
    });


    $('[data-register-select]').click(function () {
        var name = $(this).closest('tr').find('[data-register-select-name]').html();

        $('[data-register-button] button').hide();
        $('[data-register_edit]').show();
        $('[data-register_cancel]').show();

        $('[data-check-event]').prop('checked', false);
        $(this).closest('tr').find('[data-register-event]').each(function () {
            $('[data-check-event=' + $(this).data('register-event') + ']').prop('checked', true);
        });
        var el_name = $('[data-register-name]');
        el_name.val(name);
        el_name.trigger('input');
        return false;
    });

    $('[data-register_cancel]').click(function () {
        $('[data-check-event]').prop('checked', false);
        $('[data-register-button] button').hide();
        $('[data-register_add]').show();
        var el_name = $('[data-register-name]');
        el_name.val('');
        el_name.trigger('input');
        $('[data-register-button-disabled]').prop("disabled", true);
        return false;
    });

    $('[data-register-name]').on('input', function () {
        var name = $("[data-register-name]").val();
        name = name.replace(/[^A-zА-яЁё\- ]/gim, '');
        name = name.replace(/ {1,}/g, " ").trim();
        name = name.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase());
        if (name.indexOf(' ') > -1) {
            $('[data-register-name-parse-ok]').html(name);
            $('[data-register-name-parse]').val(name);
            $('[data-register-name-parse-fail]').html('');
            if ($('[data-check-event]:checked').length) {
                $('[data-register-button-disabled]').prop("disabled", false);
            } else {
                $('[data-register-button-disabled]').prop("disabled", true);
            }
        } else {
            $('[data-register-name-parse-ok]').html('');
            $('[data-register-name-parse]').val('');
            $('[data-register-name-parse-fail]').html(name);
            $('[data-register-button-disabled]').prop("disabled", true);
        }
    });
</script>