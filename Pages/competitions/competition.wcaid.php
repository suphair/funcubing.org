<?php
foreach ($comp_data->competitors as $c => $competitor) {
    if ($competitor->wcaid) {
        $wca_person = unofficial\get_wca_person($competitor->wcaid);
        $comp_data->competitors[$c]->person = $wca_person;
    }
    if (!$competitor->FCID) {
        unset($comp_data->competitors[$c]);
    }
}
?>

<h2>
    <?= $wca_icon ?>
    <?= t('Binding to WCA', 'Привязка к WCA') ?>
</h2>
<form method='POST' action="?ranking_competitors">
    <table class="table_new">
        <thead>
            <tr>
                <td>Данные FC: Имя</td>
                <td>FC ID</td>
                <td>WCA ID</td>
                <td>Нет на WCA</td>
                <td>Данные WCA: Имя</td>
                <td>Страна</td>
                <td>Пол</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comp_data->competitors as $competitor) { ?>
                <tr>
                    <td>
                        <?php if ($competitor->FCID) { ?>
                            <a target='_blank' href='<?= pageIndex() ?>competitions/rankings/competitor/<?= $competitor->FCID ?>'>
                                <?= $competitor->name ?>
                            </a>
                        <?php } ?>
                    </td>
                    <td>    
                        <?= $competitor->FCID ?>                        
                    </td>
                    <td>
                        <input pattern="[0-9]{4}[A-Za-z]{4}[0-9]{2}" maxlength="10" title='NNNNXXXXNN' name='competitor[<?= $competitor->FCID ?>][wcaid]' value='<?= $competitor->wcaid ?>'>
                    </td>
                    <td>
                        <input name='competitor[<?= $competitor->FCID ?>][nonwca]' type='checkbox' <?= $competitor->nonwca ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <?php if ($competitor->person->wca_id ?? false) { ?>
                            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $competitor->person->wca_id ?>'>
                                <?= $competitor->person->name ?>
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?= $competitor->person->country_iso2 ?? null ?>
                    </td>
                    <td>
                        <?= $competitor->person->gender ?? null ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <button>Сохранить</button >
</form>