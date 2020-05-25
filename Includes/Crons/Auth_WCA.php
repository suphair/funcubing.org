<?php

function isAuth( $data ){
  return preg_match('#You are already signed in#Usi',$data);
}

function request_WCA($url,$post = 0){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url ); // отправляем на
  curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
  curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
  curl_setopt($ch, CURLOPT_POST, $post!==0 ); // использовать данные в post
  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  $data = curl_exec($ch);
  $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
  
  curl_close($ch);
  return $data;
}

$sign_in="https://www.worldcubeassociation.org/users/sign_in";
$competition='ramenskoeopen2019';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/competitions/$competition/wcif" );
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
$status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
$result=json_decode($data,true);
print_r($result); echo ($status);

if(isset($result['error']) and $result['error']=='Not logged in'){
    
    
    $data = request_WCA($sign_in,0);    
    $data = str_get_html($data);
    $token=$data->find('input[name="authenticity_token"]',0)->value;
    
    $auth = array(
      'user[login]'=>'suphair@gmail.com',
      'user[password]'=>'kostyaWrN',
      'authenticity_token'=>$data->find('input[name="authenticity_token"]',0)->value,
    );
    $data->clear();
    unset($data);
    echo $token;
    $data = request_WCA($sign_in,$auth);
    $data = str_get_html($data);
    echo ($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/competitions/$competition/wcif" );
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
    curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);    
    $result=json_decode($data,true);
    print_r($result); echo ($status);
}

exit();
