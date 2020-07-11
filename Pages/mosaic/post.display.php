<?php

Mosaic::Init();
$widthCubes_pdf = filter_input(INPUT_POST, 'widthCubes_pdf', FILTER_VALIDATE_INT);
$heightCubes_pdf = filter_input(INPUT_POST, 'heightCubes_pdf', FILTER_VALIDATE_INT);
$pdf_images = filter_input(INPUT_POST, 'pdf_images');

if ($widthCubes_pdf and $heightCubes_pdf) {
    Mosaic::setCubes_pdf($widthCubes_pdf, $heightCubes_pdf);
}

if (in_array($pdf_images, Mosaic::$pdfImagesVars)) {
    Mosaic::setPdfImage($pdf_images);
}
