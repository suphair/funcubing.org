<?php

class Mosaic {

    protected static $_instance;
    public static $id;
    public static $dirName;
    public static $fileNameImage;
    public static $fileNameCut;
    public static $fileNamePix;
    public static $fileNamePaint_template;
    public static $fileNameBorder_template;
    public static $dirNameStep;
    public static $fileNamePaint;
    public static $fileNameBorder = [];
    public static $fileNamePDF_template;
    public static $add_schema = false;
    public static $color;
    public static $pdfImages;
    public static $customColorsSchema = [];
    public static $widthCubes_pdf;
    public static $heightCubes_pdf;
    public static $step_name;
    public static $cubes;
    public static $step = 0;
    public static $images = [];
    public static $colors = [
        'W' => array(255, 255, 255),
        'Y' => array(255, 255, 0),
        'O' => array(255, 165, 0),
        'R' => array(255, 0, 0),
        'G' => array(0, 255, 0),
        'B' => array(0, 0, 255)];
    public static $pdfImagesVars = ['EN' => 'EN', 'RU' => 'RU', 'Fill' => '', 'Letter' => 'W'];
    public static $pixels;
    private static $color_schemas = array("WYORBB", "WYORRB", "WYOORB", "WYYORB", "WWYORB", "WYORBG", "WYORGB");

    CONST START_LAYER = 6;
    CONST CUBE_AMOUNT = 300;
    CONST STEPS = 5;
    CONST MAX_PIXELS = 7;
    Const StepPicture = 'Load picture';
    Const StepPreparation = 'Set cubes';
    Const StepChoosing = 'Chosen variants';
    Const StepGeneration = 'Select PDF';

    protected function __construct() {
        
    }

    protected function __clone() {
        
    }

    protected function __wakeup() {
        
    }

    public static function Init() {
        if (isset($_SESSION['customColorsSchema'])) {
            self::$customColorsSchema = $_SESSION['customColorsSchema'];
        } else {
            for ($i = 0; $i < self::START_LAYER; $i++) {
                self::$customColorsSchema[$i] = '_';
            }
        }

        self::$cubes = isset($_SESSION['cubes']) ? $_SESSION['cubes'] : self::CUBE_AMOUNT;
        self::$images = isset($_SESSION['images']) ? $_SESSION['images'] : [];
        self::$pixels = isset($_SESSION['pixels']) ? $_SESSION['pixels'] : 3;
        self::$color = isset($_SESSION['color']) ? $_SESSION['color'] : 'B';
        self::$widthCubes_pdf = isset($_SESSION['widthCubes_pdf']) ? $_SESSION['widthCubes_pdf'] : 3;
        self::$heightCubes_pdf = isset($_SESSION['heightCubes_pdf']) ? $_SESSION['heightCubes_pdf'] : 4;
        self::$step_name = isset($_SESSION['step_name']) ? $_SESSION['step_name'] : self::StepPreparation;

        self::$pdfImages = isset($_SESSION['pdfImages']) ? $_SESSION['pdfImages'] : reset(self::$pdfImagesVars);
        if (!isset($_SESSION['GUID'])) {
            $_SESSION['GUID'] = rand_str();
        }
        self::UpdateID();
        if (isset($_SESSION['step'])) {
            self::$step = $_SESSION['step'];
            self::setStep(self::$step);
        }
    }

    public static function Reset() {
        DeleteDirectoryFiles(self::$id);
        //$_SESSION['GUID']= rand_str();
        //session_regenerate_id();
        $_SESSION = [];
        self::Init();
        //self::UpdateID();
        //if($Base){
        //   BasePage();
        //}
        self::setStepName(self::StepPreparation);
    }

