<link href="<?= PageIndex() ?>Styles/mosaic.css?1" rel="stylesheet">
<?php
mosaic\cron();
mosaic\value::init();
$step = mosaic\value::$step->step ?? 0;
$status = mosaic\get_status();
$steps = mosaic\value::STEPS;
?>
<div class="shadow" > 
    <h2>Upload the image. Get the PDF. Create picture. 
        <i class="<?= $status == 'new' ? 'now' : 'done' ?> fas fa-cog"></i>
        <i class="<?= $status == 'load' ? 'now' : ($status == 'new' ? 'future' : 'done') ?> far fa-file-image"></i>
        <?php for ($i = 1; $i <= $steps; $i++) { ?>
            <i class="<?= ($step == $i and $status != 'pdf') ? 'now' : ($step < $i ? 'future' : 'done') ?> fas fa-hand-pointer"></i>
        <?php } ?>
        <i class="<?= $status == 'pdf' ? 'now' : 'future' ?> far fa-file-pdf"></i>
    </h2>

    <?php
    if ($status != 'new') {
        include 'index.notnew.php';
    }
    if ($status == 'new') {
        include 'index.new.php';
    }
    if ($status == 'load') {
        include 'index.load.php';
    }
    if ($status == 'pdf') {
        include 'index.display.php';
    }
    if ($status == 'choose' and $step == 1) {
        include 'index.custom.php';
    }
    if ($status == 'choose' or $status == 'pdf') {
        ?>
        <div class="Wrapper">  
            <h2>
                <?php if ($status == 'choose') { ?>
                    Step <?= $step ?> of <?= $steps ?> &#9642;
                    Click on the image most similar to the original
                <?php } ?>
                <?php if ($status == 'pdf') { ?>
                    Click on the image to generate a PDF
                <?php } ?>
            </h2>
            <?php if ($status == 'choose' or $status == 'pdf') { ?>
                <table width=100% cellpadding='5'>
                    <tr>
                        <td width="200px;" align="center" valign="top" border="0px;" style="border-bottom:0px;">        
                            <img width="200px;" src="<?= PageIndex() . mosaic\value::$filename_load . '?' . rand(); ?>" class="images"/>
                            <div>   
                                <?php if ($step == 1 and $status == 'choose') { ?>
                                    <span ID="custom_schema_select" style="border-bottom: 1px blue dotted;
                                    <?= mosaic\value::$image->custom_use ? 'display: none;"' : '' ?>"
                                          onmouseover="this.style.cursor = 'pointer'"
                                          onclick="
                                                  $('#custom_schema_select').hide();
                                                  $('#custom_schema').show();">
                                        Set custom schema</span>
                                <?php } ?>
                        </td>
                        <td valign="top" style="border-bottom:0px;">
                            <?php
                            if ($status == 'choose') {
                                foreach (mosaic\get_schemas_by_step() as $i => $schema) {
                                    $code = $schema->schema;
                                    $color = mosaic\value::$session->color->code;
                                    $fileNameBorder = PageIndex() . mosaic\value::$folder_step . "/{$code}_{$color}.png";
                                    ?>
                                    <div style="float:left; width:260px;" >
                                        <form method="POST" action="?choose">
                                            <input hidden name="code" value="<?= $code ?>" >
                                            <input hidden name="step" value="<?= $step ?>">
                                            <div class="wrapper_img">
                                                <img data-img-hover
                                                     width="250px;"
                                                     src='<?= $fileNameBorder ?>'
                                                     class='choose images choose_images' 
                                                     onclick="$(this).closest('form').submit();"/>
                                            </div>
                                        </form>
                                    </div>    
                                    <?php
                                }
                            }
                            if ($status == 'pdf') {
                                foreach (mosaic\get_fix_schemas() as $i => $schema) {
                                    $code = $schema->schema;
                                    $color = mosaic\value::$session->color->code;
                                    $fileNameBorder = PageIndex() . mosaic\value::$folder_image . "/" . $schema->step . "/{$code}_{$color}.png";
                                    ?>
                                    <div style="float:left; width:260px;">
                                        <div class="wrapper_img">
                                            <a target="_blank" href='?action=pdf&id=<?= $schema->id ?>'>
                                                <img class='images pdf pdf_images' src='<?= $fileNameBorder ?>'/>
                                            </a>
                                        </div>
                                    </div>    
                                <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </div>  
    <?php } ?> 
</div>