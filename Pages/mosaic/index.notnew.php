<div class="shadow_inline">
    <form style="vertical-align: middle; float: left;" method="post" enctype="multipart/form-data" action="?reset">
        <button class="delete">
            <i class="fas fa-backspace"></i>
        </button>
        <?= mosaic\value::$session->amount ?>
        <?= mosaic\value::$session->color->name ?>
        cubes
        <?= mosaic\value::$session->pixel->name ?>
        <?php
        $pixel = mosaic\value::$session->pixel->value;
        ?>
        <?php if ($status == 'choose' OR $status == 'pdf') { ?>
            <?php $image = new Image(mosaic\value::$filename_cut); ?>
            &#9642; <?= $image->width / $pixel ?> x <?= $image->height / $pixel ?> = used <?= $image->width / $pixel * $image->height / $pixel ?> cubes&nbsp;
        <?php } ?>
        <?php if ($status == 'pdf') { ?>
            &#9642; <?= mosaic\value::$session->wide ?> by <?= mosaic\value::$session->high ?> cubes on sheet with "<?= mosaic\value::$session->display->name ?>" pics
        <?php } ?>
    </form>                    
    <?php
    if ($step == 1) {
        include 'index.reload.php';
    }
    ?>
</div>