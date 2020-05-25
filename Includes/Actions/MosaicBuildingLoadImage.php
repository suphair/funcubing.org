<?php
Mosaic::Init();

if(!$_FILES['uploadfile']['error']){
    $Cubes=Mosaic::$cubes;
    $Color=Mosaic::$color;
    $Pixels=Mosaic::$pixels;
    Mosaic::Reset();
    Mosaic::setCubes($Cubes);
    Mosaic::setColor($Color);
    Mosaic::setPixels($Pixels);
    
    if($_FILES['uploadfile']['type'] == 'image/jpeg'){   
        if (exif_imagetype($_FILES['uploadfile']['tmp_name']) != IMAGETYPE_JPEG) {
            echo 'Ошибка Даниила Епифанова: The picture is not a jpeg. Файл похож на jpeg, но это не jpeg';
            exit();
        }
       //echo Mosaic::$fileNameImage;
        copy($_FILES['uploadfile']['tmp_name'],Mosaic::$fileNameImage);
        //exit();
    }elseif($_FILES['uploadfile']['type'] == 'image/png' ){
        $Image = imagecreatefrompng($_FILES['uploadfile']['tmp_name']);
        
        imagejpeg($Image, Mosaic::$fileNameImage);            ;
    }
    
   
   Mosaic::changeCube();
   Mosaic::setStepName(Mosaic::StepChoosing);
}       



header('Location: '. PageIndex().'MosaicBuilding');
exit;
