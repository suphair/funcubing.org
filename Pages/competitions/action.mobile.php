<meta name="viewport" content="width=device-width">
<head>
    <title><?= $comp->name ?></title>
    <link rel="icon" href="<?= PageLocal() ?>Pages/competitions/mobile.png" >
    <script src="<?= PageLocal() ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="<?= PageLocal() ?>Pages/competitions/action.mobile.css?1" type="text/css"/>
</head>
<script>
    $('body').html('');
</script><button class='back' onclick="window.location.href = location.protocol + '//' + location.host + location.pathname;">X</button>
<b><?= $comp->name ?></b>
<br id="landscape"> 
<select id="event">
    <?php
    foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
        $event_dict = $events_dict[$event_round->event_dict];
        ?>
        <option name="<?= $event_round->id ?>" >
            <?= $event_dict->name ?> -  <?= $rounds_dict[$event_round->round == $event_round->rounds ? 0 : $event_round->round]->fullName; ?>
        </option>
    <?php } ?>
</select>

<?php
$events = unofficial\getEvents($comp->id);
foreach ($comp_data->event_rounds as $event_round) {
    $format_dict = $formats_dict[$event_round->format_dict];
    $formats = array_unique([$format_dict->format, 'best']);
    ?>
    <a name="<?= $event_round->id ?>"></a>
    <table data-event id="<?= $event_round->id ?>">
        <thead>
            <tr>
                <td align="right">#</td>
                <td>Name</td>
                <?php foreach (range(1, $format_dict->attempts) as $i) { ?>
                    <td align="center" data-attempts class="mobile_attempt">
                        <?= $i ?>
                    </td>
                <?php } ?>
                <?php foreach ($formats as $format) { ?>
                    <td align="right" class="mobile_attempt">
                        <b><?= str_replace(['average', 'mean', 'best'], ['Avg', 'Mean', 'Best'], $format); ?></b>
                    </td>
                <?php } ?>    
            </tr>
        </thead>
        <tbody>
            <?php
            $competitors = unofficial\getCompetitorsByEventround($event_round->id);
            foreach ($competitors as $competitor) {
                $modal = "<b>$competitor->name</b><br>";
                $class = ($competitor->podium ? 'podium' : '') . ' ' . ($competitor->next_round ? 'next_round' : '' );
                foreach (range(1, $format_dict->attempts) as $i) {
                    $modal .= "<b>$i</b> " . ($competitor->{"attempt$i"}) . "<br>";
                }
                foreach ($formats as $format) {
                    $modal .= "<b>$format {$competitor->$format}</b><br>";
                }
                ?>
                <tr data-modal='<?= $modal ?>'>
                    <td align="right" class="<?= $class ?>">
                        <?= $competitor->place ?>
                    </td>
                    <td  class="<?= $class ?>">
                        <?= $competitor->name ?>
                    </td>

                    <?php foreach (range(1, $format_dict->attempts) as $i) { ?>
                        <td align="center" data-attempts class="mobile_attempt">
                            <?= $competitor->{"attempt$i"} ?>
                        </td>
                    <?php } ?>

                    <?php foreach ($formats as $f => $format) { ?>
                        <td align="right" class="mobile_attempt">
                            <?php if (!$f) { ?>
                                <b><?= $competitor->$format; ?></b>
                            <?php } else { ?>
                                <?= $competitor->$format; ?>
                            <?php } ?>
                        </td>
                    <?php } ?>    
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
<?php } ?>

<div class="popup-fade" hidden>
    <div class="popup">
    </div>		
</div>

<script>
<?php include 'action.mobile.js'; ?>
</script>

<?php exit(); ?>