<?php

namespace Suphair \ Wca;

class Api {

    protected static $connection;

    const VERSION = '1.0.0';
    const URL = 'https://www.worldcubeassociation.org/api/v0/';
    const MINUTES = 60;

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
    
    public static function setConnection($connection){
        self::$connection=$connection;
    }
    
    public static function getUserCompetitionsUpcoming($id,$context,$assoc=true){
        $options=['upcoming_competitions' => 'true'];
        return self::getUser($id, $context, $options, $assoc);
    }
    
    public static function getUser($id,$context, $options = [], $assoc = true){
        return self:: curl($context, $options, $assoc,"users/$id");   
    }
    
    public static function getPerson($id,$context, $options = [], $assoc = true){
        return self:: curl($context, $options, $assoc,"persons/$id");   
    }
    
    
    public static function getCompetitionsUpcoming( $context, $assoc = true) {
        $options=['start' => date('Y-m-d')];
        return self::getCompetitions( $context, $options, $assoc);
    }
                        
    public static function getCompetitions( $context, $options = [], $assoc = true) {

        $options['sort']='start_date';
        $options['page']=1;
        return self:: curl($context, $options, $assoc,'competitions');
    }
    
        
    public static function getCompetition($wid,$context, $options = [], $assoc = true){
        return self:: curl($context, $options, $assoc,"competitions/$wid");   
    }
    
    public static function getCompetitionResults($wid,$context, $options = [], $assoc = true){
        return self:: curl($context, $options, $assoc,"competitions/$wid/results");   
    }
    
    public static function getCompetitionRegistrations( $wca,$context,$options = [], $assoc = true) {
        return self:: curl($context, $options, $assoc,"competitions/$wca/registrations");
    }

    private static function curl($context, $options, $assoc,$method){
        $key="$method:";
        foreach ($options as $value => $option) {
            $key .= mysqli_real_escape_string(self::$connection, "$value=$option;");
        }
        
        $cash = self::getCash($key);
        if ($cash) {
            return json_decode($cash, $assoc);
        }

        $returnData = false;
        while (TRUE) {
            $query = http_build_query($options);
            if($query){
            $url = self::URL.$method. "/?" . $query;
            }else{
            $url = self::URL.$method;    
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $dataJson = json_decode($data);
            curl_close($ch);
            self::log($url, $data, $status, $context);
            if ($status != 200) {
                break;
            }
            if (!$dataJson) {
                break;
            }
            $returnData .= $data;
            if(isset($options['page'])){
                $options['page']++;
            }else{
                break;
            }
        }


        self::setCash($key, $returnData);

        if (!$returnData) {
            if($options['page']){
                $returnData = '[]';
            }else{
                return false;
            }
        }

        return json_decode($returnData, $assoc);
    }
    
    private static function log($request, $response, $status, $context) {

        $request_escape = mysqli_real_escape_string(self::$connection, $request);
        $response_escape = mysqli_real_escape_string(self::$connection, $response);
        $context_escape = mysqli_real_escape_string(self::$connection, $context);
        $status_escape = mysqli_real_escape_string(self::$connection, $status);


        $query = " INSERT INTO wca_api_logs "
                . "(`request`,`response`,`context`,`status`,`version`) "
                . "VALUES"
                . "('$request_escape',"
                . "'$response_escape',"
                . "'$context_escape',"
                . "'$status_escape',"
                . "'" . self::VERSION . "')";

        if (!mysqli_query(self::$connection, $query)) {
            echo mysqli_error(self::$connection);
        }
    }

    private static function getCash($key) {

        $query = ("SELECT `value` "
                . " FROM wca_api_cash "
                . " WHERE `key`='$key' "
                . " AND TIMESTAMPDIFF(MINUTE,timestamp,now()) < " . self::MINUTES);

        $result = mysqli_query(self::$connection, $query);
        $row = $result->fetch_assoc();

        if (isset($row['value'])) {
            return $row['value'];
        } else {
            return false;
        }
    }

    private static function setCash($key, $value) {

        $key_escape = mysqli_real_escape_string(self::$connection, $key);
        $value_escape = mysqli_real_escape_string(self::$connection, $value);

        $query = " REPLACE INTO wca_api_cash "
                . "(`key`,`value`,`version`) "
                . "VALUES"
                . "('$key_escape',"
                . "'$value_escape',"
                . "'" . self::VERSION . "')";

        if (!mysqli_query(self::$connection, $query)) {
            echo mysqli_error(self::$connection);
        }
    }

    public static function init() {
        $queries = [];
        $errors = [];

        $queries['wca_api_logs'] = "
            CREATE TABLE `wca_api_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `request` varchar(255) DEFAULT NULL,
                `response` longtext DEFAULT NULL,
                `context` varchar(255) DEFAULT NULL,
                `status` int(3) DEFAULT NULL,
                `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `version` varchar(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        $queries['wca_api_cash'] = "
            CREATE TABLE `wca_api_cash` (
                `key` varchar(255) NOT NULL,
                `value` longtext DEFAULT NULL,
                `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `version` varchar(11) DEFAULT NULL,
                PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        foreach ($queries as $table => $query) {
            if (!mysqli_query(self::$connection, $query)) {
                $errors[$table] = mysqli_error(self::$connection);
            }
        }

        if (sizeof($errors)) {
            trigger_error("wca.api.createTables: " . json_encode($errors), E_USER_ERROR);
        }
    }

}