    private static function UpdateID() {
        self::$id = $_SESSION['GUID'];
        if (!file_exists("Images")) {
            mkdir("Images");
        }
        if (!file_exists("Images/MosaciBuilding")) {
            mkdir("Images/MosaciBuilding");
        }
        if (!file_exists("Images/MosaciBuilding/" . self::$id)) {
            mkdir("Images/MosaciBuilding/" . self::$id);
        }
        self::$add_schema = false;
        self::$dirName = "Images/MosaciBuilding/" . self::$id . "/";
        self::$fileNameImage = self::$dirName . "Image.jpg";
        self::$fileNameCut = self::$dirName . "Image_cut.png";
        self::$fileNamePDF_template = self::$dirName . "[STEP]/Image_border_[SCHEMA].png";
    }

    public static function addSchema($schema) {
        if ($schema) {
            self::$add_schema = substr($schema . "BBBBB", 0, self::START_LAYER);
        } else {
            self::$add_schema = false;
        }
    }

    public static function getSchemas() {
        $color_schemas = self::$color_schemas;
        if (self::$add_schema) {
            $color_schemas[] = self::$add_schema;
        }
        return $color_schemas;
    }

    public static function setStep($n) {
        self::$step = $n;
        self::$dirNameStep = self::$dirName . self::$step . "/";
        self::$fileNamePix = self::$dirNameStep . "/Image_pix.png";
        self::$fileNamePaint_template = self::$dirNameStep . "Image_pixel_[SCHEMA].png";
        self::$fileNameBorder_template = self::$dirNameStep . "Image_border_[SCHEMA].png";
        self::saveSession();
    }

    public static function addStep($step) {
        self::setStep($step + 1);
    }

    public static function DeleteStep() {
        DeleteDirectoryFiles(self::$id . "/" . self::$step);
        if (!file_exists(self::$dirNameStep)) {
            mkdir(self::$dirNameStep);
        }
    }

    public static function AddImage($step, $code) {
        self::$images[$step] = $code;
        self::addStep(strlen($code) - self::START_LAYER + 1);
        self::saveSession();
    }

    public static function setCubes($n) {
        self::$cubes = $n;
        self::saveSession();
    }

    private static function saveSession() {
        $_SESSION['cubes'] = self::$cubes;
        $_SESSION['images'] = self::$images;
        $_SESSION['step'] = self::$step;
        $_SESSION['pixels'] = self::$pixels;
        $_SESSION['color'] = self::$color;
        $_SESSION['pdfImages'] = self::$pdfImages;
        $_SESSION['widthCubes_pdf'] = self::$widthCubes_pdf;
        $_SESSION['heightCubes_pdf'] = self::$heightCubes_pdf;
        $_SESSION['step_name'] = self::$step_name;
    }

    public static function resetImages() {
        self::$images = [];
    }

    public static function changeCube() {

        if (isset($_POST['CubeAmount'])) {
            self::setCubes($_POST['CubeAmount']);
        }
        /*
          if(isset($_POST['width']) or isset($_POST['height']) ){
          $FileCut=new Image(self::$fileNameCut);
          $CustomCube=['width'=>$FileCut->cube_width, 'height'=>$FileCut->cube_height];
          foreach(['width','height'] as $dim)
          if(isset($_POST[$dim])) $CustomCube[$dim] += $_POST[$dim];
          }

          if(isset($_POST['schema'])){
          self::addSchema($_POST['schema']);
          }
         */
        if (file_exists(self::$fileNameImage)) {

            self::setStep(1);
            self::resetImages();

            $FileImage = new Image(self::$fileNameImage);

            $P = self::$pixels;
            $C = self::$cubes;
//print_r($FileImage);
//            exit(); 
            //        if(!empty($CustomCube)){
            //            $new_w = $CustomCube['width'] * $P;
            //            $new_h = $CustomCube['height'] * $P;
            //        }else{


            $k = sqrt($C / ($FileImage->pixels));
            $new_w = floor($k * $FileImage->width) * $P;
            $new_h = floor($k * $FileImage->heigth) * $P;


            if ($new_h * $new_w / $P / $P < $C) {
                if ($new_h < $new_w and ( $new_h + $P) * $new_w / $P / $P <= $C) {
                    $new_h = $new_h + $P;
                }
                if ($new_w < $new_h and $new_h * ($new_w + $P) / $P / $P <= $C) {
                    $new_w = $new_w + $P;
                }
                if ($new_w == $new_h and ( $new_h + $P) * ($new_w + $P) / $P / $P <= $C) {
                    $new_w = $new_w + $P;
                    $new_h = $new_h + $P;
                }
            }
            //        }

            $ImageCut = new Image(false, $new_w, $new_h);
            $ImageCut->CopyResampled($FileImage);
            $ImageCut->Save(self::$fileNameCut);

            self::generatePaints();
        }
        self::saveSession();
    }

