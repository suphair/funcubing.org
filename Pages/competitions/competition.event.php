<?php
$event = unofficial\getEventByEventround($event_round_this);
$competitors = unofficial\getCompetitorsByEventround($event_round_this);
$formats = array_unique([$event->format, 'best']);
?>
<h2>
    <i class="<?= $event->image ?>"></i>
    <?= $event->name ?>,
    <?= $rounds_dict[$event->final ? 0 : $event->round]->fullName; ?>    
    <?php if ($comp->my or $comp->organizer) { ?>
        <a href="<?= PageIndex() . "competitions/$secret/result/{$events_dict[$event->event_dict]->code}/$event->round" ?> ">
            <i class="far fa-keyboard"></i> Enter results and add competitors
        </a>
    <?php } ?>
</h2> 
<p>
    <?= $event->comment ?>
    <?= $event->cutoff ? ('<i class="fas fa-cut"></i> Cutoff ' . $event->cutoff ) : '' ?>
    <?= ($event->time_limit and!$event->cumulative) ? ('<i class="fas fa-stop-circle"></i> Time limit ' . $event->time_limit ) : '' ?>
    <?= ($event->time_limit and $event->cumulative) ? ('<i class="fas fa-plus-circle"></i> Time limit ' . $event->time_limit . ' cumulative' ) : '' ?>
</p>
<table class="table_new">
    <thead>
        <tr>
            <td>Place</td>
            <td>Competitor</td>
            <?php foreach (range(1, $event->attempts) as $i) { ?>
                <td class="attempt"><?= $i ?></td>
            <?php } ?>
            <?php foreach ($formats as $format) { ?>
                <td class="attempt"><?= ucfirst($format) ?></td>
            <?php } ?>
        <tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td align="center" class="<?= $competitor->podium ? 'podium' : '' ?> <?= $competitor->next_round ? 'next_round' : '' ?>">
                    <?= $competitor->place ?> 
                </td>
                <td >
                    <?php
                    if ($comp->ranked) {
                        $link = $competitor->FCID ? "rankings/competitor/$competitor->FCID" : false;
                    } else {
                        $link = "competitor/$competitor->id";
                    }
                    if ($link) {
                        ?>
                        <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $competitor->name ?></a>
                    <?php } else { ?>
                        <?= $competitor->name ?>
                    <?php } ?>
                </td>
                <?php foreach (range(1, $event->attempts) as $i) { ?>
                    <td class="<?= $i == $event->attempts ? 'border-right-solid' : '' ?> attempt">
                        <?= str_replace("dns", "", $competitor->{"attempt$i"}) ?>
                    </td>
                <?php } ?>

                <?php foreach ($formats as $format) { ?>
                    <td  class="attempt">
                        <b>
                            <?= str_replace(["dns", "-cutoff"], ["dnf", ""], $competitor->$format) ?>
                        </b>
                    </td>
                <?php } ?>    
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if (!sizeof($competitors)) { ?>
    <p>No competitors</p>
<?php } ?>   
