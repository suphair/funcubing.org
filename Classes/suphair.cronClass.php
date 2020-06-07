<?php

namespace Suphair;

const COMMAND_FUNCTION = 'function';
const VERSION = '1.1.0';

class Cron {

    protected $connection;
    protected $id;

    function __construct($connection) {
        $this->connection = $connection;
    }

    public function run() {
        $id = $this->logBegin("suphair.cron " . VERSION);

        $querySelect = "
            SELECT name, command, type, argument
            FROM cron_config
            WHERE next < now() OR next IS NULL
            ORDER BY next ";

        $queryUpdate = "
            UPDATE cron_config 
            SET 
                last = now(),
                next = 
                CASE 
                    WHEN schedule    
                        THEN str_to_date(
                            CONCAT(DATE_ADD(CURDATE(),INTERVAL 1 DAY),' ',schedule),
                            '%Y-%m-%d %H:%i:%s')
                    WHEN period 
                        THEN DATE_ADD(now(),INTERVAL period MINUTE)
                END    
            WHERE name = '{name}'";
        $details = [];
        $result = mysqli_query($this->connection, $querySelect);
        while ($row = $result->fetch_assoc()) {
            mysqli_query($this->connection, str_replace('{name}', $row['name'], $queryUpdate));
            $this->execCommand($row['name'], $row['command'], $row['type'], $row['argument']);
            $details[] = $row['name'];
        }
        $result->free();
        $this->logEnd($id, json_encode($details));
    }

    private function execCommand($name, $command, $type, $argument) {
        $id = $this->logBegin($name);
        switch ($type) {
            case COMMAND_FUNCTION:
                if (!function_exists($command)) {
                    $details = "ERROR: $type $command not found";
                } else {
                    $details = $command($argument);
                }
                break;
            default: $details = "ERROR: Unsupported type $type";
        }
        $this->logEnd($id, $details);
    }

    private function logBegin($name) {
        $query = " INSERT INTO cron_logs (`name`) VALUES ('$name')";

        mysqli_query($this->connection, $query);
        return mysqli_insert_id($this->connection);
    }

    private function logEnd($id, $details) {
        $details_escape = mysqli_real_escape_string($this->connection, $details);
        $query = " UPDATE cron_logs SET details = '$details_escape' WHERE id = $id";
        mysqli_query($this->connection, $query);
    }

    public function add($name, $command, $period, $schedule, $type, $argument = FALSE) {

        $name_escape = mysqli_real_escape_string($this->connection, $name);
        $command_escape = mysqli_real_escape_string($this->connection, $command);
        $type_escape = mysqli_real_escape_string($this->connection, $type);
        $schedule_escape = mysqli_real_escape_string($this->connection, $schedule);
        $argument_escape = mysqli_real_escape_string($this->connection, $argument);

        if (!is_numeric($period)) {
            $period = 'null';
        }

        if (!$schedule_escape) {
            $schedule_escape = 'null';
        } else {
            $schedule_escape = "'$schedule_escape'";
        }

        if (!$argument_escape) {
            $argument_escape = 'null';
        } else {
            $argument_escape = "'$argument_escape'";
        }

        $query = "
            REPLACE INTO cron_config
                (`name`,`command`,`type`,`last`,`next`,`period`,`schedule`,`argument`)
            VALUES
                ('$name_escape','$command_escape','$type_escape',
                null,null,
                $period,$schedule_escape,'$argument_escape')";

        mysqli_query($this->connection, $query);
    }

    public function clear($name = false) {
        if ($name) {
            $name_escape = mysqli_real_escape_string($this->connection, $name);
            $query = "
            UPDATE cron_config
            SET next = null
            WHERE name = '$name_escape'";
        } else {
            $query = "
                UPDATE cron_config
                SET next = null";
        }
        mysqli_query($this->connection, $query);
    }

    public function init() {
        $queries = [];
        $errors = [];
        $queries['cron_config'] = "
            CREATE TABLE cron_config (
                `name` varchar(255) NOT NULL,
                `command` varchar(255) NOT NULL,
                `type` varchar(255) NOT NULL,
                `last` datetime DEFAULT NULL,
                `next` datetime DEFAULT NULL,
                `period` int(11) DEFAULT NULL COMMENT 'in minutes',
                `schedule` time DEFAULT NULL,
                `argument` varchar(255) NOT NULL,
                PRIMARY KEY (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        $queries['cron_logs'] = "
            CREATE TABLE `cron_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT NULL,
                `begin` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                `details` text DEFAULT NULL,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        foreach ($queries as $table => $query) {
            if (!mysqli_query($this->connection, $query)) {
                $errors[$table] = mysqli_error($this->connection);
            }
        }

        if (sizeof($errors)) {
            trigger_error("cron.createTables: " . json_encode($errors), E_USER_ERROR);
        }
    }

}
