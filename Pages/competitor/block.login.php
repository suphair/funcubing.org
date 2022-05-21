<?php
wcaoauth::set(
        config :: get('WCA_OAUTH', 'client_id')
        , config :: get('WCA_OAUTH', 'client_secret')
        , config :: get('WCA_OAUTH', 'scope')
        , PageIndex() . config :: get('WCA_OAUTH', 'url_refer')
        , db::connection()
);

$url = wcaoauth::url();
?>
<a href="<?= $url ?>">
    <i class="fas fa-sign-in-alt"></i>
    <?= t('Sign in with WCA', 'Авторизоваться на WCA') ?>
</a>
<br>
<?php $lang = $_SESSION['lang'] ?? false ?>
<?php if ($lang == 'RU') { ?>
    <i class="flag-icon flag-icon-ru"></i> <b>Ру</b> | <a href="<?= PageIndex() ?>competitor/?action=language&lang=EN">En</a>
<?php } else { ?>
    <a href="<?= PageIndex() ?>competitor/?action=language&lang=RU">Ру</a> | <b>En</b> <i class="flag-icon flag-icon-gb"></i>
<?php } ?>