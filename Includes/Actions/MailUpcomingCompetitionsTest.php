<?php

$competitor = GetCompetitorData();

if($competitor){
    DataBaseClass::Query("Select * from MailUpcomingCompetitions where Competitor='{$competitor->id}'");
    $row=DataBaseClass::getRow();
    if(isset($row['Email'])){
        $message='This is a test email for checking your subscription';
        $subject="FunCubing: New competitions announce (test)";
        $message.="<hr> Your email: {$row['Email']}; Tracked countries: ".CountryNames($row['Country']);
        $message.="<br><a href='http://".Pageindex()."MailUpcomingCompetition'>Subscription management</a>";
        SetMessageName('mailTest',SendMail($row['Email'], $subject, $message));
    }
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();