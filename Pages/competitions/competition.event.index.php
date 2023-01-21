<div class="menu">
    <span><i class="<?= $event->image ?>"></i>
        <?= $event->name ?></span>
    <?php foreach (range(1, $event->rounds) as $round) { ?>
        <a  href="<?= PageIndex() . "competitions/$secret/event/$event->code/$round" ?> "
            class="<?= $round == $event->round ? 'select' : '' ?>">
            <?= $rounds_dict[$round == $event->rounds ? 0 : $round]->fullName; ?></a>
    <?php } ?>
</div> 
<?php if ($grand->edit) { ?>
    <h2>
        <a style="padding-left:15px" class="<?= $section == 'event_competitors' ? 'select' : '' ?>" href="<?= PageIndex() . "competitions/$secret/event_competitors/$event->code/$event->round" ?> ">
            <i class="fas fa-user-cog"></i>
            <?= t('Competitors', 'Участники') ?>
        </a>
        <a style="padding-left:15px" target="_blank" href="?action=cards">
            <i class="fas fa-print"></i>
            <?= t('Scorecards', 'Карточки') ?>
        </a>
        <a style="padding-left:15px" href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=scoketaker" ?> ">
            <i class="fas fa-list-alt"></i>
            <?= t('Enter results', 'Ввод результатов') ?>
        </a>
    </h2>
<?php } ?>
<p>
    <?= $event->comment ? ('&nbsp;<i class="fas fa-comment-dots"></i> ' . $event->comment ) : '' ?>
    <?= $event->cutoff ? ('&nbsp;<i class="fas fa-cut"></i> ' . t('Cutoff', 'Катофф') . ' ' . $event->cutoff ) : '' ?>
    <?= ($event->time_limit) ? ('&nbsp;<i class="fas fa-stop-circle"></i> ' . t('Time limit', 'Лимит по времени') . ' ' . $event->time_limit ) : '' ?>
    <?= ($event->time_limit_cumulative) ? ('&nbsp;<i class="fas fa-plus-circle"></i> ' . t('Time limit', 'Лимит по времени') . ' ' . $event->time_limit_cumulative . ' ' . t('in total', 'суммарно') ) : '' ?>
    <?php if (!$event->final and $event->next_round_value) { ?>
        &nbsp;<i class="fas fa-caret-square-right"></i> <?= t('Top', 'Лучшие') ?> <?= $event->next_round_value . ($event->next_round_procent ? '%' : '') ?> <?= t('advance next round', 'проходят дальше') ?> 
        <?= $next_round_competitors ? ("(" . t('selected', 'отобрано') . " $next_round_competitors)" ) : '' ?>
        <?php if ($event->next_round_value > $next_round_competitors and!$event->next_round_procent) { ?>
            <?= t('no more than 75%', 'не более 75%') ?>
        <?php }
        ?>
    <?php } ?>
</p>
<br>

<?php
if ($section == 'event_competitors') {
    include 'competition.event.competitors.php';
} elseif ($section == 'result') {
    include 'competition.event.result.php';
} else {
    include 'competition.event.php';
}
?>
