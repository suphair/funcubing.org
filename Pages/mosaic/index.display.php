<?php
$wide = mosaic\value::$session->wide;
$high = mosaic\value::$session->high;
$display = mosaic\value::$session->display;
?>
<div class="shadow_inline_current">
    <form style="float: left" method="post" action="?display">
        <select name="wide" style="width:40px">
            <?php for ($i = 1; $i < 10; $i++) { ?>
                <option <?= $i == $wide ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>
                <?php } ?>
        </select> wide and  
        <select name="high" style="width:40px">
            <?php for ($i = 1; $i < 10; $i++) { ?>
                <option <?= $i == $high ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>
                <?php } ?>
        </select> high cubes on sheet with pics 
        <select name="display"  style="width:70px">
            <?php foreach (mosaic\dict_displays() as $dict_display) { ?>
                <option <?= $dict_display->code == $display->code ? 'selected' : '' ?> value="<?= $dict_display->code ?>"><?= $dict_display->name ?>
                <?php } ?>
        </select>
        <?php foreach (mosaic\value::$colors as $c => $tmp) { ?>
            <img class="border" valign="middle" width=20px src='<?= PageIndex() ?>Pages/mosaic/image/<?= $c ?>_<?= $display->value ?>.png'>
        <?php } ?>
        <button>
            <i class="far fa-save"></i>
        </button>
    </form>
</div>  