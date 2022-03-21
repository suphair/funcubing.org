<?php $judges = unofficial\getRankedJudges(); ?>
<h2>
    <i title='Competitors' class="fas fa-user-tie"></i>
    Judges  (<?= count($judges) ?>)
</h2>
<table class='table_new'>
    <thead>
        <tr>
            <td>
                Name
            </td>
            <td>
                Rank
            </td>
            <td>
                Competitions
            </td>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($judges as $judge) { ?>
            <tr>   
                <td>                    
                    <?= $judge->name ?>
                </td>
                <td>
                    <?= $judge->is_senior?'Senior Judge':'Junior Judge' ?>
                </td>     
                <td align="center">
                    <?= $judge->competitions ?>
                </td>     
            </tr>
        <?php } ?>
    </tbody>
</table> 