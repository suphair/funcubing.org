<?php $competitor=GetCompetitorData();
if($competitor){
    DataBaseClass::Query("Update MailUpcomingCompetitions set Status=1, announced_at=now() where Competitor={$competitor->id}");
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();
