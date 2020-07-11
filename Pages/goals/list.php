<?php
$withGoals = [];
if ($me->id ?? FALSE) {
    $rows = db::rows("SELECT DISTINCT competition FROM goals WHERE competitor='{$me->id}' ");
    if ($rows) {
        $withGoals = array_column($rows, 'competition');
    }
}

$rows = db::rows("SELECT count(distinct goals.Competitor) competitors,"
                . " goals_competitions.id,"
                . " goals_events.code "
                . " FROM goals "
                . " JOIN goals_competitions ON goals_competitions.WCA = goals.Competition "
                . " JOIN goals_events ON goals_events.code = goals.event "
                . " GROUP BY goals_competitions.id, goals_events.code   ");
foreach ($rows as $row) {
    $CompetitorsDiscipline[$row->id][$row->code] = $row->competitors;
}
?>



<div class="shadow">      

    <h2>Competitions</h2> 
    <table class='table_new'>
        <thead>
            <tr>
                <?php if ($me) { ?>
                    <td>
                        My goals
                    </td>
                <?php } ?>
                <td>
                </td>
                <td>
                    Date
                </td>
                <td>

                </td>
                <td>
                    Competition
                </td>
                <td>
                    Competitors
                </td>
                <td>
                    Goals
                </td>
                <td>
                    Country
                </td>

            </tr> 
        </thead>
        <tbody>
            <?php
            $rows = db::rows("
                select
                DateStart<now() close,
                sum(case when goals.result is not null then 1 else 0 end) results,
                count(distinct goals.competitor) competitors,
                count(distinct goals.ID) goals,
                goals_competitions.* ,
                dict_countries.name country_name
               from goals_competitions 
                   left outer join goals on goals_competitions.WCA = goals.Competition
                   left outer join goals_events on goals_events.code = goals.event
                   left outer join dict_countries on dict_countries.iso2 = goals_competitions.Country
                   where goals.id is not null or goals_competitions.wca in('" . implode("','", $upcomings) . "')
                   group by goals_competitions.id, dict_countries.name
                   order by goals_competitions.dateEnd desc, goals_competitions.wca
                ");
            foreach ($rows as $row) {
                ?>
                <tr>
                    <?php if ($me->id ?? FALSE) { ?>
                        <td align='center'>
                            <?php if (in_array($row->wca, $upcomings)) { ?>
                                <i style="color:var(--blue)" class="fas fa-bullseye"></i>
                            <?php } elseif (in_array($row->wca, $withGoals)) { ?>
                                <i style="color:var(--green)" class="fas fa-bullseye"></i>
                            <?php } ?>
                        </td>
                    <?php } ?>

                    <td align='center'>
                        <?php if ($row->results) { ?>
                            <i title='Results uploaded' style='color: var(--green)' class="fas fa-chevron-circle-down"></i>
                        <?php } elseif ($row->close) { ?>
                            <i title='Waiting for results' style='color: var(--red)' class="fas fa-hourglass-start"></i>
                        <?php } else { ?>
                            <i title='Upcoming competitions' style='color: var(--blue)' class="fas fa-door-open"></i>
                        <?php } ?>
                    </td>
                    <td>
                        <b>
                            <?= dateRange($row->dateStart, $row->dateEnd); ?>
                        </b>
                    </td>
                    <td>
                        <span class='flag-icon flag-icon-<?= strtolower($row->country) ?>'></span>
                    </td>
                    <td>
                        <a href="<?= PageIndex() ?>goals/<?= $row->wca ?>">
                            <?= $row->name ?>
                        </a>
                    </td>
                    <td align="center">
                        <?= $row->competitors ?>
                    </td>
                    <td align="center">
                        <?= $row->goals ?>
                    </td>
                    <td>
                        <b><?= $row->country_name ?></b>, 
                        <?= $row->city ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>    
</div>
