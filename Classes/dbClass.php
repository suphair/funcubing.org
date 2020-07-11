<?php

class db {

    const VERSION = '1.0.0';

    protected static $_instance;
    protected static $connection;

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

    static function set($host, $username, $password, $schema, $port) {
        self::$connection = mysqli_init();
        mysqli_real_connect(self::$connection
                , $host, $username, $password, $schema, $port);
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
            $error = "Query:<br>$sql<br>" . self::VERSION . "<br>Error:<br>" . mysqli_error(self::$connection) . "<br>";
            trigger_error($error, E_USER_ERROR);
        }
        return $result;
    }

}
