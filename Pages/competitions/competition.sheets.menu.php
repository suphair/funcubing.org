<?php
$sheet_get = filter_input(INPUT_GET, 'sheet');
$sheets = api\get_sheets($competition->id);
$sheet = find_element($sheets, 'code', $sheet_get, null);
?>
<h2>
    <i title="General info" class="fas fa-info-circle"></i>
    <?php foreach ($sheets ?? [] as $s) { ?>
        | <a href ="?<?= $s->code ? "sheet=$s->code" : '' ?>" class="<?= ($sheet and $s->code == $sheet->code) ? 'select' : '' ?>"><?= $s->title ?></a>
    <?php } ?>
</h2>
<br>