<?php

Mosaic::Init();

$color = filter_input(INPUT_POST, 'color');
$pixels = filter_input(INPUT_POST, 'pixels', FILTER_VALIDATE_INT);
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);

if (in_array($color, ['W', 'B'])) {
    Mosaic::setColor($color);
}

if ($pixels >= 2 and $pixels <= Mosaic::MAX_PIXELS) {
    Mosaic::setPixels($pixels);
}

Mosaic::changeCube($amount);
Mosaic::setStepName(Mosaic::StepPicture);
