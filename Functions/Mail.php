<?php
function SendMail($to, $subject, $body){

   if(strpos($_SERVER['PHP_SELF'],'/'. GetIni('LOCAL', 'PageBase').'/')!==false){
        $section="SMTP_LOCAL";
    }else{
        $section="SMTP";
    }
   
    $mailSMTP = new SendMailSmtpClass(
            GetIni($section,'username'),
            GetIni($section,'password'),
            GetIni($section,'host'),
            GetIni($section,'port'));

    $from = array(GetIni($section,'from'), GetIni($section,'username') );
    $result =  $mailSMTP->send($to, $subject, $body, $from); 
    
    $sql = "INSERT INTO `LogMail` (`To`, `Subject`, `Body`, `Result`)
    VALUES ('$to', '$subject', '".DataBaseClass::Escape($body)."','$result')";

    DataBaseClass::Query ($sql);    
    
    return $result;
}


Function SendAdmin($subject, $body){
    SendMail(GetIni('ADMIN','email'), $subject, $body);
}