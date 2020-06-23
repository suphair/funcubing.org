<?php

namespace Suphair;

class Error {

    const VERSION = '1.0.5';
    const _NEW = 'new';
    const _DONE = 'done';
    const _SKIP = 'skip';
    const _WORK = 'work';

    private static $echo;

    const DIR = 'suphair_error';

    static function handler($errno, $errstr, $errfile, $errline) {
        $errorCodes = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'CoreError',
            E_CORE_WARNING => 'CoreWarning',
            E_COMPILE_ERROR => 'CompileError',
            E_COMPILE_WARNING => 'CompileWarning',
            E_USER_ERROR => 'UserError',
            E_USER_WARNING => 'UserWarning',
            E_USER_NOTICE => 'UserNotice',
            E_DEPRECATED => 'Depricated',
            E_USER_DEPRECATED => 'UserDepricated'
        ];

        $time = date("Y-m-d H:i:s");
        if (isset($errorCodes[$errno])) {
            $errcode = $errorCodes[$errno];
        } else {
            $errcode = $errno;
        }
        $message = "$errcode at $time in $errfile ($errline)";

        $backtrace = debug_backtrace();

        foreach ($backtrace as $key => $value) {
            if (isset($value['function'])
                    and $value['function'] == 'trigger_error') {
                unset($backtrace[$key]);
            }
            if (isset($value['class'])
                    and $value['class'] == 'Suphair\Error') {
                unset($backtrace[$key]);
            }
        }

        $backtrace = array_reverse($backtrace);
        $cash = md5($errno . $errstr . $errfile . $errline);
        $dir = self::dir();

        $number = false;
        $newError = true;
        $maxNumber = 0;
        foreach (scandir($dir) as $file) {
            $explode = explode("_", $file);
            if (sizeof($explode) != 4) {
                continue;
            }
            $maxNumber = max([$maxNumber, $explode[0]]);
            if ($explode[2] == $cash
                    and in_array($explode[3], [self::_NEW, self::_WORK])) {
                $number = $explode[0];
                $newError = false;
            }
        }

        if (!$number) {
            $number = $maxNumber + 1;
        }

        $text = "Error #$number: $cash<br><br>"
                . "$message<br>"
                . "$errstr<br><br>"
                . "<b>SERVER</b><br>"
                . "<pre>" . print_r($_SERVER, true) . "</pre>"
                . "<b>POST</b><br>"
                . "<pre>" . print_r($_POST, true) . "</pre>"
                . "<b>SESSION</b><br>"
                . "<pre>" . print_r($_SESSION, true) . "</pre>"
                . "<b>debug_backtrace</b><br>"
                . "<pre>" . print_r($backtrace, true) . "</pre>"
                . "<b>VERSION</b><br>"
                . self :: VERSION;

        if (self::$echo) {
            echo $text;
        }

        $text = str_replace("<br>", "\r\n", $text);
        $text = str_replace(["<b>", "</b>", "<pre>", "</pre>"], "", $text);

        if ($newError) {
            $handle = fopen($dir . $number . "_" . $errcode . "_" . $cash . '_' . self::_NEW, "a");
            fwrite($handle, $text);
            fclose($handle);
        }

        if (self::$echo
                or $errno == E_ERROR
                or $errno == E_USER_ERROR
                or $errno == E_CORE_ERROR
                or $errno == E_COMPILE_ERROR) {
            exit("<script>alert('Error: #$number')</script><p>Error: #$number</p>");
        } else {
            return false;
        }
    }

    static function shutdown() {
        $err = error_get_last();
        if (is_null($err)) {
            return;
        }
        switch ($err['type']) {
            case E_DEPRECATED:
                $type = E_USER_DEPRECATED;
                break;
            case E_NOTICE:
                $type = E_USER_NOTICE;
                break;
            case E_WARNING:
                $type = E_USER_WARNING;
                break;
            default:
                $type = E_USER_ERROR;
        }

        if (!in_array($err['type'], [E_WARNING, E_NOTICE])) {
            trigger_error(
                    '[shutdown] ' . print_r($err, true), $type
            );
        }
    }

    private static function dir() {
        return __DIR__ . "/" . self::DIR . "/logs/";
    }

    static function register($echo = false) {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        set_error_handler("Suphair\Error::handler");
        self::$echo = $echo;
        $dir = self::dir();
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        register_shutdown_function("Suphair\Error::shutdown");
    }

    static function getNew() {
        return self::get(self::_NEW);
    }

    static function getWork() {
        return self::get(self::_WORK);
    }

    static function getDone() {
        return self::get(self::_DONE);
    }

    static function getSkip() {
        return self::get(self::_SKIP);
    }

    static function getAll() {
        return self::get();
    }

    private static function get($status = false) {
        $result = [];
        $dir = self::dir();
        foreach (scandir($dir) as $file) {
            $explode = explode("_", $file);
            if (sizeof($explode) == 4
                    and ( $explode[3] == $status
                    or!$status)) {
                $result[$explode[0]] = [
                    'err' => $explode[1],
                    'hash' => $explode[2],
                    'status' => $explode[3],
                    'file' => 'logs/' . $file,
                    'time' => filectime("$dir/$file")
                ];
            }
        }
        ksort($result);
        return($result);
    }

    static function done($id) {
        self::setStatus($id, self::_DONE);
    }

    static function work($id) {
        self::setStatus($id, self::_WORK);
    }

    static function skip($id) {
        for ($i = 1; $i <= $id; $i++) {
            $status = self::getStatus($i);
            if (in_array($status, [self::_NEW, self::_WORK])) {
                self::setStatus($i, self::_SKIP);
            }
        }
    }

    private static function getStatus($id) {
        if (!$id) {
            return;
        }
        $dir = self::dir();
        foreach (scandir($dir) as $file) {
            $explode = explode("_", $file);
            if (sizeof($explode) == 4
                    and $explode[0] == $id) {
                return $explode[3];
            }
        }
    }

    private static function setStatus($id, $status) {
        if (!$id) {
            return;
        }
        $dir = self::dir();
        foreach (scandir($dir) as $file) {
            $explode = explode("_", $file);
            if (sizeof($explode) == 4
                    and $explode[0] == $id) {
                $explode[3] = $status;
                $newFile = implode("_", $explode);
                rename($dir . "/" . $file
                        , $dir . "/" . $newFile);
            }
        }
    }

}
