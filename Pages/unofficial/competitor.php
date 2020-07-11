<div class="shadow" >
    <h1>
        <i class="fas fa-user"></i> 
        <?= $competitor->name ?>
    </h1>
    <h3>Registered by <?= $competitor->creator_name ?></h3>    
    <div class="shadow2" >
        <h2>
            Competitions
        </h2>
        <table class="table_new">
            <tbody>    
                <?php foreach (unofficial\getCompetitionsByCompetitor($competitor->id) as $competition) { ?>
                    <tr>
                        <td>
                            <?= dateRange($competition->date); ?>
                        </td>
                        <td>
                            <a href="<?= PageIndex() . "unofficial/$competition->secret" ?>">
                                <?= $competition->name ?>
                            </a>
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
        <table class="table_new">
            <thead>
                <tr>
                    <td>Place</td>
                    <td>Event</td>
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
                        <tr>
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
                                <a href="<?= PageIndex() . "unofficial/$result->secret" ?>">
                                    <?= $result->competition_name ?>
                                </a> 
                                <?= dateRange($result->competition_date); ?>

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