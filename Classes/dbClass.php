<?php

class db {

    const VERSION = '1.1.0';

    protected static $_instance;
    protected static $connection;
    protected static $host;
    protected static $username;
    protected static $password;
    protected static $schema;
    protected static $port;

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

    static function connection() {
        return self::$connection;
    }

    static function set($values) {

        self::$host = $values['host'];
        self::$username = $values['username'];
        self::$password = $values['password'];
        self::$schema = $values['schema'];
        self::$port = $values['port'];
        self::$connection = mysqli_init();
        mysqli_real_connect(self::$connection
                , self::$host, self::$username, self::$password, self::$schema, self::$port);
        if (mysqli_connect_errno()) {
            die('<h1>Error establishing a database connection</h1>');
        }
        mysqli_query(self::$connection, "SET CHARSET UTF8");
    }

    static function escape($str) {
        return mysqli_escape_string(self::$connection, $str);
    }

    static function close() {
        mysqli_close(self::$connection);
    }

    static function row($sql, $statements = [], $debug = false) {
        $result = self::exec($sql, $statements, $debug);
        return $result->fetch_object();
    }

    static function rows($sql, $statements = [], $debug = false) {
        $result = self::exec($sql, $statements, $debug);
        $objects = [];
        while ($object = $result->fetch_object()) {
            $objects[] = $object;
        }
        return $objects;
    }

    static function id() {
        return self::$connection->insert_id;
    }

    static function affected() {
        return self::$connection->affected_rows;
    }

    static function exec($sql, $statements = []) {
        foreach ($statements as $key => $value) {
            $value_escape = mysqli_escape_string(self::$connection, $value);
            $sql = str_replace(":$key", "'$value_escape'", $sql);
        }
        $result = mysqli_query(self::$connection, $sql);
        if (!$result) {
            $error = "Query:<br>$sql<br>Error:<br>" . mysqli_error(self::$connection) . "<br>";
            trigger_error($error, E_USER_ERROR);
        }
        return $result;
    }

    static function exec_null($sql) {
        return self::exec(str_replace(["'null'", "''"], "null", $sql));
    }

    static function dump($dir) {
        $class = 'Ifsnop\Mysqldump\Mysqldump';
        if (class_exists($class)) {
            $dump = new $class(
                    "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$schema,
                    self::$username,
                    self::$password,
                    ['add-drop-table' => true]);

            $filename = "$dir/dump.sql";
            $dump->start($filename);
            $zip = new ZipArchive();
            $zip_name = $filename . ".zip";
            $zip->open($zip_name, ZIPARCHIVE::CREATE);
            $zip->addFile($filename);
            $zip->close();
            unlink($filename);
        } else {
            trigger_error("$class not found", E_USER_ERROR);
        }
    }

    static function template($dir) {
        $class = 'Ifsnop\Mysqldump\Mysqldump';
        if (class_exists($class)) {
            $dump = new $class(
                    "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$schema,
                    self::$username,
                    self::$password,
                    ['no-data' => true,
                'reset-auto-increment' => true]);

            $filename = "$dir/db_template.sql";
            $dump->start($filename);
        } else {
            trigger_error("$class not found", E_USER_ERROR);
        }
    }

}
