<?php
Mosaic::Init();

if (Mosaic::$step_name != Mosaic::StepPreparation and Mosaic::$step_name != Mosaic::StepPicture) {
    $FileCut = new Image(Mosaic::$fileNameCut);
}
?>
<div class="shadow" > 
    <h2>Upload the image. Get the PDF. Create picture. </h2>
    <?php if (Mosaic::$step_name != Mosaic::StepPreparation) { ?>
        <div class="shadow_inline">
            <form style="vertical-align: middle; float: left;" method="post" enctype="multipart/form-data" action="?reset">
                <button class="delete">
                    <i class="fas fa-backspace"></i>
                </button>
                <?= Mosaic::$cubes ?> <?= Mosaic::$color == 'W' ? 'white' : 'black' ?> cubes <?= Mosaic::$pixels . "x" . Mosaic::$pixels . "x" . Mosaic::$pixels ?>
                <?php if (Mosaic::$step_name != Mosaic::StepPreparation and Mosaic::$step_name != Mosaic::StepPicture) { ?>
                    &#9642; <?= $FileCut->cube_width ?> x <?= $FileCut->cube_height ?> = used <?= $FileCut->cube_total ?> cubes&nbsp;
                <?php } ?>
                <?php if (Mosaic::$step_name == Mosaic::StepGeneration) { ?>
                    &#9642; <?= Mosaic::$widthCubes_pdf ?> by <?= Mosaic::$heightCubes_pdf ?> cubes on sheet with "<?= array_search(Mosaic::$pdfImages, Mosaic::$pdfImagesVars) ?>" pics
                <?php } ?>
            </form>                    
            <?php if (Mosaic::$step == 1 and Mosaic::$step_name == Mosaic::StepChoosing) { ?>                        
                <form style="float: left" method="post" enctype="multipart/form-data" name="LoadImage" action="?image">
                    &#9642;
                    <input  type="file" name="uploadfile">
                    <input type="submit" value="Reload">
                </form> 
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="shadow_inline_current">
            <form style="float: left" method="post" action="?setting">
                <input value="<?= Mosaic::$cubes ?>" name="amount" min='1' max='10000' size='4'> 
                <select name="color" style="width:60px">
                    <option value="W" <?= Mosaic::$color == 'W' ? 'selected' : '' ?>>white</option>
                    <option value="B" <?= Mosaic::$color == 'B' ? 'selected' : '' ?>>black</option>
                </select>
                cubes
                <select name="pixels" style="width:80px">
                    <?php for ($c = 2; $c <= Mosaic::MAX_PIXELS; $c++) { ?>
                        <option value="<?= $c ?>" <?= Mosaic::$pixels == $c ? 'selected' : '' ?>><?= $c . "x" . $c . "x" . $c ?></option>
                    <?php } ?>
                </select>
                <button>
                    <i class="far fa-caret-square-right"></i>
                </button>
            </form>
        </div>
    <?php } ?> 

    <?php if (Mosaic::$step_name == Mosaic::StepPicture) { ?> 
        <div class="shadow_inline_current">       
            <form style="float: left" method="post" enctype="multipart/form-data" action="?image">
                <input type="file" name="uploadfile">
                <button>
                    <i class="far fa-caret-square-right"></i>
                </button>
            </form>          
        </div>
    <?php } ?> 
    <?php if (Mosaic::$step_name == Mosaic::StepGeneration or ( !in_array('_', Mosaic::$customColorsSchema) and Mosaic::$step == 1)) {
        ?>
        <div class="shadow_inline_current">
            <form style="float: left" method="post" action="?display">
                <select name="widthCubes_pdf" style="width:40px">
                    <?php for ($i = 1; $i < 10; $i++) { ?>
                        <option <?= $i == Mosaic::$widthCubes_pdf ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>
                        <?php } ?>
                </select> wide and  
                <select name="heightCubes_pdf" style="width:40px">
                    <?php for ($i = 1; $i < 10; $i++) { ?>
                        <option <?= $i == Mosaic::$heightCubes_pdf ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>
                        <?php } ?>
                </select> high cubes on sheet with pics 
                <select name="pdf_images"  style="width:70px">
                    <?php foreach (Mosaic::$pdfImagesVars as $name => $var) { ?>
                        <option <?= Mosaic::$pdfImages == $var ? 'selected' : '' ?> value="<?= $var ?>"><?= $name ?>
                        <?php } ?>
                </select>
                <?php foreach (Mosaic::$colors as $c => $tmp) { ?>
                    <img class="border" valign="middle" width=20px src='<?= PageIndex() ?>Pages/mosaic/image/<?= $c ?>_<?= Mosaic::$pdfImages ?>.png'>
                <?php } ?>
                <button>
                    <i class="far fa-save"></i>
                </button>
            </form>
        </div>   
    <?php } ?>     

    <?php if (Mosaic::$step == 1 and Mosaic::$step_name == Mosaic::StepChoosing) { ?>     
        <div class="shadow_inline_current" id="custom_schema"  <?= Mosaic::$customColorsSchema == ['_', '_', '_', '_', '_', '_'] ? 'style="display: none;"' : '' ?>>
            <h3>Select colors for layers</h3>
            <div class="custom_schema"  >
                <div class="CustomSchema">

                    <br>
                    <img  src="<?= PageIndex() ?><?= Mosaic::$dirName . "LayerALL.png?" . time(); ?>">
                    <br>
                    <?php for ($c = 0; $c < Mosaic::START_LAYER; $c++) { ?>
                        <?= Mosaic::$customColorsSchema[$c] ?>
                    <?php } ?>
                </div>    
                <?php
                foreach (glob(Mosaic::$dirName . "LayerL*.png") as $name) {
                    preg_match('/LayerL(.*).png/', $name, $matches);
                    $layer = $matches[1];
                    ?>
                    <div class="CustomSchema">
                        <form method='POST' action='?custom'>
                            <input hidden name='layer' value='<?= $layer ?>'>
                            <img  src="<?= PageIndex() ?><?= $name ?>?<?= time(); ?>"><br>
                            <?php foreach (Mosaic::$colors as $c => $tmp) { ?>
                                <?php if (isset(Mosaic::$customColorsSchema[$layer]) and $c == Mosaic::$customColorsSchema[$layer]) { ?>
                                    <img  class="selected color"  width=10px src='<?= PageIndex() ?>Image/mosaic/<?= $c ?>_EN.png'>
                                <?php } else { ?>
                                    <button class="custom" name='color' value='<?= $c ?>' >
                                        <img src='<?= PageIndex() ?>Image/mosaic/<?= $c ?>_.png'>
                                    </button>

                                <?php } ?>
                            <?php } ?>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>    
    <?php } ?> 

    <?php if (Mosaic::$step_name != Mosaic::StepPreparation and Mosaic::$step_name != Mosaic::StepPicture) { ?>
        <div class="Wrapper">  
            <?php
            if (!Mosaic::$step) {
                $title = "";
            } elseif (Mosaic::$step <= Mosaic::STEPS) {
                if (in_array('_', Mosaic::$customColorsSchema) or Mosaic::$step > 1) {
                    $title = "Step " . Mosaic::$step . " of " . (Mosaic::STEPS) . " &#9642; Click on the image most similar to the original";
                } else {
                    $title = "Step " . Mosaic::$step . " of " . (Mosaic::STEPS) . " "
                            . " &#9642; Click on the image if the custom scheme is correct"
                            . " <br>Or <a target='_blank' href='?action=pdf&value=" . implode(Mosaic::$customColorsSchema) . "'>generate PDF</a> with custom scheme!";
                }
            } elseif (Mosaic::$step == Mosaic::STEPS + 1) {
                $title = "Click on the image to generate a PDF";
            }
            ?>
            <?php if ($title) { ?>
                <h2><?= $title ?></h2>
                <br>
                <table width=100% cellpadding='5'>
                    <tr>
                        <td width="200px;" align="center" valign="top" border="0px;" style="border-bottom:0px;">        
                            <?php if (Mosaic::$step) { ?> 
                                <div class="wrapper_img">
                                    <img width="200px;" src="<?= PageIndex() . Mosaic::$fileNameImage . '?' . rand(); ?>" class="images"/>
                                    <div>   
                                    <?php } ?>   
                                    <?php if (Mosaic::$step == 1 and Mosaic::$step_name == Mosaic::StepChoosing) { ?>
                                        <span ID="custom_schema_select" style="border-bottom: 1px blue dotted;
                                        <?= Mosaic::$customColorsSchema != ['_', '_', '_', '_', '_', '_'] ? 'display: none;"' : '' ?>"
                                              onmouseover="this.style.cursor = 'pointer'"
                                              onclick="
                                                                  $('#custom_schema_select').hide();
                                                                  $('#custom_schema').show();">
                                            Set custom schema</span>
                                    <?php } ?>
                                    </td>
                                    <td valign="top" style="border-bottom:0px;">
                                        <?php
                                        if (Mosaic::$step <= Mosaic::STEPS and Mosaic::$step) {
                                            if (Mosaic::$color == 'W') {
                                                $Color_1 = 'W';
                                                $Color_2 = 'B';
                                            } else {
                                                $Color_1 = 'B';
                                                $Color_2 = 'W';
                                            }
                                            ?> 
                                            <?php
                                            foreach (glob(Mosaic::$dirNameStep . "/*") as $i => $name) {
                                                if ($s = strpos($name, '_pixel')) {
                                                    $code = str_replace('.png', '', substr($name, $s + 7, strlen($name) - $s));
                                                    $fileNameBorder_1 = PageIndex() . str_replace('[SCHEMA]', $code . "_" . $Color_1, Mosaic::$fileNameBorder_template);

                                                    $fileNameBorder_2 = PageIndex() . str_replace('[SCHEMA]', $code . "_" . $Color_2, Mosaic::$fileNameBorder_template);
                                                    ?>
                                                    <div style="float:left; width:260px;" 
                                                         onmouseover="document.getElementById('ImgChoose<?= $i ?>').src = '<?= $fileNameBorder_2 ?>?<?= rand() ?>'"
                                                         onmouseout="document.getElementById('ImgChoose<?= $i ?>').src = '<?= $fileNameBorder_1 ?>?<?= rand() ?>'">
                                                        <form name="ImageChoose<?= $i ?>" method="POST" action="?choose">
                                                            <input hidden name="code" value="<?= $code ?>" >
                                                            <input hidden name="step" value="<?= Mosaic::$step ?>">
                                                            <div class="wrapper_img">
                                                                <img width="250px;"
                                                                     src='<?= $fileNameBorder_1 ?>?<?= rand() ?>'
                                                                     Id='ImgChoose<?= $i ?>'
                                                                     class='choose images' 
                                                                     onclick="document.forms['ImageChoose<?= $i ?>'].submit();"/>
                                                            </div>
                                                        </form>
                                                    </div>    
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>


                                        <div style="clear:both; padding-top:10px;">
                                            <?php
                                            if (Mosaic::$step == Mosaic::STEPS + 1) {
                                                if (Mosaic::$color == 'W') {
                                                    $Color_1 = 'W';
                                                    $Color_2 = 'B';
                                                } else {
                                                    $Color_1 = 'B';
                                                    $Color_2 = 'W';
                                                }
                                                foreach (Mosaic::$images as $i => $value) {
                                                    $step = strlen($value) - Mosaic::START_LAYER + 1;
                                                    $fileNamePDF_1 = PageIndex() . str_replace(['[STEP]', '[SCHEMA]'], [$step, $value . "_" . $Color_1], Mosaic::$fileNamePDF_template);
                                                    $fileNamePDF_2 = PageIndex() . str_replace(['[STEP]', '[SCHEMA]'], [$step, $value . "_" . $Color_2], Mosaic::$fileNamePDF_template);
                                                    ?>
                                                    <div style="float:left; width:260px;"
                                                         onmouseover="document.getElementById('PdfChoose<?= $i ?>').src = '<?= $fileNamePDF_2 ?>?<?= rand() ?>'"
                                                         onmouseout="document.getElementById('PdfChoose<?= $i ?>').src = '<?= $fileNamePDF_1 ?>?<?= rand() ?>'">
                                                        <div class="wrapper_img">
                                                            <a target="_blank" href='?action=pdf&value=<?= $value ?>'>
                                                                <img src='<?= $fileNamePDF_1 ?>?<?= rand() ?>' Id="PdfChoose<?= $i ?>"  class="images pdf"/>
                                                            </a>
                                                        </div>
                                                    </div>    
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>   
                                    </td>
                                    </tr>
                                    </table>
                                <?php } ?>
                            </div>  
                        <?php } ?> 
                    </div>