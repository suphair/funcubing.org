<?php

$row = db::row("SELECT email, countries FROM announcements WHERE user='{$me->id}'");
if ($row) {
    $message = 'This is a test email for checking your subscription';
    $subject = "FunCubing: New competitions announce (test)";
    $message .= "<hr> Your email: {$row->email}; Tracked countries: " . $row->countries;
    $message .= "<br><a href='http://" . Pageindex() . "MailUpcomingCompetition'>Subscription management</a>";
    postSet('test', sendMail($row->email, $subject, $message));
}
