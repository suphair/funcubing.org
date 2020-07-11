<?php

Mosaic::Init();

$layer = filter_input(INPUT_POST, 'layer', FILTER_VALIDATE_INT);
$color = filter_input(INPUT_POST, 'color');

if ($layer !== NULL and $color) {
    Mosaic::$customColorsSchema[$layer] = $color;
    $_SESSION['customColorsSchema'] = Mosaic::$customColorsSchema;
    Mosaic::generatePaints();
}