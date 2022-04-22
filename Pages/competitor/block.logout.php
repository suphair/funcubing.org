
<?= trim(explode('(', $me->name)[0]) ?>

<a href="<?= PageIndex() ?>competitor/?action=logout">
    <i class="fas fa-sign-out-alt"></i>
    Sign out
</a>
<br>
<?php $lang = $_SESSION['lang'] ?>
<?php if ($lang == 'RU') { ?>
    <i class="flag-icon flag-icon-ru"></i> <b>Ру</b> | <a href="<?= PageIndex() ?>competitor/?action=language&lang=EN">En</a>
<?php } else { ?>
    <a href="<?= PageIndex() ?>competitor/?action=language&lang=RU">Ру</a> | <b>En</b> <i class="flag-icon flag-icon-gb"></i>
<?php } ?>
