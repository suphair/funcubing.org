<div class="shadow_inline_current">            
    <form style="float: left" method="post" action="?setting">
        <input type='number' value='<?= mosaic\value::$session->amount ?>' name="amount" min='1' max='10000' size='4'> 

        <select name="color" style="width:60px">
            <?php foreach (mosaic\dict_colors() as $color) { ?>
                <option value="<?= $color->code ?>" <?= $color->code == mosaic\value::$session->color->code ? 'selected' : '' ?>>
                    <?= $color->name ?>
                </option>
            <?php } ?>
        </select>
        cubes
        <select name="pixel" style="width:80px">
            <?php foreach (mosaic\dict_pixels() as $pixel) { ?>
                <option value="<?= $pixel->code ?>" <?= $pixel->code == mosaic\value::$session->pixel->code ? 'selected' : '' ?>>
                    <?= $pixel->name ?>
                </option>
            <?php } ?>
        </select>
        <button>
            <i class="far fa-caret-square-right"></i>
        </button>
    </form>
</div>