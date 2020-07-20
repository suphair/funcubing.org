<?php

$folder = mosaic\set_image_folder();
$filename_load = mosaic\value::$filename_load;

if (!$_FILES['uploadfile']['error']) {

    if ($_FILES['uploadfile']['type'] == 'image/jpeg') {
        if (exif_imagetype($_FILES['uploadfile']['tmp_name']) != IMAGETYPE_JPEG) {
            die('The picture is not a jpeg.');
        }
        copy($_FILES['uploadfile']['tmp_name'], $filename_load);
    } elseif ($_FILES['uploadfile']['type'] == 'image/png') {
        $Image = imagecreatefrompng($_FILES['uploadfile']['tmp_name']);
        imagejpeg($Image, $filename_load);
    }

    $imageLoad = new Image($filename_load);

    $pixel = mosaic\value::$session->pixel->value;
    $amount = mosaic\value::$session->amount;

    $coefficient = sqrt($amount / ($imageLoad->width * $imageLoad->height));
    $new_w = floor($coefficient * $imageLoad->width) * $pixel;
    $new_h = floor($coefficient * $imageLoad->height) * $pixel;


    if ($new_h * $new_w / $pixel / $pixel < $amount) {
        if ($new_h < $new_w and ( $new_h + $pixel) * $new_w / $pixel / $pixel <= $amount) {
            $new_h = $new_h + $pixel;
        }
        if ($new_w < $new_h and $new_h * ($new_w + $pixel) / $pixel / $pixel <= $amount) {
            $new_w = $new_w + $pixel;
        }
        if ($new_w == $new_h and ( $new_h + $pixel) * ($new_w + $pixel) / $pixel / $pixel <= $amount) {
            $new_w = $new_w + $pixel;
            $new_h = $new_h + $pixel;
        }
    }

    $imageCut = new Image(false, $new_w, $new_h);
    $imageCut->CopyResampled($imageLoad);
    $imageCut->Save(mosaic\value::$filename_cut);

    mosaic\set_layer();
    mosaic\update_custom_layers();
}
