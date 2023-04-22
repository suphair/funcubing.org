<?php
$delegates = unofficial\getRankedDelegates();
$delegate_roles = unofficial\getDelegateRolesDict();
?>

<h2>
    <i class="fas fa-cog"></i>
    <?= t('Official settings', 'Официальные настройки') ?>
</h2>
<form method="POST" action="?rankings_settings">
    <input hidden name='return_refer' value="<?= PageIndex() . "competitions/" . $comp->secret_base . "/ranking" ?>">
    <table class="table_info">
        <tr>
            <td>Это официальное соревнование</td>
            <td><input name="ranked" type="checkbox" <?= $comp->ranked ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td>Идентификатор</td>
            <td><input placeholder="RamenskoeOpen2017" name="rankedID" value="<?= $comp->rankedID ?>"></td>
        </tr>
        <tr>
            <td>Лимит участников</td>
            <td><input type="number" name="rankedCompetitors" value="<?= $comp->rankedCompetitors ?>"></td>
        </tr>
        <?php
        $s = 1;
        foreach ($comp_data->delegates as $comp_delegate) {
            ?>
            <tr>
                <td>Делегат / Роль</td>
                <td>
                    <select name="delegates[<?= $s ?>]">
                        <option value=0> - </option>
                        <?php
                        foreach ($delegates as $delegate) {
                            ?>
                            <option  value="<?= $delegate->wcaid ?>" <?= $comp_delegate->wcaid == $delegate->wcaid ? 'selected' : '' ?>><?= $delegate->name ?> - <?= $delegate->rank ?></option>
                            <?php
                        }
                        ?>
                    </select> /
                    <select name="delegates_role[<?= $s ?>]">
                        <option value=0> - </option>
                        <?php
                        foreach ($delegate_roles as $delegate_role) {
                            ?>
                            <option  value="<?= $delegate_role->id ?>"  <?= $comp_delegate->role == $delegate_role->role ? 'selected' : '' ?>><?= $delegate_role->role ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
            $s++;
        }
        ?>
        <tr>
            <td>Делегат (<?= $s ?>)</td>
            <td>
                <select name="delegates[<?= $s ?>]">
                    <option value=0> - </option>
                    <?php
                    foreach ($delegates as $delegate) {
                        ?>
                        <option  value="<?= $delegate->wcaid ?>"><?= $delegate->name ?> - <?= $delegate->rank ?></option>
                        <?php
                    }
                    ?>
                </select> /
                <select name="delegates_role[<?= $s ?>]">
                    <?php
                    foreach ($delegate_roles as $delegate_role) {
                        ?>
                        <option  value="<?= $delegate_role->id ?>"><?= $delegate_role->role ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Результаты подтверждены</td>
            <td><input name="approved" type="checkbox" <?= $comp->approved ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td>
            </td>
            <td>
                <button>
                    Сохранить
                </button>
            </td>
        </tr>
    </table> 
</form>
<i class="fas fa-user-cog"></i>
<a href="<?= PageIndex() ?>competitions/rankings/delegates">Управление делегатами</a>
<hr>
<h2>
    <i class='fas fa-user-tie'></i> 
    Имена организаторов
</h2>
Если в WCA задано имя на русском, то переименование сбросится при следующей авторизации организатора.
<table class='table_new'>
    <thead>
        <tr>
            <td>
                WCA ID
            </td>
            <td>
                Имя (EN)
            </td>
            <td>
                Имя (RU)
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        $comp_data->organizers[] = (object) [
                    'competitor_nameEN' => $comp->competitor_nameEN,
                    'competitor_nameRU' => $comp->competitor_nameRU,
                    'competitor_wcaid' => $comp->competitor_wcaid
        ];
        foreach ($comp_data->organizers as $organizer) {
            ?>
        <form method='POST' action='?organizer_rename'>
            <tr>
                <td>
                    <?= $organizer->competitor_wcaid ?>
                </td>
                <td>
                    <?= $organizer->competitor_nameEN ?>
                </td>
                <td>
                    <input name='nameRU' value='<?= $organizer->competitor_nameRU ?>'>
                </td>
                <td>
                    <button>Переименовать</button>
                </td>
            </tr>
            <input hidden name='wcaid' value='<?= $organizer->competitor_wcaid ?>'>
        </form>
    <?php } ?>

</tbody>
</table>
<hr>
<h2>
    <i class="fas fa-random"></i>
    <?= t('Scrambles', 'Скрамблы') ?>
</h2>
Interchange/*.json
<form method='POST' action='?scrambles'>
    <textarea name='json'><?= $json_scrambles->json ?? false ?></textarea>
    <input hidden name='competition' value='<?= $competition->local_id ?>'>
    <button>Сохранить</button>
</form>
<?php if ($json_scrambles ?? false) { ?>
    <a target="_blank" href="<?= PageIndex() ?>competitions/<?= $competition->id ?>/scrambles">Открыть вкладку Скрамблы</a>
<?php } ?>


