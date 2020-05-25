<?php

function add_visit(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
    elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
    else $ip = $remote;
    $user_agent=@$_SERVER['HTTP_USER_AGENT'];

  /*  $bots=['YandexBot','YandexMobileBot','Googlebot','Mail.RU_Bot','SeznamBot','bingbot','coccocbot','SafeDNSBot','DuckDuckGo-Favicons-Bot',
'BLEXBot','msnbot','CCBot','TelegramBot','LinkpadBot' ,'SurdotlyBot','YandexMetrika','Applebot','PaperLiBot','Clarabot','bot@linkfluence.com',
        'Twitterbot','ZoomBot','SMTBot','Discordbot',' Nimbostratus-Bot',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36'
 
        ];

    $isBot=false;
    foreach($bots as $bot){   
        if(stripos($user_agent,$bot)!==false){           
            $isBot=true;
        }
    }
   */
    
    
        DataBaseClass::Query("Select * from Visit where IP='$ip' and Date=CURDATE() ");

        if(!is_array(DataBaseClass::getRow())){
            DataBaseClass::Query("Insert into Visit (IP,Date,User_Agent) values ('$ip',CURDATE(),'$user_agent') ");
        }    
        
        DataBaseClass::Query("Select count(distinct IP) count from Visit where User_Agent='$user_agent' having count(distinct IP)>10");
        if(DataBaseClass::getRow()['count']>10){
            DataBaseClass::Query("Update Visit  set Hidden=1 where User_Agent='$user_agent'");
        }
        
        DataBaseClass::Query("Update Visit set Hidden=1 where User_Agent like '%bot%' or User_Agent like '%Bot%'");
    
        
}
