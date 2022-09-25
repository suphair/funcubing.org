<h2>
    <i class="fas fa-user-edit"></i> 
    <?= t('Rename WCA', 'Смена WCA имени') ?>
</h2>

<form method="POST" action="?ranking_rename_add">
    WCA ID 
    <input pattern="[0-9]{4}[A-Za-z]{4}[0-9]{2}" maxlength="10" title='NNNNXXXXNN' name='wcaid' autocomplete="off" required>
    <?= t('New name WCA', 'Новое WCA имя') ?> <input name="name" autocomplete="off" required/>        
    <button><?= t('Rename', 'Переименовать') ?></button>
</form>
<br>
<table class='table thead_stable'>
    <thead>
        <tr>
            <th>WCAID</th>
            <th><?= t('Name WCA', 'Имя WCA') ?></th>
            <th><?= t('New name WCA', 'Новое WCA имя') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach (unofficial\get_rename() as $row) {
            $person = unofficial\get_wca_person($row->wcaid);
            ?>
            <tr>
        <form method="POST" action="?ranking_rename_delete">
            <td><?= $row->wcaid ?></td>
            <td style="color:red"><?= explode(" (", $person->name ?? false)[0] ?></td>
            <td style="color:green"><?= $row->name ?></td>
            <td>
                <input hidden name="wcaid" value="<?= $row->wcaid ?>"/>
                <input hidden name="id" value="<?= $row->id ?>"/>
                <button class="delete"><?= t('Remove', 'Удалить') ?></button>
            </td>
        </form>
    </tr>
<?php } ?>
</tbody>
</table>
