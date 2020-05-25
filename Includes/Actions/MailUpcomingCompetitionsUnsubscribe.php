<?php $competitor=GetCompetitorData();
if($competitor){
    DataBaseClass::Query("Update MailUpcomingCompetitions set Status=0 where Competitor={$competitor->id}");
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();
