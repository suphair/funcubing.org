<?php

namespace mosaic;

class value {

    public static $colors = [
        'W' => [255, 255, 255],
        'Y' => [255, 255, 0],
        'O' => [255, 165, 0],
        'R' => [255, 0, 0],
        'G' => [0, 255, 0],
        'B' => [0, 0, 255]
    ];
    protected static $_instance;
    static $session;
    static $image;
    static $folder_session;
    static $folder_image;
    static $folder_layers;
    static $folder_step;
    static $filename_load;
    static $filename_cut;
    static $filename_pix;
    static $step;
    static $first_len;

    const FOLDER = 'Images/mosaic/';
    const STEPS = 5;

    public static function init() {
        $session_id = session_id();
        $session = \db::row("Select * from mosaic_session WHERE session='$session_id'");
        if (!$session) {
            $color_dict = \db::row("SELECT code FROM mosaic_colors_dict WHERE `default` = 1")->code;
            $pixel_dict = \db::row("SELECT code FROM mosaic_pixels_dict WHERE `default` = 1")->code;
            $display_dict = \db::row("SELECT code FROM mosaic_displays_dict WHERE `default` = 1")->code;
            \db::exec("INSERT INTO mosaic_session (session, color_dict, pixel_dict, display_dict) VALUES ('$session_id', '$color_dict', '$pixel_dict', '$display_dict')");

            $session = \db::row("Select * from mosaic_session WHERE session='$session_id'");
            self::$session = $session;
            set_session_folder();
        }
        self::$session = $session;

        self::$session->color = \db::row("SELECT * FROM mosaic_colors_dict WHERE code = '{$session->color_dict}' ");
        self::$session->pixel = \db::row("SELECT * FROM mosaic_pixels_dict WHERE code = '{$session->pixel_dict}' ");
        self::$session->display = \db::row("SELECT * FROM mosaic_displays_dict WHERE code = '{$session->display_dict}' ");

        $folder_session = self::FOLDER . $session->folder;
        if (!file_exists($folder_session)) {
            trigger_error($folder_session);
        }

        self::$filename_load = "$folder_session/load.jpg";
        self::$filename_cut = "$folder_session/cut.png";

        self::$folder_session = $folder_session;
        $image = \db::row("Select * FROM mosaic_images WHERE session_id = {$session->id} AND active = 1 ");
        self::$image = $image;
        if ($image) {
            $folder_image = $folder_session . '/' . $image->folder;
            if (!file_exists($folder_image)) {
                trigger_error($folder_image);
            }
            self::$folder_image = $folder_image;
            $folder_layers = "$folder_image/layers";
            self::$folder_layers = $folder_layers;

            if (!file_exists($folder_layers)) {
                mkdir($folder_layers);
            }

            $step = \db::row("SELECT id, step FROM `mosaic_steps` WHERE image_id={$image->id} ORDER BY step desc");
            if ($step) {
                self::$step = $step;
                self::$folder_step = "$folder_image/" . self::$step->step;
                self::$filename_pix = self::$folder_step . "/pix.png";
            }
        }

        self::$first_len = strlen(\db::row("SELECT code FROM mosaic_schemas_dict")->code);
    }

    private function __construct() {
        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

}
