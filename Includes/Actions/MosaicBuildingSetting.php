<?php

Mosaic::Init();

if(isset($_POST['color']) and in_array($_POST['color'],['W','B'])){
    Mosaic::setColor($_POST['color']);
}


if(isset($_POST['Pixels']) and is_numeric($_POST['Pixels'])){
    if($_POST['Pixels']>=2 and $_POST['Pixels']<=Mosaic::MAX_PIXELS){
        Mosaic::setPixels($_POST['Pixels']);
    }
}

Mosaic::changeCube();
Mosaic::setStepName(Mosaic::StepPicture);
header('Location: '. PageIndex().'MosaicBuilding');
exit;
