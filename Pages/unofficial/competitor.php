<?php
$competitions = unofficial\getCompetitionsByCompetitor($competitor->id);
$organizers = [];
foreach ($competitions as $competition) {
    $organizers[$competition->competition_competitor_id] = $competition->competition_competitor_name;
}
asort($organizers);
?>
<div class="shadow" >
    <h1>
        <i class="fas fa-user"></i> 
        <?= $competitor->name ?>
    </h1>
    <?php if (sizeof($organizers) > 1) { ?>
        <h3>Organizers:
            <?php foreach ($organizers as $organizer_id => $organizer_name) { ?>
                <?php if ($organizer_id == $competitor->creator_id) { ?>
                    <i class="fas fa-check-square"></i>
                    <?= $organizer_name ?>
                <?php } else { ?>
                    <input id ='organizer_<?= $organizer_id ?>' data-organizer='<?= $organizer_id ?>' type='checkbox' >
                    <label for='organizer_<?= $organizer_id ?>'>
                        <?= $organizer_name ?>
                    </label>
                <?php } ?>    
            <?php } ?>
        <?php } ?>
    </h3>    
    <div class="shadow2" >
        <h2>
            Competitions
        </h2>
        <table class="table_new" data-showing>
            <tbody>    
                <?php foreach ($competitions as $competition) { ?>
                    <tr  
                    <?php if ($competition->competition_competitor_id != $competitor->creator_id) { ?>
                            data-row-organizer='<?= $competition->competition_competitor_id ?>' hidden
                        <?php } ?>
                        >
                        <td>
                            <?= dateRange($competition->date); ?>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "unofficial/$competition->secret" ?>">
                                <?= $competition->name ?>
                            </a>
                        </td>
                        <td>
                            <?= $competition->competition_competitor_name ?>
                        </td>
                        <td>
                            <a target="_blank" href="<?= PageIndex() . "unofficial/competitor/$competition->competitor_id?action=certificate" ?>">
                                <i class="fas fa-print"></i>
                                certificate
                            </a>
                        </td>       
                    </tr>
                <?php } ?>
            <tbody> 
        </table>
        <?php
        $results = unofficial\getResutsByCompetitorMain($competitor->id);
        $results_events = [];
        foreach ($results as $result) {
            $results_events[$result->event_dict][] = $result;
        }
        ?>
    </div>

    <div class="shadow2" >
        <h2>Results</h2>
        <table class="table_new" data-showing>
            <thead>
                <tr>
                    <td>Place</td>
                    <td>Event</td>
                    <td>Date</td>
                    <td>Competition</td>
                    <?php foreach (range(1, 5) as $i) { ?>
                        <td class='attempt'>
                            <?= $i ?>
                        </td>
                    <?php } ?>
                    <td class="table_new_center">
                        Average
                    </td>
                    <td class="table_new_center">
                        Best
                    </td>
                <tr>
            </thead>
            <tbody>
                <?php foreach ($results_events as $results_event) { ?>
                    <tr>
                        <td colspan='10' >
                            &nbsp;
                        </td>
                    </tr>    
                    <?php foreach ($results_event as $result) { ?>
                        <tr  
                        <?php if ($result->competition_competitor_id != $competitor->creator_id) { ?>
                                data-row-organizer='<?= $result->competition_competitor_id ?>' hidden
                            <?php } ?>
                            >
                            <td align='center' class="<?= $result->podium ? 'podium' : '' ?>">
                                <?= $result->place ?> 
                            </td>
                            <td>
                                <i class="<?= $result->event_image ?>"></i>
                                <?= $result->event_name ?>, 
                                <?php if ($result->final) { ?>
                                    final
                                <?php } else { ?>
                                    round <?= $result->round ?>  
                                <?php } ?>
                            </td>
                            <td>
                                <?= dateRange($result->competition_date); ?>
                            </td>
                            <td>
                                <a href="<?= PageIndex() . "unofficial/$result->secret" ?>">
                                    <?= $result->competition_name ?>
                                </a> 
                            </td>
                            <?php foreach (range(1, 5) as $i) { ?>
                                <td class='attempt'>
                                    <?= str_replace('dns', '', $result->{"attempt$i"}); ?>
                                </td>
                            <?php } ?>
                            <td class='attempt' style="font-weight: bold">
                                <?= str_replace(['dns', 'dnf', '-cutoff'], '', $result->average); ?>
                                <?= str_replace(['dns', 'dnf'], '', $result->mean); ?>
                            </td>    
                            <td class='attempt' style="font-weight: bold">
                                <?= str_replace(array('dns', 'dnf'), '', $result->best); ?>
                            </td>
                        </tr>    
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
<?php include 'competitor.js' ?>
</script>