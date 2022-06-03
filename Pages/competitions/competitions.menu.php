<?php
$show = filter_input(INPUT_GET, 'show');
$mine = $me ? $show == 'mine' : false;
$ranked = $show == 'ranked';
$aviable_hidden = (unofficial\admin() or unofficial\federation());
$show_hidden = ($show == 'hidden' and $aviable_hidden);
$create = request(1) == 'create';
$all = $show == 'all';
$ranked = !($mine or $all or $create or $show_hidden);
$url_base = PageIndex() . "competitions";
?>
<div class="menu">
    <a href="<?= $url_base ?>?show=ranked" class="<?= $ranked ? 'select' : '' ?>">
        <?= t("Speedcubing Federation", 'Федерация Спидкубинга') ?>
    </a>
    <?php if ($me) { ?>
        <a href="<?= $url_base ?>?show=mine" class="<?= $mine ? 'select' : '' ?>">
            <?= t('My competition', 'Мои соревнования') ?>
        </a>
    <?php } ?>
    <a href="<?= $url_base ?>?show=all" class="<?= $all ? 'select' : '' ?>">
        <?= t('All', 'Все') ?>
    </a>
    <?php if ($aviable_hidden) { ?>
        <a href="<?= $url_base ?>?show=hidden" class="<?= $show_hidden ? 'select' : '' ?>">
            <?= t('Hidden', 'Скрытые'); ?>
        </a>
    <?php } ?>
    <span class="separator"></span>

    <a href="<?= $url_base ?>/create"  class="<?= $create ? 'select' : '' ?>">
        <i class="fas fa-calendar-plus"></i> <?= t("Create", 'Создать') ?>
    </a>

    <a href="<?= PageIndex() ?>competitions/rankings"> 
        <?= $ranked_icon ?> <?= t('Rankings ', 'Рейтинг') ?>
    </a>
</div>