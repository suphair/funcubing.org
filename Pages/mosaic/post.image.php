<?php

if (($_FILES['uploadfile'] ?? false) and!$_FILES['uploadfile']['error']) {

    $IMAGETYPES = [
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG => 'imagecreatefrompng'];
    $IMAGETYPE = exif_imagetype($_FILES['uploadfile']['tmp_name']);
    $imagecreatefrom = $IMAGETYPES[$IMAGETYPE] ?? FALSE;
    if (!$imagecreatefrom) {
        postSet('post.image', 'Your picture is not a jpeg or png');
    } else {

        $Image = $imagecreatefrom($_FILES['uploadfile']['tmp_name']);

        $folder = mosaic\set_image_folder();
        $filename_load = mosaic\value::$filename_load;
        imagejpeg($Image, $filename_load);

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
}
