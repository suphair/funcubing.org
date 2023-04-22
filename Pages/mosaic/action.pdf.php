<?php

mosaic\value::init();
$scheme_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$schema = mosaic\get_schema_by_id($scheme_id);
if (!$schema) {
    die("Schema with id = [$scheme_id] is lost");
}

$file = mosaic\value::$folder_image . "/" . $schema->step . "/" . $schema->schema . ".png";
$image = new Image($file);
$pixel = mosaic\value::$session->pixel->value;
$display = mosaic\value::$session->display->value;

$image->block = new stdClass();
$image->cube = new stdClass();
$image->pixel = new stdClass();
$blocks = new stdClass();
$blocks->cubes = new stdClass();
$blocks->pixel = new stdClass();
$cubes = new \stdClass();
$cubes->pixel = new \stdClass();

$image->pixel->width = $image->width;
$image->pixel->height = $image->height;


$blocks->cubes->width = mosaic\value::$session->wide;
$blocks->cubes->height = mosaic\value::$session->high;

$cubes->pixel->width = $pixel;
$cubes->pixel->height = $pixel;

$blocks->pixel->width = $cubes->pixel->width * $blocks->cubes->width;
$blocks->pixel->height = $cubes->pixel->height * $blocks->cubes->height;

$image->block->width = ceil($image->pixel->width / $blocks->pixel->width);
$image->block->height = ceil($image->pixel->height / $blocks->pixel->height);

$image->cube->width = ceil($image->pixel->width / $pixel);
$image->cube->height = ceil($image->pixel->height / $pixel);

function center($page_width, $page_height, $count_width, $count_height) {
    if ($page_width > $page_height) {
        $padding_width_defaut = 30;
        $padding_height_defaut = 20;
    } else {
        $padding_width_defaut = 20;
        $padding_height_defaut = 30;
    }

    $width_free = $page_width - 2 * $padding_width_defaut;
    $height_free = $page_height - 2 * $padding_height_defaut;

    $size = min([$width_free / $count_width, $height_free / $count_height]);
    $padding_width = ($page_width - $size * $count_width) / 2;
    $padding_height = ($page_height - $size * $count_height) / 2;

    return (object) ['padding_width' => $padding_width, 'padding_height' => $padding_height, 'size' => $size];
}

$image->GetColors_256();

if ($image->pixel->width > $image->pixel->height) {
    $pdf = new FPDF('L', 'mm');
} else {
    $pdf = new FPDF('P', 'mm');
}



$pdf->AddPage();
$center = center($pdf->GetPageWidth()
        , $pdf->GetPageHeight()
        , $image->pixel->width
        , $image->pixel->height);

for ($h = 0; $h < $image->pixel->height; $h++) {
    for ($w = 0; $w < $image->pixel->width; $w++) {
        $color_pix = $image->colors_img_256[$w][$h];
        $pdf->SetFillColor(mosaic\value::$colors[$color_pix][0], mosaic\value::$colors[$color_pix][1], mosaic\value::$colors[$color_pix][2]);
        $pdf->Rect($w * $center->size + $center->padding_width
                , $h * $center->size + $center->padding_height
                , $center->size
                , $center->size
                , "F");
    }
}

$pdf->SetDrawColor(...json_decode(mosaic\value::$session->color->border));
$pdf->SetLineWidth(0.2);
for ($h = 0; $h < $image->cube->height; $h++) {
    for ($w = 0; $w < $image->cube->width; $w++) {
        $pdf->Rect($w * $center->size * $cubes->pixel->width + $center->padding_width
                , $h * $center->size * $cubes->pixel->height + $center->padding_height
                , $center->size * $cubes->pixel->width
                , $center->size * $cubes->pixel->height);
    }
}

$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFont('Arial', '', 18);
$pdf->Text($center->padding_width, $center->padding_height - 5, "{$image->cube->width} x {$image->cube->height} = " . ($image->cube->width * $image->cube->height) . ' / cubes ' . mosaic\value::$session->pixel->name);



$pdf->AddPage();
$center = center($pdf->GetPageWidth()
        , $pdf->GetPageHeight()
        , $image->block->width * $blocks->cubes->width * $cubes->pixel->width
        , $image->block->height * $blocks->cubes->height * $cubes->pixel->height);


for ($h = 0; $h < $image->pixel->height; $h++) {
    for ($w = 0; $w < $image->pixel->width; $w++) {
        $color_name = $image->colors_img_256[$w][$h];
        $imagename = "Pages/mosaic/image/$color_name" . "_$display.png";
        $pdf->Image(
                $imagename
                , $w * $center->size + $center->padding_width
                , $h * $center->size + $center->padding_height
                , $center->size
                , $center->size
        );
    }
}

$pdf->SetLineWidth(0.2);
$pdf->SetDrawColor(215, 215, 215);
for ($h = 0; $h < $image->block->height * $blocks->cubes->height; $h++) {
    for ($w = 0; $w < $image->block->width * $blocks->cubes->width; $w++) {
        $pdf->Rect(
                $w * $center->size * $cubes->pixel->width + $center->padding_width
                , $h * $center->size * $cubes->pixel->height + $center->padding_height
                , $center->size * $cubes->pixel->width
                , $center->size * $cubes->pixel->height);
    }
};
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(0, 0, 0);

