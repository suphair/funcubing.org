<?php

namespace mosaic;

function cron() {
    $details = ['clear' => 0];
    $sessions = \db::rows("
select id, folder, timestamp, TIMESTAMPDIFF(hour, timestamp, current_timestamp) diff
from
(
select 
greatest(max(`mosaic_session`.`timestamp`),
max(`mosaic_images`.`timestamp`), max(`mosaic_steps`.`timestamp`), max(`mosaic_schemas`.`timestamp`) ) `timestamp`,
mosaic_session.id, mosaic_session.folder
from `mosaic_session`
left outer join `mosaic_images` on `mosaic_images`.`session_id` = `mosaic_session`.`id`
left outer join `mosaic_steps` on `mosaic_steps`.`image_id` = `mosaic_images`.`id`
left outer join  `mosaic_schemas` on `mosaic_schemas`.`step_id` = `mosaic_steps`.`id`
group by mosaic_session.id,mosaic_session.folder )t
");

    foreach ($sessions as $session) {
        if ($session->diff > 24) {
            \db::exec("DELETE mosaic_schemas FROM mosaic_schemas "
                    . " join `mosaic_steps` on `mosaic_steps`.`id` = `mosaic_schemas`.`step_id`"
                    . " join  `mosaic_images` on `mosaic_images`.`id` = `mosaic_steps`.`image_id`"
                    . " where mosaic_images.session_id = {$session->id}");

            \db::exec("DELETE mosaic_steps FROM mosaic_steps "
                    . " join  `mosaic_images` on `mosaic_images`.`id` = `mosaic_steps`.`image_id`"
                    . " where mosaic_images.session_id = {$session->id}");

            \db::exec("DELETE FROM mosaic_images where mosaic_images.session_id = {$session->id}");
            \db::exec("DELETE FROM mosaic_session where id = {$session->id}");
            $details['clear']++;
            deleteDir('Images/mosaic/' . $session->folder);
        }
    }
    return json_encode($details);
}

function deleteDir($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    foreach (getFiles($dir) as $file) {
        is_dir("$dir/$file") ?
                        deleteDir("$dir/$file") :
                        @unlink("$dir/$file");
    }
    return rmdir($dir);
}

function getFiles($dir) {
    return array_diff(
            scandir($dir)
            , ['.', '..']
    );
}

function rand_str($length = 8) {
    $chars = 'ABDEFGHKNQRSTYZ23456789';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, strlen($chars)) - 1, 1);
    }
    return $string;
}

function dict_colors() {
    return $dict_rows = \db::rows("Select * from mosaic_colors_dict order by `order`");
}

function dict_pixels() {
    return $dict_rows = \db::rows("Select * from mosaic_pixels_dict order by `order`");
}

function dict_displays() {
    return $dict_rows = \db::rows("Select * from mosaic_displays_dict order by `order`");
}

function update_session($color, $pixel, $amount) {
    $color = \db::escape($color);
    $pixel = \db::escape($pixel);
    $amount = \db::escape($amount);
    if ($amount > 0) {
        \db::exec("UPDATE IGNORE mosaic_session SET amount = '$amount', color_dict = '$color', pixel_dict = '$pixel', setting_fix = true WHERE id = '" . value::$session->id . "'");
    }
}

function update_display($wide, $high, $display) {
    $wide = \db::escape($wide);
    $high = \db::escape($high);
    $display = \db::escape($display);
    if ($wide > 0 and $high > 0) {
        \db::exec("UPDATE IGNORE mosaic_session SET wide = '$wide', high = '$high', display_dict = '$display' WHERE id = '" . value::$session->id . "'");
    }
}

function get_rows_dict_by_session($dict) {
    check_dict($dict);
    $selected = get_row_dict_by_session($dict)->code;
    $dict_rows = \db::rows("Select * from mosaic_{$dict}s_dict order by `order`");
    foreach ($dict_rows as $d => $dict_row) {
        $dict_rows[$d]->selected = ($dict_row->code == $selected);
    }
    return $dict_rows;
}

function check_dict($dict) {
    if (!in_array($dict, ['pixel', 'color'])) {
        trigger_error($dict);
    }
}

function get_status() {
    $session = value::$session;
    $image = value::$image;

    if ($image->choose_complete ?? FALSE) {
        $status = 'pdf';
    } elseif ($image) {
        $status = 'choose';
    } elseif ($session->setting_fix) {
        $status = 'load';
    } else {
        $status = 'new';
    }
    return $status;
}

function set_session_folder() {
    $session_folder = rand_str();
    $folder = "Images/mosaic/" . $session_folder;
    if (!file_exists($folder)) {
        mkdir($folder);
    }
    \db::exec("UPDATE mosaic_session SET folder = '$session_folder' WHERE id = '" . value::$session->id . "'");
    return $folder;
}

