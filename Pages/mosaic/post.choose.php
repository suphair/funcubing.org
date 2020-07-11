<?php
Mosaic::Init();
$step = filter_input(INPUT_POST, 'step', FILTER_VALIDATE_INT);
$code = filter_input(INPUT_POST, 'code');

if ($step > 0 and $step <= Mosaic::STEPS) {
    Mosaic::AddImage($step, $code);
    Mosaic::generatePaints($code);
}
if (Mosaic::$step == Mosaic::START_LAYER) {
    Mosaic::setStepName(Mosaic::StepGeneration);
}
