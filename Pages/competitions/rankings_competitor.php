<?php
$FCID = strtoupper(db::escape(request(3)));
$competitor = unofficial\getCompetitorRankings($FCID);
if (!$competitor) {
    echo 11;
}
?>
<h1>
    <i class="fas fa-user"></i> <?= $competitor->name ?>
    <?= $ranked_icon ?> <?= $competitor->FCID ?>
</h1> 

<?php
$results = unofficial\getResutsByCompetitorRankings($competitor->FCID);
$results_events = [];
foreach ($results as $result) {
    $results_events[$result->event_dict][] = $result;
}
?>
<div>
    <h2>
        Personal Records
    </h2>
    <table class="table_new" data-showing>
        <thead>
            <tr>
                <td>Event</td>
                <td>Rank</td>
                <td>Single</td>
                <td>Average</td>
                <td>Rank</td>
            <tr>
        </thead>
        <tbody>
            <?php
            foreach ($events_dict as $event_att) {
                $rating_best = $ratings[$event_att->id]['best'][$FCID] ?? false;
                $rating_average = $ratings[$event_att->id]['average'][$FCID] ?? false;
                $top_rating_best = ($rating_best->order ?? false) <= 10;
                $top_rating_average = ($rating_average->order ?? false) <= 10;
                ?>
                <?php if ($rating_best or $rating_average) { ?>
                    <tr>
                        <td>
                            <i class="<?= $event_att->image ?>"></i>
                            <?= $event_att->name ?>
                        </td>
                        <td align='center' class="<?= $top_rating_best ? 'podium' : '' ?>">
                            <?= $rating_best->order ?? false ?>
                        </td>
                        <td align='center' > <?= $rating_best->result ?? false ?></td>
                        <td align='center' > <?= $rating_average->result ?? false ?></td>
                        <td align='center' class="<?= $top_rating_average ? 'podium' : '' ?>">
                            <?= $rating_average->order ?? false ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

        </tbody>
    </table>
</div>

<div>
    <h2>Results</h2>
    <table class="table_new" data-showing>
        <thead>
            <tr>
                <td>Event</td>
                <td>Competition</td>
                <td>Round</td>
                <td>Place</td>
                <td class="table_new_center">
                    Single
                </td>
                <td style="padding:0"></td>
                <td class="table_new_center">
                    Average
                </td>
                <td style="padding:0"></td>
                <td class="table_new_center" colspan="5">
                    Solves
                </td>
            <tr>
        </thead>
        <tbody>
            <?php
            foreach ($results_events as $results_event) {
                foreach ($results_event as $result) {
                    $record_best = in_array($result->result_id, $competitor_history_record[$competitor->FCID]['best'] ?? []);
                    $record_average = in_array($result->result_id, $competitor_history_record[$competitor->FCID]['average'] ?? []);
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $result->event_image ?>"></i>
                            <?= $result->event_name ?>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/$result->secret" ?>">
                                <?= $result->competition_name ?>
                            </a> 
                        </td>
                        <td>
                            <?= $rounds_dict[$result->final ? 0 : $result->round]->smallName; ?>
                        </td>
                        <td align='center' class="<?= $result->podium ? 'podium' : '' ?>">
                            <?= $result->place ?> 
                        </td>
                        <td class='attempt <?= $record_best ? 'record' : '' ?>' style="font-weight: bold">
                            <?= $result->best; ?>
                        </td>
                        <td style="padding:0">
                            <?= $record_best ? 'R' : '' ?>
                        </td>
                        <td class='attempt <?= $record_average ? 'record' : '' ?>' style="font-weight: bold">
                            <?= str_replace(['dns', '-cutoff'], ['', 'dnf'], $result->average); ?>
                            <?= str_replace(['dns', '-cutoff'], ['', 'dnf'], $result->mean); ?>
                        </td>
                        <td style="padding:0">
                            <?= $record_average ? 'R' : '' ?>
                        </td>
                        <?php foreach (range(1, 5) as $i) { ?>
                            <td class='attempt'>
                                <?= str_replace('dns', '', $result->{"attempt$i"}); ?>
                            </td>
                        <?php } ?>
                    </tr>    
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php $competitions = unofficial\getRankedCompetitionsbyCompetitor($competitor->FCID); ?>
<div>
    <h2>
        Competitions
    </h2>

    <table class='table_new'>
        <thead>
            <tr>
                <td>
                    Competition
                </td>
                <td>
                    Senior Judge
                </td>
                <td>
                    Judge
                </td>
                <td>
                    Organizer
                </td>
                <td/>
                <td>
                    Date
                </td>
                <td>
                    Web site
                </td>
            </tr>    
        </thead>
        <tbody>
            <?php foreach ($competitions as $competition) { ?>
                <tr>   
                    <td>                    
                        <a href="<?= PageIndex() ?>competitions/<?= $competition->secret ?>"><?= $competition->name ?> </a>
                    </td>
                    <td>
                        <?= $competition->judgeSenior_name ?>
                    </td>      
                    <td>
                        <?= $competition->judgeJunior_name ?>
                    </td>      
                    <td>
                        <?= $competition->competitor_name ?>
                    </td>      
                    <td>
                        <?php if ($competition->upcoming) { ?>
                            <i style='color:var(--gray)' class="fas fa-hourglass-start"></i>
                        <?php } ?>
                        <?php if ($competition->run) { ?>
                            <i style='color:var(--green)' class="fas fa-running"></i>
                        <?php } ?>
                    </td>
                    <td>
                        <?= dateRange($competition->date, $competition->date_to) ?>
                    </td>
                    <td>
                        <?php unofficial\getFavicon($competition->website) ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table> 
</div>

<script>
<?php include 'competitor.js' ?>
</script>