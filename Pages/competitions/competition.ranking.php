<?php
$judges = unofficial\getRankedJudges();
$judge_roles = unofficial\getJudgeRolesDict();
?>

<h2>
    <i class="fas fa-cog"></i>
    Настройки для представителя Федерации Спидкубинга
</h2>
<form method="POST" action="?rankings_settings">
    <input hidden name='return_refer' value="<?= PageIndex() . "competitions/" . $comp->secret_base . "/ranking" ?>">
    <table class="table_info">
        <tr>
            <td>Включить в рейтинг Федерации Спидкубинга</td>
            <td><input name="ranked" type="checkbox" <?= $comp->ranked ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td>Идентификатор</td>
            <td><input placeholder="RamenskoeOpen2017" name="rankedID" value="<?= $comp->rankedID ?>"></td>
        </tr>
        <?php
        $s = 1;
        foreach ($comp_data->judges as $comp_judge) {
            ?>
            <tr>
                <td>Cудья / Роль</td>
                <td>
                    <select name="judges[<?= $s ?>]">
                        <option value=0> - </option>
                        <?php
                        foreach ($judges as $judge) {
                            ?>
                            <option  value="<?= $judge->wcaid ?>" <?= $comp_judge->wcaid == $judge->wcaid ? 'selected' : '' ?>><?= $judge->name ?> - <?= $judge->rank ?></option>
                            <?php
                        }
                        ?>
                    </select> /
                    <select name="judges_role[<?= $s ?>]">
                        <option value=0> - </option>
                        <?php
                        foreach ($judge_roles as $judge_role) {
                            ?>
                            <option  value="<?= $judge_role->id ?>"  <?= $comp_judge->role == $judge_role->role ? 'selected' : '' ?>><?= $judge_role->role ?></option>
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
            <td>Cудья (<?= $s ?>)</td>
            <td>
                <select name="judges[<?= $s ?>]">
                    <option value=0> - </option>
                    <?php
                    foreach ($judges as $judge) {
                        ?>
                        <option  value="<?= $judge->wcaid ?>"><?= $judge->name ?> - <?= $judge->rank ?></option>
                        <?php
                    }
                    ?>
                </select> /
                <select name="judges_role[<?= $s ?>]">
                    <?php
                    foreach ($judge_roles as $judge_role) {
                        ?>
                        <option  value="<?= $judge_role->id ?>"><?= $judge_role->role ?></option>
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
<a href="<?= PageIndex() ?>competitions/rankings/judges">Управление судьями Федерации Спидкубинга</a>
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