function set_image_folder() {
    $folder_image = rand_str();
    $folder = value::$folder_session . "/" . $folder_image;
    if (!file_exists($folder)) {
        mkdir($folder);
    }
    deactivate_images();
    $custom = str_repeat("_", value::$first_len);
    \db::exec("INSERT INTO mosaic_images (session_id,folder,active,custom) VALUES ('" . value::$session->id . "','$folder_image',1,'$custom')");
    value::init();
}

function deactivate_images() {
    \db::exec("UPDATE mosaic_images SET active = false WHERE session_id = '" . value::$session->id . "'");
}

function setting_fix($fix) {
    \db::exec("UPDATE mosaic_session SET setting_fix = " . (boolval($fix) + 0) . " WHERE id = '" . value::$session->id . "'");
}

function set_layer($schema = false) {
    $image = value::$image;
    if (!$schema) {
        $step = 0;
    } else {
        $step = value::$step->step;
        \db::exec("UPDATE mosaic_schemas"
                . " JOIN mosaic_steps ON mosaic_schemas.step_id = mosaic_steps.id"
                . " SET fix = true "
                . " WHERE mosaic_steps.image_id = $image->id "
                . " AND mosaic_steps.step = $step"
                . " AND `schema` = '$schema'");
    }

    if ($step == value::STEPS) {
        \db::exec("UPDATE mosaic_images SET choose_complete = 1 WHERE id = $image->id");
        value::init();
        return;
    }
    $step++;
    $schemas = get_schemas($schema);
    $folder_image = value::$folder_image;
    $folder_step = "$folder_image/$step";
    if (!file_exists($folder_step)) {
        mkdir($folder_step);
    }

    $image_cut = new \Image(value::$filename_cut);
    $color_count = strlen($schemas[0]);
    imagetruecolortopalette($image_cut->image, false, $color_count);
    $filename_pix = "$folder_step/pix.png";
    $image_cut->Save($filename_pix);
    \db::exec("INSERT INTO mosaic_steps (image_id, step) VALUES ('$image->id', $step)");
    $step_id = \db::id();

    $image_pix = new \Image($filename_pix);
    $colors_index = $image_pix->getColorsIndex($color_count);
    $image_pix->SetColors();

    value::init();
    foreach ($schemas as $schema) {
        add_schema($schema, $step_id);
    }
    value::init();
}

function add_schema($schema, $step_id, $is_custom = 0) {
    if ($is_custom) {
        $id = \db::row("SELECT id FROM mosaic_schemas WHERE step_id = $step_id AND `schema` = '$schema'")->id ?? FALSE;
        if ($id) {
            \db::exec("UPDATE mosaic_schemas SET is_custom = 1 WHERE id = $id");
            return;
        }
    }

    $Image = new \Image(value::$filename_pix);
    $color_count = strlen($schema);
    $colors_index = $Image->getColorsIndex($color_count);
    $Image->SetColors();
    $width = $Image->width;
    $height = $Image->height;
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $rgb = ImageColorAt($Image->image, $x, $y);
            $rgb_arr = imagecolorsforindex($Image->image, $rgb);
            $color_index = $colors_index[$rgb_arr['red']][$rgb_arr['green']][$rgb_arr['blue']];
            $Image->SetPixelColorIndex($x, $y, substr($schema, $color_index, 1));
        }
    }

    $filename_schema = value::$folder_step . "/" . $schema . ".png";
    $Image->Save($filename_schema);

    $border = 1;
    $cell = 7;
    $BC = $cell + $border;

    $new_w = $width * $BC + $border;
    $new_h = $height * $BC + $border;

    $ImageBorder = new \Image(false, $new_w, $new_h);
    $ImageBorder->CopyResampled($Image);
    $color_borders = [];

    foreach (dict_colors() as $color) {
        $ImageBorder->SetColor(...json_decode($color->border));

        for ($w = 0; $w <= $width; $w++) {
            $ImageBorder->FilledRectangle($BC * $w, 0, $border + $BC * $w, $new_h);
        }
        for ($h = 0; $h <= $height; $h++) {
            $ImageBorder->FilledRectangle(0, $BC * $h, $new_w, $border + $BC * $h);
        }
        $filename_border = value::$folder_step . "/{$schema}_{$color->code}.png";
        $ImageBorder->Save($filename_border);
    }

    \db::exec("INSERT INTO mosaic_schemas (step_id, `schema`,is_custom) VALUES ('$step_id','$schema',$is_custom)");
}

function get_fix_schemas() {
    return \db::rows("SELECT "
                    . "  mosaic_schemas.`schema`,"
                    . " mosaic_schemas.`id`,"
                    . " mosaic_steps.step "
                    . " FROM mosaic_schemas "
                    . " JOIN mosaic_steps ON mosaic_steps.id = mosaic_schemas.step_id"
                    . " WHERE mosaic_schemas.fix = 1 AND mosaic_steps.image_id = " . value::$image->id);
}

