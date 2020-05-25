<?php

$url_refer = PageIndex().GetIni('VK_AUTH','url_refer');
if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false){
   $url_refer="http:".$url_refer;
}else{
   $url_refer="https:".$url_refer;
}
$client_id = GetIni('VK_AUTH','client_id');
$client_secret = GetIni('VK_AUTH','client_secret');
    
$url = "https://api.vkontakte.ru/oauth/authorize?client_id=$client_id&redirect_uri=".urlencode($url_refer)."&response_type=code&scope=wall,offline";

unset($_SESSION['VK']);

$code="";
if(isset($_GET['code'])){
    $code=$_GET['code'];

    $postdata = http_build_query(
    array(
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code'=>$code,
        'redirect_uri'=>$url_refer
    )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents("https://api.vkontakte.ru/oauth/token", false, $context); 
    $access_token=json_decode($result)->access_token;
    
    
    $ch = curl_init("https://api.vk.com/method/wall.post?owner_id=1193451&message=TEST&access_token=$access_token&v=5.70");
    
    $result = curl_exec($ch);
    curl_close($ch);
}

?>