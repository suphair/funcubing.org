<h1><i class="fas fa-random"></i> <?= t('Scrambles', 'Скрамблы') ?></h1>
<script><?php include('competition.scrambles.js') ?></script>
<?php
if ($json_scrambles ?? false) {
    $events_name = [];
    foreach ($events_dict as $e) {
        $events_name[$e->code] = $e;
    }
    $scrambles = json_decode($json_scrambles->json);
    $events_in = $scrambles->wcif->events;
    $events = [];
    foreach ($events_name as $event_code) {
        foreach ($events_in as $event) {
            if ($event->id == $event_code->code) {
                $events[] = $event;
            }
        }
    }
    ?>
    <table class='table_info'>
        <tr><td>Competition Name</td><td><?= $scrambles->competitionName ?></td></tr>
        <tr><td>Version</td><td><?= $scrambles->version ?></td></tr>
        <tr><td>Generation date</td><td><?= $scrambles->generationDate ?></td></tr>
        <tr><td></td><td><a target="_blank" href="<?= PageIndex() ?>competitions/<?= $competition->id ?>?action=scrambles">View JSON</a></td></tr>


    </table>
    <div class="menu" data-event-menu="1">
        <?php foreach ($events as $event) { ?>
            <a hreef="#" data-event="<?= $event->id ?>"><i class='<?= $events_name[$event->id]->image ?>'></i></a>
        <?php } ?>
    </div>
    <?php foreach ($events as $event) { ?>
        <div class='menu' data-group-menu="<?= $event->id ?>" hidden>
            <?php foreach ($event->rounds as $round_number => $round) { ?>
                <?php foreach ($round->scrambleSets as $scrambleSets_id => $scrambleSets) { ?>
                    <nobr><a href="#" data-group="<?= $event->id . '_' . $round_number . '_' . $scrambleSets_id ?>">раунд <?= $round_number + 1 ?> группа <?= $scrambleSets_id + 1 ?></a></nobr>
                <?php } ?>
            <?php } ?>  
        </div>    
    <?php } ?>
    <?php foreach ($events as $event) { ?>

        <div id="event_scramble_<?= $event->id ?>" class="event_scramble" hidden>    
            <?php foreach ($event->rounds as $round_number => $round) { ?>
                <?php foreach ($round->scrambleSets as $scrambleSets_id => $scrambleSets) { ?>
                    <div id="group_scramble_<?= $event->id . '_' . $round_number . '_' . $scrambleSets_id ?>" class="group_scramble" hidden>
                        <h2><span class='<?= $events_name[$event->id]->image ?>'>
                            </span><?= $events_name[$event->id]->name ?>
                            - раунд <?= $round_number + 1 ?>
                            - группа <?= $scrambleSets_id + 1 ?></h2>
                        <table class="table">
                            <tbody>
                                <?php
                                foreach ($scrambleSets->scrambles as $n => $scrs) {
                                    $key = $event->id . $round_number . $scrambleSets_id . $n;
                                    if ($event->id == 'minx') {
                                        $scrs = str_replace(["\r", "\n"], ' ', $scrs);
                                    }
                                    ?>
                                    <tr>
                                        <td  width="20">
                                            <?= $n + 1 ?></td>
                                        <td  width="300" style="font-size: 150%"><?= $scrs ?></td>
                                        <td id="<?= $key ?>" width="300">
                                            <?php if ($event->id != '333mbf') { ?>
                                                <script>
                                                    $(document).ready(function (argument) {
                                                        $("#<?= $key ?>").append(image.draw(["<?= $event->id ?>", "<?= $scrs ?>", 0]));
                                                    });
                                                </script>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php
                                foreach ($scrambleSets->extraScrambles as $n => $scrs) {
                                    $key = $event->id . $round_number . $scrambleSets_id . 'EX' . $n;
                                    if ($event->id == 'minx') {
                                        $scrs = str_replace(["\r", "\n"], ' ', $scrs);
                                    }
                                    ?>
                                    <tr>
                                        <td>Ex <?= $n + 1 ?></td>
                                        <td style="font-size: 150%"><?= $scrs ?></td>
                                        <td id="<?= $key ?>">
                                            <?php if ($event->id != '333mbf') { ?>
                                                <script>
                                                    $(document).ready(function (argument) {
                                                        $("#<?= $key ?>").append(image.draw(["<?= $event->id ?>", "<?= $scrs ?>", 0]));
                                                    });
                                                </script>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>

<script>
    $('.event_scramble').first().show();
    $('.group_scramble').first().show();
    $('.menu[data-event-menu] a').first().addClass('select');
    $('.menu[data-group-menu] a').first().addClass('select');
    $('.menu[data-group-menu]').first().show();

    $('[data-event]').click(function () {
        var event = $(this).data('event');
        $('.event_scramble').hide();
        $('.menu[data-group-menu]').hide();
        $('.menu[data-group-menu=' + event + ']').show();
        $('.menu[data-event-menu] a').removeClass('select');
        $(this).addClass('select');
        var event_scramble_id = '#event_scramble_' + event;
        $(event_scramble_id).show();
        $(event_scramble_id + ' .group_scramble').first().show();
        $('.menu[data-group-menu] a').removeClass('select');
        $('.menu[data-group-menu=' + event + '] a').first().addClass('select');
        return false;
    });

    $('[data-group]').click(function () {
        $('.group_scramble').hide();
        $('#group_scramble_' + $(this).data('group')).show();
        $('.menu[data-group-menu] a').removeClass('select');
        $(this).addClass('select');
        return false;
    });

</script>

<style>
    .menu a:hover {
        cursor: pointer;
    }
</style>