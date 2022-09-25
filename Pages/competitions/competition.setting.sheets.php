<?php include 'competition.setting.menu.php' ?>
<?php
$sheets = unofficial\getCompetitionSheets($comp->id);
?>

<h2><?= t('Sheets settings', 'Управление вкладками') ?></h2>
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th><?= t('Сode', 'Код') ?></th>
            <th><?= t('Title', 'Заголовок') ?>        </th>
            <th>
                <?= t('Content', 'Содержимое') ?>
                (<?= t('support', 'можно использвать') ?> <i class="fab fa-markdown"></i> <a target="_blank" href="https://ru.wikipedia.org/wiki/Markdown">Markdown</a>)
            </th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sheets as $sheet) { ?>
            <tr>
        <form method="POST" action="?sheet_edit">
            <input hidden name="order" value="<?= $sheet->order ?>">
            <td><?= $sheet->order ?></td>
            <td title="<?= $sheet->user ?> <?= $sheet->datetime ?>">
                <input name="sheet" required value="<?= $sheet->sheet ?>"/>
            </td>
            <td>
                <input name="title" required value="<?= $sheet->title ?>"/>
            </td>
            <td><textarea name="content" style="width:600px; height:100px; "><?= $sheet->content ?></textarea></td>
            <td><button><?= t('Save', 'Сохранить') ?></button>
                <br><br>
        </form>
        <form method="POST" action="?sheet_delete" onsubmit="return confirm('<?= t('Delete sheet', 'Удалить вкладку') ?> <?= $sheet->sheet ?>?')">
            <input hidden name="order" value="<?= $sheet->order ?>">
            <button class="delete">
                <?= t('Delete', 'Удалить') ?>
            </button>
        </form>
    </td>
    </tr>

<?php } ?>
<form method="POST" action="?sheet_create">
    <tr/>
    <tr>
        <td></td>
        <td>
            <input name="sheet" required/>
        </td>
        <td><input name="title" required/></td>
        <td><textarea name="content" style="width:600px; height:100px; "></textarea></td>
        <td><button><?= t('Create', 'Создать') ?></button></td>
    </tr>
</form>

</tbody>
</table>


