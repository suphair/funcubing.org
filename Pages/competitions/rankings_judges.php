<?php $judges = unofficial\getRankedJudges(['is_archive' => false]); ?>
<h2>
    <i title='Competitors' class="fas fa-user-tie"></i>
    <?= t('Judges', 'Судьи') ?> (<?= count($judges) ?>)
</h2>
<div>
    <?= markdown::convertToHtml(unofficial\getText('judges')); ?>
</div>
<table class='table thead_stable'>
    <thead>
        <tr>
            <th width="10%">
                <?= t('Contacts', 'Контакты') ?>
            </th>
            <th>
                <?= t('Name', 'Имя') ?>
            </th>
            <th>
                <?= t('Region', 'Регион') ?>
            </th>
            <th>
                <?= t('Rank', 'Звание') ?>
            </th>
            <th>
                WCA ID <i class="fas fa-external-link-alt"></i>
            </th>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($judges as $judge) { ?>
            <tr>   
                <td>
                    <font size="3">
                    <?= unofficial\build_contact($judge) ?>
                    </font>
                </td>
                <td>                    
                    <?= $judge->name ?>
                </td>
                <td>
                    <?= $judge->region ?>
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
<?php if (api\get_me()->is_federation ?? false or \api\get_me()->is_admin ?? false) { ?>
    <?php $judges = unofficial\getRankedJudges(); ?>
    <h2>
        <i class="fas fa-user-cog"></i>
        Управление
    </h2>
    <form method="POST" action="?text">
        <input hidden name='code' value='judges'>
        EN<textarea name="textEN" style="width:500px; height:100px; "><?= unofficial\getText('judgesEN') ?></textarea>
        support <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>
        <br>
        RU<textarea name="textRU" style="width:500px; height:100px; "><?= unofficial\getText('judgesRU') ?></textarea>
        support <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>
        <br><button>Описание</button> 
    </form>
    <hr>
    <form method="POST" action="?ranking_judge_add">
        WCA ID <input name="wcaid" required"> 
        <button> <i class="fas fa-user-plus"></i>Добавить</button>
    </form>
    <br>
    <form method="POST" action="?ranking_judge">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>
                        Имя
                    </th>
                    <th>
                        Звание
                    </th>
                    <th>
                        Регион
                    </th>
                    <th>
                        WCA ID
                    </th>
                    <th>
                        Контакты
                    </th>
                    <th>
                        Hide
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($judges as $judge) { ?>
                    <tr>
                        <td>
                            EN<br>RU
                        </td>
                        <td>
                            <?php if ($judge->is_archive) { ?>
                                <i class="fas fa-user-alt-slash"></i>
                            <?php } ?>
                        </td>
                        <td>
                            <?= $judge->nameEN ?>
                            <br>
                            <input name="judges[<?= $judge->wcaid ?>][nameRU]" value="<?= $judge->nameRU ?>">
                        </td>
                        <td>
                            <input name="judges[<?= $judge->wcaid ?>][rankEN]" value="<?= $judge->rankEN ?>">
                            <br>
                            <input name="judges[<?= $judge->wcaid ?>][rankRU]" value="<?= $judge->rankRU ?>">
                        </td>
                        <td>
                            <input name="judges[<?= $judge->wcaid ?>][regionEN]" value="<?= $judge->regionEN ?>">
                            <br>
                            <input name="judges[<?= $judge->wcaid ?>][regionRU]" value="<?= $judge->regionRU ?>">
                        </td>
                        <td>
                            <?= $judge->wcaid ?>
                            <input hidden name="judges[<?= $judge->wcaid ?>][wcaid]" value="<?= $judge->wcaid ?>">
                        </td>
                        <td style="text-align:right">
                            <p><i class="fab fa-vk"></i>
                                <input name="judges[<?= $judge->wcaid ?>][vk]" value="<?= $judge->vk ?>"/></p>
                            <p><i class="fab fa-telegram-plane"></i>
                                <input name="judges[<?= $judge->wcaid ?>][telegram]" value="<?= $judge->telegram ?>"/></p>
                            <p><i class='fas fa-phone'></i>
                                <input name="judges[<?= $judge->wcaid ?>][phone]" value="<?= $judge->phone ?>"/></p>
                            <p><i class='far fa-envelope'></i>
                                <input name="judges[<?= $judge->wcaid ?>][email]" value="<?= $judge->email ?>"/></p>
                        </td>
                        <td>
                            <input type="checkbox" name="judges[<?= $judge->wcaid ?>][is_archive]" <?= $judge->is_archive ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>        

        <button>Сохранить</button>
    </form>
    <?php
}?>