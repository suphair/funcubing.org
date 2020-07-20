<div class="shadow_inline_current" id="custom_schema"  <?= !mosaic\value::$image->custom_use ? 'style="display: none;"' : '' ?>>
    <h3>Select colors for layers</h3>
    <div class="custom_schema"  >
        <div class="CustomSchema">
            <br>
            <img  src="<?= mosaic\value::$folder_layers ?>\all.png?<?= time() ?>">
            <br>
            <?php foreach (str_split(mosaic\value::$image->custom) as $color) { ?>
                <?= $color ?>
            <?php } ?>
        </div>    
        <?php foreach (str_split(mosaic\value::$image->custom) as $layer => $color) { ?>
            <div class="CustomSchema">
                <form method='POST' action='?custom'>
                    <input hidden name='layer' value='<?= $layer ?>'>
                    <img  src="<?= mosaic\value::$folder_layers ?>\<?= $layer ?>.png?<?= time() ?>"><br>
                    <?php foreach (mosaic\value::$colors as $c => $tmp) { ?>
                        <?php if ($c == $color) { ?>
                            <img  class="selected color"  width=10px src='<?= PageIndex() ?>Pages/mosaic/image/<?= $c ?>_EN.png'>
                        <?php } else { ?>
                            <button class="custom" name='color' value='<?= $c ?>' >
                                <img src='<?= PageIndex() ?>Pages/mosaic/image/<?= $c ?>_.png'>
                            </button>

                        <?php } ?>
                    <?php } ?>
                </form>
            </div>
        <?php } ?>
    </div>
</div> 