if ($pixel > 1) {
    for ($h = 0; $h < $image->block->height; $h++) {
        for ($w = 0; $w < $image->block->width; $w++) {
            $pdf->Rect(
                    $w * $center->size * $blocks->cubes->width * $cubes->pixel->width + $center->padding_width
                    , $h * $center->size * $blocks->cubes->height * $cubes->pixel->height + $center->padding_height
                    , $center->size * $blocks->cubes->width * $cubes->pixel->width
                    , $center->size * $blocks->cubes->height * $cubes->pixel->height);
        }
    }
}


for ($h = 0; $h < $image->block->height; $h++) {
    $pdf->Text($center->padding_width - 6
            , ($h + 1 / 2) * $center->size * $blocks->cubes->height * $cubes->pixel->height + $center->padding_height + 3
            , $image->block->height - $h);
    $pdf->Text($pdf->GetPageWidth() - $center->padding_width + 2
            , ($h + 1 / 2) * $center->size * $blocks->cubes->height * $cubes->pixel->height + $center->padding_height + 3
            , $image->block->height - $h);
    $pdf->Line($center->padding_width / 2
            , ($h + 1) * $center->size * $blocks->cubes->height * $cubes->pixel->height + $center->padding_height
            , $pdf->GetPageWidth() - $center->padding_width / 2
            , ($h + 1) * $center->size * $blocks->cubes->height * $cubes->pixel->height + $center->padding_height);
}

$pdf->Line($center->padding_width / 2
        , $center->padding_height
        , $pdf->GetPageWidth() - $center->padding_width / 2
        , $center->padding_height);



$i_name = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J',
    11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U',
    21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z'];

for ($w = 0; $w < $image->block->width; $w++) {
    $pdf->Text(($w + 1 / 2) * $center->size * $blocks->cubes->width * $cubes->pixel->width + $center->padding_width
            , $center->padding_height - 3
            , $i_name[$w + 1] ?? ($w + 1));
    $pdf->Text(($w + 1 / 2) * $center->size * $blocks->cubes->width * $cubes->pixel->width + $center->padding_width
            , $pdf->GetPageHeight() - $center->padding_height + 8
            , $i_name[$w + 1] ?? ($w + 1));
    $pdf->Line(
            ($w + 1 ) * $center->size * $blocks->cubes->width * $cubes->pixel->width + $center->padding_width
            , $center->padding_height / 2
            , ($w + 1 ) * $center->size * $blocks->cubes->width * $cubes->pixel->width + $center->padding_width
            , $pdf->GetPageHeight() - $center->padding_height / 2);
}
$pdf->Line(
        $center->padding_width
        , $center->padding_height / 2
        , $center->padding_width
        , $pdf->GetPageHeight() - $center->padding_height / 2);



for ($hp = $image->block->height - 1; $hp > -1; $hp--) {
    for ($wp = 0; $wp < $image->block->width; $wp++) {
        if ($blocks->cubes->width > $blocks->cubes->height) {
            $pdf->AddPage("L");
        } else {
            $pdf->AddPage("P");
        }
        $center = center($pdf->GetPageWidth()
                , $pdf->GetPageHeight()
                , $blocks->cubes->width * $cubes->pixel->width
                , $blocks->cubes->height * $cubes->pixel->height);

        $pdf->SetFont('Arial', '', 18);
        $pdf->Text($center->padding_width, $center->padding_height / 2, ($i_name[$wp + 1] ?? ($wp + 1)) . ($image->block->height - $hp));

        $pdf->SetDrawColor(220, 220, 220);

        for ($h = 0; $h < $blocks->pixel->height; $h++) {
            for ($w = 0; $w < $blocks->pixel->width; $w++) {
                $wi = $wp * $blocks->pixel->width + $w;
                $hi = $hp * $blocks->pixel->height + $h;

                $color_name = $image->colors_img_256[$wi][$hi] ?? FALSE;
                if ($color_name) {
                    $imagename = "Pages/mosaic/image/$color_name" . "_$display.png";
                    $pdf->Image(
                            $imagename
                            , $w * $center->size + $center->padding_width
                            , $h * $center->size + $center->padding_height
                            , $center->size
                            , $center->size
                    );
                } else {
                    $pdf->SetFillColor(220, 220, 220);
                    $pdf->Rect($w * $center->size + $center->padding_width
                            , $h * $center->size + $center->padding_height
                            , $center->size
                            , $center->size
                            , "F");
                }
            }
        }

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);

        for ($h = 0; $h < $blocks->pixel->height; $h++) {
            for ($w = 0; $w < $blocks->pixel->width; $w++) {
                $pdf->Rect($w * $center->size + $center->padding_width
                        , $h * $center->size + $center->padding_height
                        , $center->size
                        , $center->size);
            }
        }

        $pdf->SetLineWidth(1.2);
        $pdf->SetDrawColor(220, 220, 220);
        for ($h = 0; $h < $blocks->cubes->height; $h++) {
            for ($w = 0; $w < $blocks->cubes->width; $w++) {
                $pdf->Rect(
                        $w * $center->size * $cubes->pixel->width + $center->padding_width
                        , $h * $center->size * $cubes->pixel->height + $center->padding_height
                        , $center->size * $cubes->pixel->width
                        , $center->size * $cubes->pixel->height);
            }
        }
        $pdf->Rect(
                $center->padding_width
                , $center->padding_height
                , $pdf->GetPageWidth() - $center->padding_width * 2
                , $pdf->GetPageHeight() - $center->padding_height * 2);
    }
}

$pdf->Output('mosaic_' . $scheme_id . '.pdf', 'I');
$pdf->Close();
