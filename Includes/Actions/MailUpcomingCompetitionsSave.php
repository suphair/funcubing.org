<?php    
$competitor=GetCompetitorData();
if($competitor){
   
    if(isset($_POST['countries'])){
        $countries_post=$_POST['countries'];
    }else{
        $countries_post=[];
    }

    DataBaseClass::Query("Select * from Country where ISO2<>'' and Name<>'' order by Name");
    $countries=[];
    foreach(DataBaseClass::getRows() as $country){
        $countries[]=$country['ISO2'];
    }


    foreach($countries_post as $c=>$country){
        $country= DataBaseClass::Escape($country);
        if(!isset($country,$countries)){
            unset($countries_post[$c]);
        }
    }
    if(!sizeof($countries_post)){
        $countries_post[]=$competitor->country_iso2;
    }

    DataBaseClass::Query("Update MailUpcomingCompetitions set Country='".implode(',',$countries_post)."' "
            . "where Competitor={$competitor->id}");
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();