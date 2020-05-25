<?php
    Mosaic::Init();
    //$Image = new Image(Mosaic::$fileNameImage); 
    Mosaic::Reset(); 
    //$Image->Save(Mosaic::$fileNameImage);
    //Mosaic::changeCube();
    //Mosaic::setStep(1);
    //BasePage();

    header('Location: '. PageIndex().'MosaicBuilding');
    exit;

