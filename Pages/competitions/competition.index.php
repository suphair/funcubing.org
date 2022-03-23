<?php
$code = request(3);
$round = request(4);

if ($event_round_this) {
    $event_dict = $comp_data->event_dict->by_code[$code]->id ?? FALSE;
    if (!$round) {
        $round = 1;
    }
    $event_round_this = $comp_data->rounds[$event_dict][$round]->round->id ?? null;
    $event = unofficial\getEventByEventround($event_round_this);
    $competitors = unofficial\getCompetitorsByEventround($event_round_this);
    $formats = array_unique([$event->format, 'best']);
    if ($event_round_this == null) {
        include 'competition.event.wrong.php';
        $section = false;
    }
}
$records = unofficial\getRankedRecordbyCompetition($comp->id);
?>
<h1>
    <a 
        class="<?= $section == 'competitors' ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret/competitors" ?>">
        <i title='Competitors' class="fas fa-users"></i>
    </a>
    <a 
        class="<?= $section == 'events' ? 'select' : '' ?>"
        href="<?= PageIndex() . "competitions/$secret/events" ?>">
        <i title='Events' class="fas fa-newspaper"></i>
    </a>
    <?php if (sizeof($records)) { ?>
        <a 
            class="<?= $section == 'records' ? 'select' : '' ?>"
            href="<?= PageIndex() . "competitions/$secret/records" ?>">
            <i title='Records' class="fas fa-trophy"></i> 
        </a>    
    <?php } ?>

    <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
        <a class="<?= $event_round_this == $event_round_id ? 'select' : '' ?>"
           title="<?= $comp_data->events[$event_round->event_dict]->name ?> / round <?= $event_round->round ?>"
           href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
            <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
        </a>
    <?php } ?>
</h1>   

<div class="shadow2" >
    <?php
    if ($section == 'events') {
        include 'competition.events.php';
    } elseif ($section == 'records') {
        include 'competition.records.php';
    } else {
        if ($comp->my or $comp->organizer) {
            ?>
            <p>
                <?php if (!$event_round_this) { ?>
                    <a href="?action=certificates">Download certificates</a>  ▪
                <?php } ?>
                <?php if (sizeof($comp_data->competitors)) { ?>
                    <a target="_blank" href="?action=cards">Print competitors cards</a> ▪
                <?php } ?>     
                    <a target="_blank" href="?action=result">Print the results</a> ▪
                <a target="_blank" href="?action=cards&blank">Print blank competitors cards</a> ▪
                <a target="_blank" href="?action=export">Export results</a>
                <?php if ($event_round_this) { ?>
                    ▪ <a target="_blank" href="?action=export&format=txt">TXT results</a>
                <?php } ?>
            </p>
            <?php
        }
        if (($comp->my or $comp->organizer) and $section == 'result') {
            include 'competition.event.index.php';
        } elseif ($event_round_this and ($comp->my or $comp->organizer) and $section == 'event_competitors') {
            include 'competition.event.index.php';
        } elseif ($section == 'event') {
            include 'competition.event.index.php';
        } elseif (!$event_round_this and $section == 'competitors') {
            include 'competition.competitors.php';
        }
    }
    ?>
</div>