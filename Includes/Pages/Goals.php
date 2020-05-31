<?php
$Competitor = GetCompetitorData();

$upcomings = [-1];
$withGoals= [];
if ($Competitor) {
    foreach (GetUpcomingCompetition($Competitor->id)['upcoming_competitions'] as $competition) {
        $upcomings[] = $competition['id'];
    }
    DataBaseClass::Query("SELECT DISTINCT Competition FROM Goal WHERE Competitor='{$Competitor->id}' ");
    foreach(DataBaseClass::getRows() as $row){
        $withGoals[]=$row['Competition'];
    }
    
}
    DataBaseClass::FromTable("GoalDiscipline", "Code<>'333mbf'");
    $disciplines = DataBaseClass::QueryGenerate();
    $CompetitorsDiscipline = [];
    foreach ($disciplines as $discipline) {
        DataBaseClass::Query("
                select  count(distinct G.Competitor) Competitors, CG.ID, GD.Code
                from Goal G
                   join GoalCompetition CG on CG.WCA=G.Competition
                   join GoalDiscipline GD on GD.Code=G.Discipline
                   group by CG.ID, GD.Code  
                ");
        foreach (DataBaseClass::getRows() as $r) {
            $CompetitorsDiscipline[$r['ID']][$r['Code']] = $r['Competitors'];
        }
    }
    ?>



    <div class="shadow">      

        <h2>Competitions</h2> 
        <table class='table_new'>
            <thead>
                <tr>
                    <?php if ($Competitor) { ?>
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
                DataBaseClass::Query("
                select
                DateStart<now() close,
                sum(case when G.Result is not null then 1 else 0 end) Results,
                count(distinct G.Competitor) Competitors,
                count(distinct G.ID) Goals,
                CG.* 
               from GoalCompetition CG
                   left outer join Goal G on CG.WCA=G.Competition
                   left outer join GoalDiscipline GD on GD.Code=G.Discipline
                   where G.ID is not null or CG.WCA in('" . implode("','", $upcomings) . "')
                   group by CG.ID
                   order by CG.DateEnd desc,CG.WCA
                ");
                foreach (DataBaseClass::getRows() as $row) {
                    ?>
                    <tr>
                        <?php if ($Competitor) { ?>
                        <td align='center'>
                            <?php if (in_array($row['WCA'], $upcomings)) { ?>
                                <i style="color:var(--blue)" class="fas fa-arrow-circle-right"></i>
                            <?php }elseif (in_array($row['WCA'], $withGoals)) { ?>
                                <i style="color:var(--green)" class="fas fa-arrow-circle-right"></i>
                            <?php } ?>
                        </td>
                        <?php } ?>
                            
                        <td align='center'>
                            <?php if ($row['Results']) { ?>
                                <i title='Results uploaded' style='color: var(--green)' class="fas fa-chevron-circle-down"></i>
                            <?php } elseif ($row['close']) { ?>
                                <i title='Waiting for results' style='color: var(--red)' class="fas fa-hourglass-start"></i>
                            <?php } else { ?>
                                <i title='Upcoming competitions' style='color: var(--blue)' class="fas fa-door-open"></i>
                            <?php } ?>
                        </td>
                        <td>
                            <b>
                                <?= date_range($row['DateStart'], $row['DateEnd']); ?>
                            </b>
                        </td>
                        <td>
                            <span class='flag-icon flag-icon-<?= strtolower($row['Country']) ?>'></span>
                        </td>
                        <td>
                            <a href="<?= PageIndex() ?>Goals/Competition/<?= $row['WCA'] ?>">
                                <?= $row['Name'] ?>
                            </a>
                        </td>
                        <td align="center">
                            <?= $row['Competitors'] ?>
                        </td>
                        <td align="center">
                            <?= $row['Goals'] ?>
                        </td>
                        <td>
                            <b><?= CountryName($row['Country']) ?></b>, 
                            <?= $row['City'] ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>    
    </div>
    