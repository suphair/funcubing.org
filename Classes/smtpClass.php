<?php

class smtp {

    private $host;
    private $port;
    private $username;
    private $password;
    private $connection;

    const VERSION = '2.0.0';

    public function __construct($connection, $username, $password, $host, $port = 25) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->connection = $connection;
    }

    function send($to, $subject, $message, $from, $fromName) {
        $contentMail = $this->getContentMail($subject, $message, $from, $fromName);
        $result = $this->_send($to, $contentMail);
        $this->log($to, $subject, $message, "$from<$fromName>", $result);
        return $result;
    }

    private function _send($to, $contentMail) {
        if (!$socket = @fsockopen($this->host, $this->port, $errorNumber, $errorDescription, 30)) {
            return "$errorNumber:$errorDescription";
        }
        if (!$this->_parseServer($socket, "220")) {
            return 'Connection error';
        }

        $server_name = $_SERVER["SERVER_NAME"];
        fputs($socket, "EHLO $server_name\r\n");
        if (!$this->_parseServer($socket, "250")) {
            // если сервер не ответил на EHLO, то отправляем HELO
            fputs($socket, "HELO $server_name\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                return 'Error of command sending: HELO';
            }
        }

        fputs($socket, "AUTH LOGIN\r\n");
        if (!$this->_parseServer($socket, "334")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, base64_encode($this->username) . "\r\n");
        if (!$this->_parseServer($socket, "334")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, base64_encode($this->password) . "\r\n");
        if (!$this->_parseServer($socket, "235")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, "MAIL FROM: <{$this->username}>\r\n");
        if (!$this->_parseServer($socket, "250")) {
            fclose($socket);
            return 'Error of command sending: MAIL FROM';
        }

        $emails_to_array = explode(',', str_replace(" ", "", $to));
        foreach ($emails_to_array as $email) {
            fputs($socket, "RCPT TO: <{$email}>\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                return 'Error of command sending: RCPT TO';
            }
        }

        fputs($socket, "DATA\r\n");
        if (!$this->_parseServer($socket, "354")) {
            fclose($socket);
            return 'Error of command sending: DATA';
        }

        fputs($socket, "$contentMail\r\n.\r\n");
        if (!$this->_parseServer($socket, "250")) {
            fclose($socket);
            return 'E-mail didn\'t sent';
        }

        fputs($socket, "QUIT\r\n");
        fclose($socket);
        return true;
    }

    private function _parseServer($socket, $response) {
        $responseServer='xxx';
        while (substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;
    }

    private function getContentMail($subject, $message, $from, $fromName) {
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "=?=\r\n";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: $from <$fromName>\r\n";
        $contentMail .= "$headers\r\n";
        $contentMail .= "$message\r\n";
        return $contentMail;
    }

    private function log($to, $subject, $message, $from, $result) {

        $to_escape = mysqli_real_escape_string($this->connection, $to);
        $from_escape = mysqli_real_escape_string($this->connection, $from);
        $result_escape = mysqli_real_escape_string($this->connection, $result);
        $subject_escape = mysqli_real_escape_string($this->connection, $subject);
        $message_escape = mysqli_real_escape_string($this->connection, $message);
        $query = " INSERT INTO smtp_logs "
                . "(`to`,`from`,`result`,`subject`,`message`,`version`) "
                . "VALUES"
                . "('$to_escape',"
                . "'$from_escape',"
                . "'$result_escape',"
                . "'$subject_escape',"
                . "'$message_escape',"
                . "'" . self::VERSION . "')";
        mysqli_query($this->connection, $query);
        return mysqli_insert_id($this->connection);
    }

    public static function init($connection) {
        $queries = [];
        $errors = [];

        $queries['smtp_log'] = "
            CREATE TABLE `smtp_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `to` varchar(255) DEFAULT NULL,
                `subject` varchar(255) DEFAULT NULL,
                `message` text DEFAULT NULL,
                `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `from` varchar(255) DEFAULT NULL,
                `result` varchar(255) DEFAULT NULL,
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
            trigger_error("smtp.createTables: " . json_encode($errors), E_USER_ERROR);
        }
    }

}
