<?php

function SendMail($to, $subject, $body) {

    if (strpos($_SERVER['PHP_SELF'], '/' . GetIni('LOCAL', 'PageBase') . '/') !== false) {
        $section = "SMTP_LOCAL";
    } else {
        $section = "SMTP";
    }

    $smpt = new Suphair\Smtp(
            DataBaseClass::getConection(), GetIni($section, 'username'), GetIni($section, 'password'), GetIni($section, 'host'), GetIni($section, 'port'));
    $result = $smpt->send($to, $subject, $body, GetIni($section, 'from'), GetIni($section, 'username'));
    return $result;
}

Function SendAdmin($subject, $body) {
    SendMail(GetIni('ADMIN', 'email'), $subject, $body);
}
