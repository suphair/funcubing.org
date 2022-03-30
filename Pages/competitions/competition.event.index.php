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
            <i class="far fa-keyboard"></i> Enter results
        </a>
    <?php } ?>
    <?php if ($comp->my or $comp->organizer) { ?>
        <a style="padding-left:15px"  href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=scoketaker" ?> ">
            <i class="fas fa-list-alt"></i> Enter results
        </a>
    <?php } ?>
    <?php if ($comp->my or $comp->organizer) { ?>
        <a class="<?= $section == 'event_competitors' ? 'select' : '' ?>" style="padding-left:15px" href="<?= PageIndex() . "competitions/$secret/event_competitors/$event->code/$event->round" ?> ">
            <i class="fas fa-user-cog"></i> Add/Remove Competitors
        </a>
    <?php } ?>    
</h2> 
<p>
    <?= $event->comment ?>
    <?= $event->cutoff ? ('<i class="fas fa-cut"></i> Cutoff ' . $event->cutoff ) : '' ?>
    <?= ($event->time_limit and!$event->cumulative) ? ('<i class="fas fa-stop-circle"></i> Time limit ' . $event->time_limit ) : '' ?>
    <?= ($event->time_limit and $event->cumulative) ? ('<i class="fas fa-plus-circle"></i> Time limit ' . $event->time_limit . ' cumulative' ) : '' ?>
    &nbsp;<a target='blank' href="<?= PageIndex() . "competitions/$secret/event/$event->code/$event->round?action=projector" ?>"><i class="fas fa-tv"></i> Projector </a>
    &nbsp;<a target="_blank" href="?action=result"> <i class="fas fa-print"></i> Print results</a>
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
