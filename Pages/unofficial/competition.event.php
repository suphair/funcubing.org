<?php
$event = unofficial\getEventByEventround($event_round_this);
$competitors = unofficial\getCompetitorsByEventround($event_round_this);
$formats = array_unique([$event->format, 'best']);
?>
<h2>
    <i class="<?= $event->image ?>"></i>
    <?= $event->name ?>
    <?php if ($event->final and $event->rounds > 1) { ?>    
        , final
    <?php } ?>
    <?php if (!$event->final and $event->rounds > 1) { ?>   
        , round <?= $event->round ?>
    <?php } ?>    
    <?php if ($comp->my or $comp->organizer) { ?>
        <a href="<?= PageIndex() . "unofficial/$secret/result/{$events_dict[$event->event_dict]->code}/$event->round" ?> ">
            <i class="far fa-keyboard"></i> Enter results
        </a>
    <?php } ?>
</h2> 
<p>
    <?= $event->comment ?>
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
                    <a href="<?= PageIndex() . "unofficial/competitor/$competitor->id" ?>">
                        <?= $competitor->name ?>
                    </a>
                </td>
                <?php foreach (range(1, $event->attempts) as $i) { ?>
                    <td class="<?= $i == $event->attempts ? 'border-right-solid' : '' ?> attempt">
                        <?= str_replace("dns", "", $competitor->{"attempt$i"}) ?>
                    </td>
                <?php } ?>

                <?php foreach ($formats as $format) { ?>
                    <td  class="attempt">
                        <b>
                            <?= str_replace(["dnf", "dns", "-cutoff"], "", $competitor->$format) ?>
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
