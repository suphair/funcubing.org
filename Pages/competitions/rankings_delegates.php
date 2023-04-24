<?php $delegates = unofficial\getRankedDelegates(['is_archive' => false]); ?>
<h2>
    <i title='Competitors' class="fas fa-user-tie"></i>
    <?= t('Delegates', 'Делегаты') ?> (<?= count($delegates) ?>)
</h2>
<div> 
    <?= markdown::convertToHtml(unofficial\getText('delegates')); ?>
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
        <?php foreach ($delegates as $delegate) { ?>
            <tr>   
                <td>
                    <font size="3">
                    <?= unofficial\build_contact($delegate) ?>
                    </font>
                </td>
                <td>                    
                    <?= $delegate->name ?>
                </td>
                <td>
                    <?= $delegate->region ?>
                </td>    
                <td>
                    <?= $delegate->rank ?>
                </td>    
                <td>
                    <?php if ($delegate->wcaid) { ?>
                        <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $delegate->wcaid ?>'>
                            <?= $delegate->wcaid ?>
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table> 
<?php if (api\get_me()->is_federation_ext ?? false or \api\get_me()->is_admin ?? false) { ?>
    <?php $delegates = unofficial\getRankedDelegates(); ?>
    <h2>
        <i class="fas fa-user-cog"></i>
        Управление
    </h2>
    <form method="POST" action="?text">
        <input hidden name='code' value='delegates'>
        EN<textarea name="textEN" style="width:500px; height:100px; "><?= unofficial\getText('delegatesEN') ?></textarea>
        support <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>
        <br>
        RU<textarea name="textRU" style="width:500px; height:100px; "><?= unofficial\getText('delegatesRU') ?></textarea>
        support <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>
        <br><button>Описание</button> 
    </form>
    <hr>
    <form method="POST" action="?ranking_delegate_add">
        WCA ID <input name="wcaid" required"> 
        <button> <i class="fas fa-user-plus"></i>Добавить</button>
    </form>
    <br>
    <form method="POST" action="?ranking_delegate">
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
                <?php foreach ($delegates as $delegate) { ?>
                    <tr>
                        <td>
                            EN<br>RU
                        </td>
                        <td>
                            <?php if ($delegate->is_archive) { ?>
                                <i class="fas fa-user-alt-slash"></i>
                            <?php } ?>
                        </td>
                        <td>
                            <?= $delegate->nameEN ?>
                            <br>
                            <input name="delegates[<?= $delegate->wcaid ?>][nameRU]" value="<?= $delegate->nameRU ?>">
                        </td>
                        <td>
                            <input name="delegates[<?= $delegate->wcaid ?>][rankEN]" value="<?= $delegate->rankEN ?>">
                            <br>
                            <input name="delegates[<?= $delegate->wcaid ?>][rankRU]" value="<?= $delegate->rankRU ?>">
                        </td>
                        <td>
                            <input name="delegates[<?= $delegate->wcaid ?>][regionEN]" value="<?= $delegate->regionEN ?>">
                            <br>
                            <input name="delegates[<?= $delegate->wcaid ?>][regionRU]" value="<?= $delegate->regionRU ?>">
                        </td>
                        <td>
                            <?= $delegate->wcaid ?>
                            <input hidden name="delegates[<?= $delegate->wcaid ?>][wcaid]" value="<?= $delegate->wcaid ?>">
                        </td>
                        <td style="text-align:right">
                            <p><i class="fab fa-vk"></i>
                                <input name="delegates[<?= $delegate->wcaid ?>][vk]" value="<?= $delegate->vk ?>"/></p>
                            <p><i class="fab fa-telegram-plane"></i>
                                <input name="delegates[<?= $delegate->wcaid ?>][telegram]" value="<?= $delegate->telegram ?>"/></p>
                            <p><i class='fas fa-phone'></i>
                                <input name="delegates[<?= $delegate->wcaid ?>][phone]" value="<?= $delegate->phone ?>"/></p>
                            <p><i class='far fa-envelope'></i>
                                <input name="delegates[<?= $delegate->wcaid ?>][email]" value="<?= $delegate->email ?>"/></p>
                        </td>
                        <td>
                            <input type="checkbox" name="delegates[<?= $delegate->wcaid ?>][is_archive]" <?= $delegate->is_archive ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>        

        <button>Сохранить</button>
    </form>
    <?php
}?>