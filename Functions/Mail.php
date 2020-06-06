<?php

function SendMail($to, $subject, $body) {

    $smpt = new Suphair\Smtp(
            DataBaseClass::getConection()
            , Suphair \ Config :: get('SMTP', 'username')
            , Suphair \ Config :: get('SMTP', 'password')
            , Suphair \ Config :: get('SMTP', 'host')
            , Suphair \ Config :: get('SMTP', 'port'));
    $result = $smpt->send(
            $to
            , $subject
            , $body
            , Suphair \ Config :: get('SMTP', 'from')
            , Suphair \ Config :: get('SMTP', 'username')
    );
    return $result;
}
