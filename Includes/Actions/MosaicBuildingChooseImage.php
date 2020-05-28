<?php

Mosaic::Init();

if ($_POST['Step'] > 0 and $_POST['Step'] <= STEPS) {
    //print_r($_POST);
    //exit();
    Mosaic::AddImage($_POST['Step'], $_POST['Code']);
    Mosaic::generatePaints();
}
if (Mosaic::$step == Mosaic::START_LAYER) {
    Mosaic::setStepName(Mosaic::StepGeneration);
}

header('Location: ' . PageIndex() . 'MosaicBuilding');
exit;