function update_custom_layers() {
    $folder_image = value::$folder_image;
    $folder_layers = value::$folder_layers;
    $filename_pix = "$folder_image/1/pix.png";
    $image_pix = new \Image($filename_pix);
    $color_count = value::$first_len;
    $colors_index = $image_pix->getColorsIndex($color_count);
    $image_pix->SetColors();

    $image_layers = [];
    for ($i = 0; $i < $color_count; $i++) {
        $image_layers[$i] = new \Image(false, $image_pix->width, $image_pix->height);
        $image_layers[$i]->SetColor('230', '230', '230');
        $image_layers[$i]->Filled();
    }
    $image_layers_all = new \Image(false, $image_pix->width, $image_pix->height);

    $customs = str_split(value::$image->custom);

    for ($x = 0; $x < $image_pix->width; $x++) {
        for ($y = 0; $y < $image_pix->height; $y++) {
            $rgb = ImageColorAt($image_pix->image, $x, $y);
            $rgb_arr = imagecolorsforindex($image_pix->image, $rgb);
            $color_index = $colors_index[$rgb_arr['red']][$rgb_arr['green']][$rgb_arr['blue']];
            if (isset($customs[$color_index]) and isset(value::$colors[$customs[$color_index]])) {
                $cs = value::$colors[$customs[$color_index]];
                $image_layers_all->SetColor($cs[0], $cs[1], $cs[2]);
                $image_layers[$color_index]->SetColor($cs[0], $cs[1], $cs[2]);
            } else {
                $image_layers_all->SetColor('230', '230', '230');
                $image_layers[$color_index]->SetColor('0', '0', '0');
            }
            $image_layers_all->SetPixelColor($x, $y);
            $image_layers[$color_index]->SetPixelColor($x, $y);
        }
    }

    foreach ($image_layers as $i => $image_layer) {
        $image_layer->Save("$folder_layers/$i.png");
    }
    $image_layers_all->Save("$folder_layers/all.png");
}

function get_schemas($schema = false) {
    $schemas = [];
    if (!$schema) {
        foreach (\db::rows("SELECT code FROM mosaic_schemas_dict") as $schema_dict) {
            $schemas[] = $schema_dict->code;
        }
    } else {

        $len = strlen($schema);
        for ($i = 0; $i < $len; $i++) {
            if ($i == 0) {
                $schemas[] = $schema[0] . $schema;
            } elseif ($i == $len - 1) {
                $schemas[] = $schema . $schema[$len - 1];
            } else {
                $schemas[] = substr($schema, 0, $i) . substr($schema, $i - 1, $len);
                $schemas[] = substr($schema, 0, $i + 1) . substr($schema, $i, $len);
            }
        }
    }
    sort($schemas);
    return array_unique($schemas);
}

function set_custom_color($layer, $color) {
    $image = value::$image;
    $customs = $image->custom;
    $customs[$layer] = $color;
    $custom_full = (strpos($customs, '_') === false) + 0;
    \db::exec("UPDATE mosaic_images SET custom = '$customs', custom_full = $custom_full, custom_use = 1 WHERE id = $image->id ");
    if ($custom_full) {
        add_schema($customs, value::$step->id, true);
    }
    value::init();
}

function get_schema_by_id($scheme_id) {
    return \db::row("SELECT mosaic_schemas.`schema`, mosaic_steps.step"
                    . " FROM mosaic_schemas "
                    . " JOIN mosaic_steps ON mosaic_steps.id = mosaic_schemas.step_id"
                    . " JOIN mosaic_images ON mosaic_images.id = mosaic_steps.image_id"
                    . " WHERE mosaic_schemas.id = $scheme_id "
                    . "AND mosaic_images.session_id = '" . value::$session->id . "'");
}

function get_schemas_by_step() {
    if (!(value::$step->id ?? FALSE)) {
        value::$step->step = 1;
    }
    if (value::$image->custom_full and value::$step->step == 1) {
        return \db::rows("SELECT mosaic_schemas.`schema`"
                        . " FROM mosaic_schemas WHERE step_id = '" . value::$step->id . "'"
                        . " AND mosaic_schemas.is_custom"
                        . " ORDER BY mosaic_schemas.`schema`");
    } else {
        return \db::rows("SELECT mosaic_schemas.`schema`"
                        . " FROM mosaic_schemas WHERE step_id = '" . value::$step->id . "'"
                        . " ORDER BY mosaic_schemas.`schema`");
    }
}

function get_cubes($image) {
    $cubes = new \stdClass();
    $cubes->onimage = new \stdClass();
    $cubes->inpixel = new \stdClass();
    $pixel = value::$session->pixel->value;
    $cubes->inpixel->width = $pixel;
    $cubes->inpixel->height = $pixel;
    $cubes->onimage->width = $image->width / $pixel;
    $cubes->onimage->height = $image->height / $pixel;
    $cubes->onimage->total = $cubes->onimage->width * $cubes->onimage->height;
    return $cubes;
}

require_once 'valueClass.php';
require_once 'imageClass.php';
