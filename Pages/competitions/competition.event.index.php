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
    <?php if (false and($comp->my or $comp->organizer)) { ?>
        <a  class="<?= $section == 'result' ? 'select' : '' ?>" style="padding-left:15px"  href="<?= PageIndex() . "competitions/$secret/result/$event->code/$event->round" ?> ">
            <i class="far fa-keyboard"></i>Enter results
        </a>
    <?php } ?>
    <?php if ($comp->my or $comp->organizer) { ?>
        <i style="padding-left:15px" class="fas fa-list-alt"></i>
        <a  href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=scoketaker" ?> ">Enter results</a>
    <?php } ?>
    <?php if ($comp->my or $comp->organizer) { ?>
        <i style="padding-left:15px" class="fas fa-user-cog"></i>
        <a class="<?= $section == 'event_competitors' ? 'select' : '' ?>" href="<?= PageIndex() . "competitions/$secret/event_competitors/$event->code/$event->round" ?> ">Add/Remove Competitors</a>
    <?php } ?>    
</h2> 
<p>
    <?= $event->comment ? ('&nbsp;<i class="fas fa-comment-dots"></i> Cutoff ' . $event->comment ) : '' ?>
    <?= $event->cutoff ? ('&nbsp;<i class="fas fa-cut"></i> Cutoff ' . $event->cutoff ) : '' ?>
    <?= ($event->time_limit and!$event->cumulative) ? ('&nbsp;<i class="fas fa-stop-circle"></i> Time limit ' . $event->time_limit ) : '' ?>
    <?= ($event->time_limit and $event->cumulative) ? ('&nbsp;<i class="fas fa-plus-circle"></i> Time limit ' . $event->time_limit . ' cumulative' ) : '' ?>
    <?php if (!$event->final and $event->next_round_value) { ?>
        &nbsp;<i class="fas fa-caret-square-right"></i> Top <?= $event->next_round_value . ($event->next_round_procent ? '%' : '') ?> advance next round 
        <?= $next_round_competitors ? " ($next_round_competitors)" : '' ?>
    <?php } ?>
    &nbsp;<i class="fas fa-tv"></i> <a target='blank' href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=projector" ?>">Projector</a>
    &nbsp;<i class="fas fa-print"></i> <a target="_blank" href="?action=result">Print results</a>
    <?php if ($comp->my or $comp->organizer) { ?>
        <?php if (sizeof($comp_data->competitors)) { ?>
            ▪ <a target="_blank" href="?action=cards">Competitior cards</a>
        <?php } ?>     
        ▪ <a target="_blank" href="?action=cards&blank">Blank cards</a>
        ▪ <a target="_blank" href="?action=export">Export results</a>
        <?php if ($event_round_this) { ?>
            ▪ <a target="_blank" href="?action=export&format=txt">TXT results</a>
        <?php } ?>
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
