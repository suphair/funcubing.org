<?php

class config {

    const VERSION = '2.0.2';

    protected static $dir;
    protected static $server;
    protected static $configDefault;
    protected static $configServer;

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

    static function init($dir) {
        self::$dir = $dir;
        self::$server = str_replace("www.","",strtolower(filter_input(INPUT_SERVER, 'SERVER_NAME')));

        if (!file_exists($dir)) {
            trigger_error("config: direcory [$dir] is not exists", E_USER_ERROR);
        }

        self::$configDefault = self::$dir . "/default.ini";

        if (!file_exists(self::$configDefault)) {
            trigger_error("config: file .ini[" . self::$configDefault . "] not exists", E_USER_ERROR);
        }

        self::$configServer = self::$dir . "/" . self::$server . ".ini";
        if (!file_exists(self::$configServer)) {
            trigger_error("config: file .ini [" . self::$configServer . "] not exists", E_USER_ERROR);
        }

        $serverKey = self::$dir . "/" . self::$server . ".key";
        if (!file_exists($serverKey)) {
            trigger_error("config: file. key [$serverKey] not exists", E_USER_ERROR);
        }
    }

    static function info() {
        return "config(" . self::VERSION . "): " . self::$server;
    }

    static function isLocalhost() {
        return self::$server == 'localhost';
    }

    static function get($section, $param) {
        $configDefault = parse_ini_file(self::$configDefault, true);
        $configServer = parse_ini_file(self::$configServer, true);

        if (isset($configDefault[$section][$param])) {
            return trim($configDefault[$section][$param]);
        } elseif (isset($configServer[$section][$param])) {
            return trim($configServer[$section][$param]);
        } else {
            trigger_error("config: value $section/$param not found", E_USER_ERROR);
        }
    }

    static function template($dir) {
        $configDefault = parse_ini_file(self::$configDefault, true);
        $configServer = parse_ini_file(self::$configServer, true);
        $config = '';
        $config .= ';version ' . self::VERSION . "\n";
        $config .= ';' . date('d M Y') . "\n";
        foreach (array_merge($configDefault, $configServer) as $section => $values) {
            $config .= "[$section]\n";
            foreach ($values as $key => $value) {
                $config .= "    $key=\n";
            }
        }
        $handle = fopen("$dir/config_template.ini", "w+");
        fwrite($handle, $config);
        fclose($handle);
    }

}
