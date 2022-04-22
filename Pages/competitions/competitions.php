<br>
<?php
if (!($me->wca_id ?? FALSE)) {
    ?>    
    <h3>
        <i class="error far fa-hand-paper"></i> 
        <?=
        t('To create competition you need to sign in with WCA and have a WCA ID.',
                'Для создания соренования вам нужно войти через WCA и иметь WCA ID.')
        ?>
    </h3>
<?php } else { ?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
        });
    </script>

    <form method="POST" action="?create">
        <b><?= t('Create competition', 'Создать соревнование') ?></b> 
        <input required placeholder="RamenskoeMeeting #1" type="text" name="name" value="" />
        <input style="width:140px" placeholder="<?= t('Select date', 'Выберите дату') ?>" required type="text" id="datepicker" name="date">
        <button>
            <i class="fas fa-plus-circle"></i> 
            <?= t('Create', 'Создать') ?>
        </button>
    </form>
    <i class="fas fa-info-circle"></i> <?=
    t('Competitions is created privately.
    You can make them public later in the settings.
    Or leave them hidden for your testing or fun.',
            'Соревнования создаются приватными. Вы можете сделать их публичными позже. 
            Или оставить спрятанными для тестирования или развлечения.')
    ?>
<?php } ?>
<hr>
<br>
<?php $mine = ($me and filter_input(INPUT_GET, 'show') == 'mine'); ?>
<?php $competitions = unofficial\getCompetitions($me, $mine); ?>
<?php
$owners = [];
foreach ($competitions as $competition) {
    $owners[$competition->competitor] = $competition->competitor_name;
}
asort($owners);
?>
<p>
    <?php if ($mine) { ?>
        <i class="far fa-eye"></i>
        <a href="?show=all">
            <?= t('Show all', 'Показать все') ?>
        </a>
    <?php } elseif ($me) { ?>
        <i class="fas fa-crown"></i>
        <a href="?show=mine"><?= t('Show only mine', 'Показать только мои') ?></a>&nbsp;
    <?php } ?>
    <?php if (!$mine) { ?>
        <i class="fas fa-user-tie"></i>
        <select data-owner-select>
            <option value='0' selected><?= t('All organizers', 'Все организаторы') ?></option>
            <?php foreach ($owners as $id => $name) { ?>
                <option value='<?= $id ?>'>
                    <?= $name ?>
                </option>    
            <?php } ?>
        </select>
    <?php } ?>
    | 
    <a href="<?= PageIndex() ?>competitions/rankings" title="Rankings"> 
        <?= $ranked_icon ?>
        <?= t('Rankings', 'Рейтинг') ?>
    </a> 
</p>
<table class='table_new'>
    <thead>
        <tr>
            <td/>
            <td>
                <?= t('Organizer', 'Организатор') ?>

            </td>
            <td>
                <?= t('Competition', 'Наименование') ?>

            </td>
            <td/>
            <td>
                <?= t('Date', 'Дата') ?>

            </td>
            <td>
                <?= t('Web site', 'Сайт') ?>

            </td>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($competitions as $competition) { ?>
            <tr data-owner='<?= $competition->competitor ?>'>   
                <td align="left" >
                    <?php if (!$competition->show) { ?>
                        <i class="far fa-eye-slash"></i>
                    <?php } ?>
                    <?= ($competition->without_FCID and $competition->ranked and unofficial\admin()) ? '<span style="color:red"><i class="fas fa-user-check"></i></span>' : '' ?>
                    <?php if ($competition->my) { ?>
                        <i class="fas fa-crown"></i>
                    <?php } elseif ($competition->organizer) { ?>
                        <i class="fas fa-user-tie"></i>
                    <?php } ?>
                    <?php if ($competition->ranked) { ?>
                        <?= $ranked_icon ?>
                    <?php } ?>
                </td>
                <td>
                    <?= $competition->competitor_name ?>
                </td>   
                <td>                    
                    <a href="<?= PageIndex() ?>competitions/<?= $competition->secret ?>"><?= $competition->name ?> </a>
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
                    <?php unofficial\getFavicon($competition->website, false) ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>  

<script>
<?php include 'competitions.js' ?>
</script>