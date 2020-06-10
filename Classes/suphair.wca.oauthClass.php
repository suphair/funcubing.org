<?php

namespace Suphair \ Wca;

class Oauth {

    protected static $scope = 'public';
    protected static $clientId;
    protected static $urlRefer;
    protected static $clientSecret;
    protected static $connection;

    const VERSION = '1.1.1';

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

    static function set($clientId, $clientSecret, $scope, $urlRefer, $connection) {
        self::$scope = $scope;
        self::$clientId = $clientId;
        self::$clientSecret = $clientSecret;
        $http = filter_input(INPUT_SERVER, 'SERVER_NAME') == 'localhost' ? "http" : "https";
        self::$urlRefer = $http . ':' . $urlRefer;
        self::$connection = $connection;
    }

    static function url() {
        $_SESSION['suphair.wca.oauth.request_uri'] = filter_input(INPUT_SERVER, 'REQUEST_URI');

        return "https://www.worldcubeassociation.org/oauth/authorize?"
                . "client_id=" . self::$clientId . "&"
                . "redirect_uri=" . urlencode(self::$urlRefer) . "&"
                . "response_type=code&"
                . "scope=" . self::$scope . "";
    }

    static function location() {
        header("Location: {$_SESSION['suphair.wca.oauth.request_uri']}");
        exit();
    }

    static function authorize() {

        if (filter_input(INPUT_GET, 'error') == 'access_denied') {
            self::location();
        }

        $code = filter_input(INPUT_GET, 'code');
        if ($code) {
            $postdata = http_build_query(
                    [
                        'grant_type' => 'authorization_code',
                        'client_id' => self::$clientId,
                        'client_secret' => self::$clientSecret,
                        'code' => $code,
                        'redirect_uri' => self::$urlRefer
                    ]
            );
            $ch = curl_init("https://www.worldcubeassociation.org/oauth/token");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded', '"Accept-Language: en-us,en;q=0.5";']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            if (isset(json_decode($result)->error)) {
                trigger_error("wca.oauth.getToken: $result <br>" . print_r($postdata, true), E_USER_ERROR);
            }

            if ($status != 200) {
                trigger_error("wca.oauth.getToken: $status<br>$url", E_USER_ERROR);
            }

            $accessToken = json_decode($result)->access_token;

            if (!$accessToken) {
                self::location();
            }

            $ch = curl_init('https://www.worldcubeassociation.org/api/v0/me');
            $authorization = "Authorization: Bearer $accessToken";
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            if ($status != 200) {
                trigger_error("wca.oauth.getMe: $status<br>$url", E_USER_ERROR);
            }

            if (isset(json_decode($result)->me->id)) {
                $me = json_decode($result)->me;
                self::log($me);
                $_SESSION['suphair.wca.oauth.me'] = $me;
                return $me;
            } else {
                $_SESSION['suphair.wca.oauth.me'] = false;
                self::location();
            }
        }
    }

    private static function log($me) {

        if (isset($me->id) and is_numeric($me->id)) {
            $id_escape = mysqli_real_escape_string(
                    self::$connection, $me->id
            );
        } else {
            return;
        }
        if (isset($me->name)) {
            $name_escape = mysqli_real_escape_string(
                    self::$connection, $me->name
            );
        } else {
            $name_escape = FALSE;
        }

        if (isset($me->wca_id)) {
            $wcaid_escape = mysqli_real_escape_string(
                    self::$connection, $me->wca_id
            );
        } else {
            $wcaid_escape = FALSE;
        }
        if (isset($me->country_iso2)) {
            $countryiso2_escape = mysqli_real_escape_string(
                    self::$connection, $me->country_iso2
            );
        } else {
            $countryiso2_escape = FALSE;
        }
        $query = " INSERT INTO wca_oauth_logs "
                . "(`me_id`,`me_name`,`me_wcaid`,`me_countryiso2`,`version`) "
                . "VALUES"
                . "('$id_escape',"
                . "'$name_escape',"
                . "'$wcaid_escape',"
                . "'$countryiso2_escape',"
                . "'" . self::VERSION . "')";

        mysqli_query(self::$connection, $query);
        return mysqli_insert_id(self::$connection);
    }

    public static function init($connection) {
        $queries = [];
        $errors = [];

        $queries['wca_oauth_logs'] = "
            CREATE TABLE `wca_oauth_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `me_id` int(11) DEFAULT NULL,
                `me_name` varchar(255) DEFAULT NULL,
                `me_wcaid` varchar(10) DEFAULT NULL,
                `me_countryiso2` varchar(2) DEFAULT NULL,
                `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `version` varchar(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        foreach ($queries as $table => $query) {
            if (!mysqli_query($connection, $query)) {
                $errors[$table] = mysqli_error($connection);
            }
        }

        if (sizeof($errors)) {
            trigger_error("wca.oauth.createTables: " . json_encode($errors), E_USER_ERROR);
        }
    }

}
