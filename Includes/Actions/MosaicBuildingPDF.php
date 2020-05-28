<?php

Mosaic::Init();

$W_max = Mosaic::$widthCubes_pdf;
$H_max = Mosaic::$heightCubes_pdf;


$name = getRequest()[2];
$file = Mosaic::$dirName . (strlen($name) - Mosaic::START_LAYER + 1) . "/Image_pixel_" . $name . ".png";
//echo $file;
//exit();
$Image = new Image($file);
$Image->GetColors_256();

$heigth = $Image->heigth;
$width = $Image->width;

$P = Mosaic::$pixels;
$W = $P * $W_max;
$H = $P * $H_max;
$Cube_width = ceil($width / $W);
$Cube_height = ceil($heigth / $H);


if ($width > $heigth) {
    @$pdf = new FPDF('L', 'mm');
} else {
    @$pdf = new FPDF('P', 'mm');
}


$dx = 30;
$dy = 10;

$pdf_w = $pdf->w;
$pdf_h = $pdf->h;

$w_total = $Cube_width * $W;
$h_total = $Cube_height * $H;

$cell_w = ($pdf_w - 2 * $dx) / $Image->width;
$cell_h = ($pdf_h - 2 * $dy) / $Image->heigth;

$cell_s = min(array($cell_w, $cell_h));
$cell_w = $cell_s;
$cell_h = $cell_s;

$dx = ($pdf_w - $cell_w * $Image->width) / 2;
$dy = ($pdf_h - $cell_h * $Image->heigth) / 2;

if ($dy < $dx)
    $dy = $dx;


$pdf->SetFont('Arial', '', 24);

$pdf->AddPage();

for ($h = 0; $h < $heigth; $h ++) {
    for ($w = 0; $w < $width; $w ++) {
        $color_pix = $Image->colors_img_256[$w][$h];
        $pdf->SetFillColor(Mosaic::$colors[$color_pix][0], Mosaic::$colors[$color_pix][1], Mosaic::$colors[$color_pix][2]);
        $pdf->Rect($w * $cell_w + $dx, $h * $cell_h + $dy, $cell_w, $cell_h, "F");
    }
}

$pdf->SetLineWidth(0.2);
if (Mosaic::$color == 'W') {
    $pdf->SetDrawColor(200, 200, 200);
} else {
    $pdf->SetDrawColor(0, 0, 0);
}
for ($h = 0; $h < $heigth; $h ++) {
    for ($w = 0; $w < $width; $w ++) {
        $pdf->Rect(($w) * $cell_w + $dx, ($h) * $cell_h + $dy, $cell_w, $cell_h);
    }
}

if ($P > 1) {
    $pdf->SetLineWidth(0.4);
    if (Mosaic::$color == 'W') {
        $pdf->SetDrawColor(200, 200, 200);
    } else {
        $pdf->SetDrawColor(0, 0, 0);
    }
    for ($h = 0; $h < $heigth; $h += $P) {
        for ($w = 0; $w < $width; $w += $P) {
            $pdf->Rect(($w) * $cell_w + $dx, ($h) * $cell_h + $dy, $cell_w * $P, $cell_h * $P);
        }
    }
}
$pdf->SetDrawColor(0, 0, 0);
$pdf->Text($dx, $dy / 2, ($width / $P) . ' * ' . ($heigth / $P) . " = " . ($width * $heigth / ($P * $P)));


$pdf->AddPage();


$dx = ($pdf_w - $cell_w * ceil($Image->width / $W) * $W) / 2;
$dy = ($pdf_h - $cell_h * ceil($Image->heigth / $H) * $H) / 2;
if ($dy < $dx)
    $dy = $dx;
for ($h = 0; $h < $heigth; $h ++) {
    for ($w = 0; $w < $width; $w ++) {
        $color_name = $Image->colors_img_256[$w][$h];
        $Imagename = "Image/CubeImage/$color_name" . "_" . Mosaic::$pdfImages . ".png";
        if (file_exists($Imagename)) {
            $pdf->Image($Imagename, $w * $cell_w + $dx, $h * $cell_h + $dy, $cell_w, $cell_h);
        } else {
            $color_pix = $color_name;
            $pdf->SetFillColor(Mosaic::$colors[$color_pix][0], Mosaic::$colors[$color_pix][1], Mosaic::$colors[$color_pix][2]);
            $pdf->Rect($w * $cell_w + $dx, $h * $cell_h + $dy, $cell_w, $cell_h, "F");
        }
    }
}

$pdf->SetLineWidth(0.2);
$pdf->SetDrawColor(215, 215, 215);
for ($h = 0; $h < $heigth; $h ++) {
    for ($w = 0; $w < $width; $w ++) {
        $pdf->Rect(($w) * $cell_w + $dx, ($h) * $cell_h + $dy, $cell_w, $cell_h);
    }
}


if ($P > 1) {
    $pdf->SetLineWidth(0.5);
    $pdf->SetDrawColor(0, 0, 0);
    for ($h = 0; $h < $heigth; $h += $H) {
        for ($w = 0; $w < $width; $w += $W) {
            $pdf->Rect(($w) * $cell_w + $dx, ($h) * $cell_h + $dy, $cell_w * $W, $cell_h * $H);
        }
    }
}

