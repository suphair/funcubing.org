<?php $judges = unofficial\getRankedJudges(['is_archive' => false]); ?>
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
            <td align="center">WCA ID</td>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($judges as $judge) { ?>
            <tr>   
                <td>                    
                    <?= $judge->name ?>
                </td>
                <td>
                    <?= $judge->rank ?>
                </td>    
                <td>
                    <?php if ($judge->wcaid) { ?>
                        <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $judge->wcaid ?>'>
                            <?= $judge->wcaid ?>
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table> 
<?php if (unofficial\federation()) { ?>
    <?php $judges = unofficial\getRankedJudges(); ?>
    <h2>
        <i class="fas fa-user-cog"></i>
        Управление
    </h2>
    <form method="POST" action="?ranking_judge_add">
        WCA ID <input name="wcaid" required"> <button> <i class="fas fa-user-plus"></i>Добавить</button>
    </form>
    <table class="table_new">
        <thead>
            <tr>
                <td>

                </td>
                <td>
                    Имя (EN)
                </td>
                <td>
                    Имя (RU)
                </td>
                <td>
                    Звание (EN)
                </td>
                <td>
                    Звание (RU)
                </td>
                <td>
                    WCA ID
                </td>
                <td>
                    В архиве
                </td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($judges as $judge) { ?>
            <form method="POST" action="?ranking_judge">
                <tr>
                    <td>
                        <?php if ($judge->is_archive) { ?>
                            <i class="fas fa-user-alt-slash"></i>
                        <?php } ?>
                    </td>
                    <td>
                        <?= $judge->nameEN ?>
                    </td>
                    <td>
                        <input name="nameRU" value="<?= $judge->nameRU ?>">
                    </td>
                    <td>
                        <input name="rankEN" value="<?= $judge->rankEN ?>">
                    </td>
                    <td>
                        <input name="rankRU" value="<?= $judge->rankRU ?>">
                    </td>
                    <td>
                        <?= $judge->wcaid ?>
                    </td>
                    <td>
                        <input type="checkbox" name="is_archive" <?= $judge->is_archive ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <input hidden name="wcaid" value="<?= $judge->wcaid ?>">
                        <button>Сохранить</button>
                    </td>
                </tr>

            </form>
        <?php } ?>
    </tbody>
    </table>        
    <?php
}?>