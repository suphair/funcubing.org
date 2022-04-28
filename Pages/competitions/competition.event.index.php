<h2>
    <?php if ($section != 'event') { ?>
        <a  href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round" ?> ">
        <?php } ?>
        <i class="<?= $event->image ?>"></i>
        <?= $event->name ?>,
        <?= $rounds_dict[$event->final ? 0 : $event->round]->fullName; ?>    
        <?php if ($section != 'event') { ?>
        </a>
    <?php } ?>
    <?php if ($comp->my or $comp->organizer) { ?>
        <i style="padding-left:15px" class="fas fa-list-alt"></i>
        <a  href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=scoketaker" ?> "><?= t('Enter results', 'Ввод результатов') ?></a>
    <?php } ?>
    <?php if ($comp->my or $comp->organizer) { ?>
        <i style="padding-left:15px" class="fas fa-user-cog"></i>
        <a class="<?= $section == 'event_competitors' ? 'select' : '' ?>" href="<?= PageIndex() . "competitions/$secret/event_competitors/$event->code/$event->round" ?> "><?= t('Add/Remove Competitors', 'Добавление/Удаление участников') ?></a>
    <?php } ?>    
</h2> 
<p>
    <?= $event->comment ? ('&nbsp;<i class="fas fa-comment-dots"></i> ' . $event->comment ) : '' ?>
    <?= $event->cutoff ? ('&nbsp;<i class="fas fa-cut"></i> ' . t('Cutoff', 'Катофф') . ' ' . $event->cutoff ) : '' ?>
    <?= ($event->time_limit and!$event->cumulative) ? ('&nbsp;<i class="fas fa-stop-circle"></i> ' . t('Time limit', 'Лимит по времени') . ' ' . $event->time_limit ) : '' ?>
    <?= ($event->time_limit and $event->cumulative) ? ('&nbsp;<i class="fas fa-plus-circle"></i> ' . t('Time limit', 'Лимит по времени') . ' ' . $event->time_limit . ' ' . t('in total', 'суммарно') ) : '' ?>
    <?php if (!$event->final and $event->next_round_value) { ?>
        &nbsp;<i class="fas fa-caret-square-right"></i> <?= t('Top', 'Лучшие') ?> <?= $event->next_round_value . ($event->next_round_procent ? '%' : '') ?> <?= t('advance next round', 'проходят дальше') ?> 
        <?= $next_round_competitors ? " ($next_round_competitors)" : '' ?>
        <?php if ($event->next_round_value > $next_round_competitors and!$event->next_round_procent) { ?>
            <?= t('no more than 75%', 'не более 75%') ?>
        <?php }
        ?>
    <?php } ?>
    <?php if ($comp->ranked) { ?>
        <?= $ranked_icon ?>
        <a href="<?= PageIndex() ?>competitions/rankings/<?= $event->code ?>">
            <?= t('Event rankings', 'Рейтинг дисциплины') ?>
        </a>
    <?php } ?>
</p>
<br>
<p>
    <i class="fas fa-tv"></i> <a target='blank' href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=projector" ?>"><?= t('Projector', 'Проектор') ?></a>
    &nbsp;<i class="fas fa-print"></i> <a target="_blank" href="?action=result"><?= t('Print results', 'Печать результатов') ?></a>
    <?php if ($comp->my or $comp->organizer) { ?>
        <?php if (sizeof($comp_data->competitors)) { ?>
            ▪ <a target="_blank" href="?action=cards"><?= t('Competitior cards', 'Карточки участников') ?></a>
        <?php } ?>     
        ▪ <a target="_blank" href="?action=cards&blank"><?= t('Blank cards', 'Пустые карточки') ?></a>
        ▪ <a target="_blank" href="?action=export&format=txt"><?= t('TXT results', 'Результаты в TXT') ?></a>
    <?php } ?>
</p>
<?php
if ($section == 'event_competitors') {
    include 'competition.event.competitors.php';
} elseif ($section == 'result') {
    include 'competition.event.result.php';
} else {
    include 'competition.event.php';
}
?>
