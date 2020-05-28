<?php

class Image {

    public $width;
    public $heigth;
    public $pixels;
    public $cube_width;
    public $cube_height;
    public $cube_total;
    public $image;
    public $type;
    private $file;
    public $colors_img;
    public $colors_img_256;
    private $color;
    public $colors;

    public function __construct($file, $width = 0, $heigth = 0) {
        if ($file) {
            $this->file = $file;
            if (!$this->checkFile($this->file)) {
                return false;
            }
            list($this->width, $this->heigth, $this->type) = getimagesize($this->file);
            $this->pixels = $this->width * $this->heigth;
            $this->cube_width = $this->width / Mosaic::$pixels;
            $this->cube_height = $this->heigth / Mosaic::$pixels;
            $this->cube_total = $this->cube_width * $this->cube_height;

            if ($this->type == 2) {
                $this->image = imagecreatefromjpeg($this->file);
            } elseif ($this->type == 3) {
                $this->image = imagecreatefrompng($this->file);
            } else {
                Mosaic::Reset();
            }
        } else {
            if ($width and $heigth and is_numeric($width) and is_numeric($heigth)) {
                $this->image = imagecreatetruecolor($width, $heigth);
                $this->width = $width;
                $this->heigth = $heigth;
                $this->pixels = $this->width * $this->heigth;
            } else {
                Mosaic::Reset();
            }
        }
    }

    private function checkFile($file) {
        if (!file_exists($file)) {
            echo $this->file . "<br>";
            Mosaic::Reset();
            return false;
        } else {
            return true;
        }
    }

    public function CopyResampled($image) {
        imagecopyresampled($this->image, $image->image, 0, 0, 0, 0, $this->width, $this->heigth, $image->width, $image->heigth);
    }

    public function Save($file) {
        if (strlen($file) > 4) {
            if (substr($file, -4) == '.png') {
                imagepng($this->image, $file);
                return;
            }
            if (substr($file, -4) == '.jpg') {
                imagejpeg($this->image, $file);
                return;
            }
        }
        Mosaic::Reset();
        //      BasePage();
    }

    public function GetColors() {
        $this->colors_img = [];
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->heigth; $y++) {
                $this->colors_img[$x][$y] = imagecolorsforindex($this->image, ImageColorAt($this->image, $x, $y));
            }
        }
    }

    public function GetColors_256() {
        $this->colors_img_256 = [];
        $colors_256 = [];
        foreach (Mosaic::$colors as $name => $code) {
            $colors_256[$code[0] * 16 * 16 + $code[1] * 16 + $code[2]] = $name;
        }

        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->heigth; $y++) {
                $color_arr = imagecolorsforindex($this->image, imagecolorat($this->image, $x, $y));
                $code = $color_arr['red'] * 16 * 16 + $color_arr['green'] * 16 + $color_arr['blue'];
                $this->colors_img_256[$x][$y] = $colors_256[$code];
            }
        }
    }

    public function Filled() {
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->heigth; $y++) {
                imagesetpixel($this->image, $x, $y, $this->color);
            }
        }
    }

    public function SplitColors() {
        $this->GetColors();
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->heigth; $y++) {
                $diff = 1;
                $Color_N = 0;
                foreach (Mosaic::$colors as $n => $color) {
                    $r1 = ($color[0] - $this->colors_img[$x][$y]['red']) / 255;
                    $r2 = ($color[1] - $this->colors_img[$x][$y]['green']) / 255;
                    $r3 = ($color[2] - $this->colors_img[$x][$y]['blue']) / 255;
                    $diff_tmp = sqrt(($r1 * $r1 + $r2 * $r2 + $r3 * $r3) / 3);
                    if ($diff_tmp < $diff) {
                        $diff = $diff_tmp;
                        $Color_N = $n;
                    }
                }
                imagesetpixel($this->image, $x, $y, imagecolorallocate(
                                $this->image, Mosaic::$colors[$Color_N][0], Mosaic::$colors[$Color_N][1], Mosaic::$colors[$Color_N][2]));
            }
        }
    }

    public function SetColor($red, $green, $blue) {
        $this->color = imagecolorallocate($this->image, $red, $green, $blue);
    }

    public function SetColors() {
        $this->colors = [];
        foreach (Mosaic::$colors as $name => $code) {
            $this->colors[$name] = imagecolorallocate($this->image, $code[0], $code[1], $code[2]);
        }
    }

    public function FilledRectangle($x0, $y0, $x1, $y1) {
        imagefilledrectangle($this->image, $x0, $y0, $x1, $y1, $this->color);
    }

    public function getColorsIndex($color_count) {
        $colors = [];
        for ($i = 0; $i < $color_count; $i++) {
            $colors[] = imagecolorsforindex($this->image, $i);
        }
        usort($colors, "color_hsl");

        $Colors_index = [];
        foreach ($colors as $n => $color) {
            $Colors_index[$color['red']][$color['green']][$color['blue']] = $n;
        }
        return $Colors_index;
    }

    public function SetPixelColorIndex($x, $y, $index) {
        imagesetpixel($this->image, $x, $y, $this->colors[$index]);
    }

    public function SetPixelColor($x, $y) {
        imagesetpixel($this->image, $x, $y, $this->color);
    }

}
