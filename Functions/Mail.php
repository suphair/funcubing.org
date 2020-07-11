<?php

function sendMail($to, $subject, $body) {

    $smpt = new smtp(
            db::connection()
            , config::get('SMTP', 'username')
            , config::get('SMTP', 'password')
            , config::get('SMTP', 'host')
            , config::get('SMTP', 'port'));
    $result = $smpt->send(
            $to
            , $subject
            , $body
            , config::get('SMTP', 'from')
            , config::get('SMTP', 'username')
    );
    return $result;
}
