<?php $setting_select = request(2); ?>
<h2>
    <i class="fas fa-cog"></i>
    <?php if ($comp->my) { ?>
        <a href='<?= $competition->url ?>/setting' class='<?= $setting_select == 'setting' ? 'select' : '' ?>'
           ><?= t('Setting', 'Настройки') ?></a>
        |
        <a href='<?= $competition->url ?>/setting_sheets' class='<?= $setting_select == 'setting_sheets' ? 'select' : '' ?>'
           ><?= t('Sheets', 'Вкладки') ?></a>
        |
        <a href='<?= $competition->url ?>/setting_events' class='<?= $setting_select == 'setting_events' ? 'select' : '' ?>'
           ><?= t('Events', 'Дисциплины') ?></a>
        |
    <?php } ?>
    <a href='<?= $competition->url ?>/registrations' class='<?= $setting_select == 'registrations' ? 'select' : '' ?>'
       ><?= t('Registrations', 'Регистрации') ?></a>
</h2>