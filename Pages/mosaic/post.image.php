<?php

Mosaic::Init();

if (!$_FILES['uploadfile']['error']) {
    
    
    $Cubes = Mosaic::$cubes;
    $Color = Mosaic::$color;
    $Pixels = Mosaic::$pixels;
    Mosaic::Reset();
    Mosaic::setCubes($Cubes);
    Mosaic::setColor($Color);
    Mosaic::setPixels($Pixels);

    if ($_FILES['uploadfile']['type'] == 'image/jpeg') {
        if (exif_imagetype($_FILES['uploadfile']['tmp_name']) != IMAGETYPE_JPEG) {
            echo 'The picture is not a jpeg.';
            exit();
        }
        copy($_FILES['uploadfile']['tmp_name'], Mosaic::$fileNameImage);
        
    } elseif ($_FILES['uploadfile']['type'] == 'image/png') {
        $Image = imagecreatefrompng($_FILES['uploadfile']['tmp_name']);

        imagejpeg($Image, Mosaic::$fileNameImage);
    }


    Mosaic::changeCube();
    Mosaic::setStepName(Mosaic::StepChoosing);
}
