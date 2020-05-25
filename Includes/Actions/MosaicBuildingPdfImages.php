<?php
include_once  'initIncluders.php';
if(isset($_POST['pdf_images']) and in_array($_POST['pdf_images'],Mosaic::$pdfImagesVars)){
    Mosaic::setPdfImage($_POST['pdf_images']);
}
header('Location:index.php');
exit;