$pdf->SetLineWidth(0.2);
$pdf->SetDrawColor(0, 0, 0);
for ($h = 0; $h < $heigth; $h += $H) {
    $pdf->Text($dx - 6, $h * $cell_h + $dy + $cell_h * 3 * 4 / 2 + 4, $Cube_height - $h / $H);
    $pdf->Text($pdf->w - $dx + 2, $h * $cell_h + $dy + $cell_h * $H / 2 + 4, $Cube_height - $h / $H);
    $pdf->Line($dx / 2, $h * $cell_h + $dy + $cell_h * $H, $dx * 3 / 2 + $Cube_width * $W * $cell_w, $h * $cell_h + $dy + $cell_h * $H);
}
$pdf->Line($dx / 2, $dy, $dx * 3 / 2 + $Cube_width * $W * $cell_w, $dy);

$i_name = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N',
    15 => 'O', 16 => 'P', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z');
for ($k = 26; $k < 100; $k++) {
    $i_name[$k] = $k;
}
$pdf->SetLineWidth(0.2);
$pdf->SetDrawColor(0, 0, 0);
for ($w = 0; $w < $width; $w += $W) {
    $pdf->Text($w * $cell_w + $dx + $cell_w, $dy / 2 + 8, $i_name[$w / $W + 1]);
    $pdf->Text($w * $cell_w + $dx + $cell_w, $Cube_height * $H * $cell_h + $dy * 3 / 2 - 4, $i_name[$w / $W + 1]);
    $pdf->Line($w * $cell_w + $dx + $cell_w * $W, $dy / 2, $w * $cell_w + $dx + $cell_w * $W, $dy * 3 / 2 + $Cube_height * $H * $cell_h);
}
$pdf->Line($dx, $dy / 2, $dx, $dy * 3 / 2 + $Cube_height * $P * 4 * $cell_h);


$ddx = 15;
$ddy = 30;

for ($j = $Cube_height; $j >= 1; $j--) {
    for ($i = 1; $i <= $Cube_width; $i++) {
        $pdf->SetFillColor(200, 200, 200);
        $pdf->addPage("P");
        $pdf_w = $pdf->w;
        $pdf_h = $pdf->h;
        $kk = min(($pdf_w - 2 * $ddx) / $W_max, ($pdf_h - 2 * $ddy) / $H_max);


        $pdf->SetFont('Arial', '', 24);
        $pdf->SetLineWidth(0.2);

        $ty = $kk * $W_max / $Cube_width;
        for ($t = 1; $t <= $Cube_width; $t++) {
            $pdf->Rect($ddx + $ty * ($t - 1), $ddy - 10, $ty, 10);
        }
        $pdf->Rect($ddx + $ty * ($i - 1), $ddy - 10, $ty, 10, "F");
        $pdf->Text($ddx + $ty * ($i - 1) + 3, $ddy - 2, $i_name[$i] . ($Cube_height - $j + 1));


        for ($ii = 1; $ii <= $W; $ii++) {
            for ($jj = 1; $jj <= $H; $jj++) {
                $xx = $ddx + $kk / $P * ($ii - 1);
                $yy = $ddy + 5 + $kk / $P * ($jj - 1);
                if (isset($Image->colors_img_256[$ii + $W * ($i - 1) - 1][$jj + $H * ($j - 1) - 1])) {
                    $color_name = $Image->colors_img_256[$ii + $W * ($i - 1) - 1][$jj + $H * ($j - 1) - 1];
                    $Imagename = "Image/CubeImage/$color_name" . "_" . Mosaic::$pdfImages . ".png";
                    if (file_exists($Imagename)) {
                        $pdf->Image($Imagename, $xx, $yy, $kk / $P, $kk / $P);
                    } else {
                        $color_pix = $color_name;
                        $pdf->SetFillColor(Mosaic::$colors[$color_pix][0], Mosaic::$colors[$color_pix][1], Mosaic::$colors[$color_pix][2]);
                        $pdf->Rect($xx, $yy, $kk / $P, $kk / $P, "F");
                    }
                } else {
                    $pdf->SetFillColor(215, 215, 215);
                    $pdf->Rect($xx, $yy, $kk / $P, $kk / $P, "F");
                }
            }
        }


        $pdf->SetLineWidth(1);
        $pdf->SetDrawColor(215, 215, 215);
        for ($ii = 1; $ii <= $W; $ii++) {
            for ($jj = 1; $jj <= $H; $jj++) {
                $xx = $ddx + $kk / $P * ($ii - 1);
                $yy = $ddy + 5 + $kk / $P * ($jj - 1);
                $pdf->Rect($xx, $yy, $kk / $P, $kk / $P);
            }
        }

        if ($P > 1) {
            $pdf->SetLineWidth(1.5);
            $pdf->SetDrawColor(0, 0, 0);
            for ($ii = 1; $ii <= $W_max; $ii++) {
                for ($jj = 1; $jj <= $H_max; $jj++) {
                    $xx = $ddx + $kk * ($ii - 1);
                    $yy = $ddy + 5 + $kk * ($jj - 1);
                    $pdf->Rect($xx, $yy, $kk, $kk);
                }
            }
        }
    }
}

$pdf->Output(date("dMY") . '_MosaicBuilding.pdf', 'I');
$pdf->Close();
?>
