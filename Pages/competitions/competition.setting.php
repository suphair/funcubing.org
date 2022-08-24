<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $(function () {
        $("#datepicker_from").datepicker({dateFormat: "dd.mm.yy"});
        $("#datepicker_to").datepicker({dateFormat: "dd.mm.yy"});
    });
</script>
<?php include 'competition.setting.menu.php' ?>
<?php
$organizers = db::rows("SELECT "
                . " dict_competitors.name,"
                . " unofficial_organizers.wcaid"
                . " FROM unofficial_organizers"
                . " LEFT OUTER JOIN dict_competitors "
                . "             on unofficial_organizers.wcaid in (dict_competitors.wcaid,dict_competitors.wid) "
                . " WHERE competition=$comp->id"
                . " ORDER BY name");
?>
<table class="table_info">
    <tr>
        <td>
            <i class="fas fa-user-tie"></i>
            <?= t('Main organizer', 'Основной организатор') ?>
        </td>
        <td>
            <?= $comp->competitor_name ?> - <?= $comp->competitor_wcaid ?>
        </td>
    </tr>   
    <tr>
        <td>
            <i class="fas fa-user-tie"></i>
            <?= t('Оrganizers', 'Организаторы') ?>
        </td>
        <td>
            <?= t('All action except the settings (this section)', 'Все действия, кроме настроек (данный раздел)') ?>
        </td>
    </tr>
    <?php 
    foreach ($competition->organizers as $o => $organizer) { ?>
        <form method="POST" action="?organizer_remove" onsubmit="return confirm('<?= t('Remove organizer', 'Исключить организатора') ?>  <?= $organizer->name ?>?')">
            <tr>
                <td>
                </td>
                <td>
                    <input type="hidden" name="wcaid" value="<?= $organizer->wca_id ?>">
                    <button class="delete">
                        <i class="fas fa-user-minus"></i>
                        <?= t('Remove', 'Исключить') ?>
                    </button>
                    <?= $organizer->name ?> - <?= $organizer->wca_id ?>
                </td>
            </tr>
        </form>
    <?php } ?>
    <form method="POST" action="?organizer_add">
        <tr>
            <td>

            </td>
            <td>
                WCA ID <input name="wcaid" required="" value="">
                <button>
                    <i class="fas fa-user-plus"></i>
                    <?= t('Add', 'Добавить') ?>
                </button>
            </td>
        </tr>
    </form>
    <form method="POST" action="?setting">
        <tr>
            <td>
                <i class="fas fa-eye"></i>
                <?= t('Public', 'Публичные') ?>

            </td>
            <td>
                <input <?= $comp->show ? 'checked' : '' ?> type="radio" name="show" value="1"/>
                <?= t('displayed in the competition list', 'отображаются в списке соревнований') ?>
            </td>
        </tr>
        <tr>
            <td>
                <i class="far fa-eye-slash"></i>
                <?= t('Private', 'Спрятанные') ?>
            </td>
            <td>
                <input <?= !$comp->show ? 'checked' : '' ?> type="radio" name="show" value="0"/>
                <?= t('only visible via the link (for your testing or fun)', 'видны только по ссылке (для тестирования или развлечения)') ?>
            </td>
        </tr>
        <tr>
            <td><?= t('Name', 'Название') ?></td> 
            <td><input required style="width:500px" type="text" name="name" value="<?= $comp->name ?>" /></td>
        </tr>
        <tr>
            <td><?= t('City', 'Город') ?></td> 
            <td><input style="width:500px" type="text" name="city" value="<?= $comp->city ?>" /></td>
        </tr>
        <tr>
            <td><?= t('Details', 'Описание') ?></td>
            <td>
                <textarea name="details" style="width:500px; height:100px; " ><?= htmlspecialchars($comp->details) ?></textarea>
                <?= t('support', 'можно использвать') ?> <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>
            </td>
        </tr>
        <tr>
            <td><?= t('Logo', 'Логотип') ?></td>
            <td>
                <?php if ($comp->logo) { ?>
                    <img src="<?= $comp->logo ?>" width="50px"><br>
                <?php } ?>
                <input style="width:500px" type="url" name="logo" placeholder=" <?= t('Link to the picture', 'Ссылка на картинку') ?>" pattern="http[s]?://.*" value="<?= htmlspecialchars($comp->logo) ?>" /></td>
        </tr>
        <tr>
            <td><?= t('Date', 'Дата') ?></td>
            <td>
                <input required  style="width:140px" type="text" id="datepicker_from" name="date" value="<?= date('d.m.Y', strtotime($comp->date)) ?>">
                -
                <input  style="width:140px" type="text" id="datepicker_to" name="date_to" value="<?= ($comp->date_to ?? false) ? date('d.m.Y', strtotime($comp->date_to)) : false ?>">
            </td>
        </tr>
        <tr>
            <td><?= t('Website', 'Сайт') ?></td>
            <td>
                <input style="width:500px" type="url" placeholder="https://example.com" pattern="http[s]?://.*" name="website" value="<?= $comp->website ?>">
                <?= unofficial\getFavicon($comp->website, false) ?>
            </td>
        </tr>        
        <tr>
            <td>
                <i class="fas fa-user-plus"></i>
                <?= t('Self-registration', 'Самостоятельная регистрация') ?>
            </td>
            <td>
                <input type="checkbox" <?= $comp->secretRegistration ? 'checked' : ''; ?> name="registration">
                <?= t('competitors can register themselves (without authorization)', 'участники регистрируются самостоятельно (без авторизации)') ?>
            </td>
        </tr>
        <?php
        if ($comp->secretRegistration) {
            $link = (config::isLocalhost() ? 'http:' : 'https:') . PageIndex() . "competitions/$comp->secret/registration/$comp->secretRegistration";
            ?>
            <tr>
                <td>
                </td>
                <td>
                    <?= t('link for self-registration', 'ссылка на самостоятельную регистрацию') ?>
                    <br>
                    <a target='_blank' href="<?= $link ?>"><?= $link ?></a>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td>
            </td>
            <td>
                <button>
                    <i class="fas fa-save"></i>
                    <?= t('Save', 'Сохранить') ?>
                </button>
            </td>
        </tr>
    </form>
</table>

<?php
if ($comp_data->competition->delete) {
    ?>
    <form method="POST" action="?delete" onsubmit="return confirm('<?= t('Delete competition', 'Удалить соревнование') ?> <?= $comp->name ?>?')">
        <button class="delete">
            <i class="fas fa-trash"></i>
            <?= t('Delete', 'Удалить') ?>
        </button>
    </form>    
<?php } ?>