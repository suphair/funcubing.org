<?php

Mosaic::Init();



if (isset($_POST['widthCubes_pdf']) and isset($_POST['heightCubes_pdf']) and is_numeric($_POST['widthCubes_pdf']) and is_numeric($_POST['heightCubes_pdf'])) {
    Mosaic::setCubes_pdf($_POST['widthCubes_pdf'], $_POST['heightCubes_pdf']);
}
if (isset($_POST['pdf_images']) and in_array($_POST['pdf_images'], Mosaic::$pdfImagesVars)) {
    Mosaic::setPdfImage($_POST['pdf_images']);
}

header('Location: ' . PageIndex() . 'MosaicBuilding');
exit;