    static private function setSchemas() {
        if (self::$step == 1) {
            return self::getSchemas();
        } elseif (isset($_POST['Code'])) {
            $Base = $_POST['Code'];
            $ColorSchemas = [];
            for ($i = 0; $i < strlen($Base); $i++) {
                if ($i == 0) {
                    $ColorSchemas[] = $Base[0] . $Base;
                } elseif ($i == strlen($Base) - 1) {
                    $ColorSchemas[] = $Base . $Base[strlen($Base) - 1];
                } else {
                    $ColorSchemas[] = substr($Base, 0, $i) . substr($Base, $i - 1, strlen($Base));
                    $ColorSchemas[] = substr($Base, 0, $i + 1) . substr($Base, $i, strlen($Base));
                }
            }
            return array_unique($ColorSchemas);
        }
        self::Reset(true);
    }

    static public function generatePaints() {
        $ColorSchemas = self::setSchemas();
        if (self::$step == 1 and sizeof(self::$customColorsSchema) == self::START_LAYER and ! in_array('_', self::$customColorsSchema)) {
            $ColorSchemas = [implode('', self::$customColorsSchema)];
        }

        $ImageCut = new Image(self::$fileNameCut);
        $color_count = self::$step + self::START_LAYER - 1;
        imagetruecolortopalette($ImageCut->image, false, $color_count);
        self::DeleteStep();
        $ImageCut->Save(self::$fileNamePix);

        $Image_Pixel = new Image(self::$fileNamePix);
        $Colors_index = $Image_Pixel->getColorsIndex($color_count);

        $Image = new Image(self::$fileNamePix);
        $Image->SetColors();
        $ImageLayers = [];
        $ImageLayers_JS = [];
        $ImageLayers_JS_ = [];
        if (self::$step == 1) {
            for ($i = 0; $i < self::START_LAYER; $i++) {
                $ImageLayers[$i] = new Image(false, $Image->width, $Image->heigth);
                $ImageLayers[$i]->SetColor('230', '230', '230');
                $ImageLayers[$i]->Filled();
            }


            $ImageLayersALL = new Image(false, $Image->width, $Image->heigth);
            //$ImageLayersALL->SetColor('200','200','200');
            //$ImageLayersALL->Filled();


            $CCS = self::$customColorsSchema;
            //$CustomColorSchema=['','Y','G','R','B',''];

            for ($x = 0; $x < $Image_Pixel->width; $x++) {
                for ($y = 0; $y < $Image_Pixel->heigth; $y++) {
                    $rgb = ImageColorAt($Image->image, $x, $y);
                    $rgb_arr = imagecolorsforindex($Image->image, $rgb);
                    $color_index = $Colors_index[$rgb_arr['red']][$rgb_arr['green']][$rgb_arr['blue']];
                    if (isset($CCS[$color_index]) and isset(self::$colors[$CCS[$color_index]])) {
                        $cs = self::$colors[$CCS[$color_index]];
                        $ImageLayersALL->SetColor($cs[0], $cs[1], $cs[2]);
                        $ImageLayers[$color_index]->SetColor($cs[0], $cs[1], $cs[2]);
                    } else {
                        $ImageLayersALL->SetColor('230', '230', '230');
                        $ImageLayers[$color_index]->SetColor('0', '0', '0');
                    }
                    $ImageLayersALL->SetPixelColor($x, $y);
                    $ImageLayers[$color_index]->SetPixelColor($x, $y);
                }
            }

            foreach ($ImageLayers as $i => $ImageLayer) {
                $ImageLayer->Save(self::$dirName . "LayerL" . $i . ".png");
                $ImageLayer->Save(self::$dirName . "LayerL" . $i . ".png");
            }
            $ImageLayersALL->Save(self::$dirName . "LayerALL.png");
        }

        foreach ($ColorSchemas as $Schema) {
            $Image = new Image(self::$fileNamePix);
            $Image->SetColors();
            for ($x = 0; $x < $Image_Pixel->width; $x++) {
                for ($y = 0; $y < $Image_Pixel->heigth; $y++) {
                    $rgb = ImageColorAt($Image->image, $x, $y);
                    $rgb_arr = imagecolorsforindex($Image->image, $rgb);
                    $color_index = $Colors_index[$rgb_arr['red']][$rgb_arr['green']][$rgb_arr['blue']];
                    $Image->SetPixelColorIndex($x, $y, substr($Schema, $color_index, 1));
                }
            }
            self::$fileNamePaint = str_replace('[SCHEMA]', $Schema, self::$fileNamePaint_template);

            $Image->Save(self::$fileNamePaint);

            $W = $Image->width;
            $H = $Image->heigth;

            $border = 1;
            $cell = 7;
            $BC = $cell + $border;

            $new_w = $W * $BC + $border;
            $new_h = $H * $BC + $border;

            $ImageBorder = new Image(false, $new_w, $new_h);
            $ImageBorder->CopyResampled($Image);
            $color_borders = ['W' => [200, 200, 200], 'B' => [0, 0, 0]];

            foreach ($color_borders as $color_name => $color_arr) {
                $ImageBorder->SetColor($color_arr[0], $color_arr[1], $color_arr[2]);

                for ($w = 0; $w <= $W; $w++) {
                    $ImageBorder->FilledRectangle($BC * $w, 0, $border + $BC * $w, $new_h);
                }
                for ($h = 0; $h <= $H; $h++) {
                    $ImageBorder->FilledRectangle(0, $BC * $h, $new_w, $border + $BC * $h);
                }
                self::$fileNameBorder[$color_name] = str_replace('[SCHEMA]', $Schema . "_$color_name", self::$fileNameBorder_template);
                $ImageBorder->Save(self::$fileNameBorder[$color_name]);
            }$Image = new Image(self::$fileNamePaint);
            $W = $Image->width;
            $H = $Image->heigth;

            $border = 1;
            $cell = 7;
            $BC = $cell + $border;

            $new_w = $W * $BC + $border;
            $new_h = $H * $BC + $border;

            $ImageBorder = new Image(false, $new_w, $new_h);
            $ImageBorder->CopyResampled($Image);
            $color_borders = ['W' => [200, 200, 200], 'B' => [0, 0, 0]];

            foreach ($color_borders as $color_name => $color_arr) {
                $ImageBorder->SetColor($color_arr[0], $color_arr[1], $color_arr[2]);

                for ($w = 0; $w <= $W; $w++) {
                    $ImageBorder->FilledRectangle($BC * $w, 0, $border + $BC * $w, $new_h);
                }
                for ($h = 0; $h <= $H; $h++) {
                    $ImageBorder->FilledRectangle(0, $BC * $h, $new_w, $border + $BC * $h);
                }
                self::$fileNameBorder[$color_name] = str_replace('[SCHEMA]', $Schema . "_$color_name", self::$fileNameBorder_template);
                $ImageBorder->Save(self::$fileNameBorder[$color_name]);
            }
        }
    }

    public static function setPixels($pixels) {
        self::$pixels = $pixels;
        self::saveSession();
    }

    public static function setColor($color) {
        self::$color = $color;
        self::saveSession();
    }

    public static function setPdfImage($pdfImages) {
        self::$pdfImages = $pdfImages;
        self::saveSession();
    }

    public static function setCubes_pdf($widthCubes_pdf, $heightCubes_pdf) {
        self::$widthCubes_pdf = $widthCubes_pdf;
        self::$heightCubes_pdf = $heightCubes_pdf;
        self::saveSession();
    }

    public static function setStepName($name) {
        self::$step_name = $name;
        self::saveSession();
    }

}
