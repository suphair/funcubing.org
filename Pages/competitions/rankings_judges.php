<?php $judges = unofficial\getRankedJudges(); ?>
<h2>
    <i title='Competitors' class="fas fa-user-tie"></i>
    <?= t('Judges', 'Судьи') ?> (<?= count($judges) ?>)
</h2>
<table class='table_new'>
    <thead>
        <tr>
            <td>
                <?= t('Name', 'Имя') ?>
            </td>
            <td>
                <?= t('Rank', 'Звание') ?>
            </td>
            <td>
                <?= t('Competitions', 'Соревнования') ?>
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
                    <?= t($judge->is_senior ? 'Senior Judge' : 'Judge', $judge->is_senior ? 'Главный судья' : 'Судья' )
                    ?>
                </td>     
                <td align="center">
                    <?= $judge->competitions ?>
                </td>     
            </tr>
        <?php } ?>
    </tbody>
</table> 