<?php $competitions = unofficial\getRankedCompetitions(); ?>
<h2>
    <i title='Competitors' class="fas fa-cubes"></i>
    Competitions  (<?= count($competitions) ?>)
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
            <td>
                Competitors
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
                <td align="center">
                    <?= $competition->competitors ?>
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