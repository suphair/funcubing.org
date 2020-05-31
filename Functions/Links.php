<?php

function GetUrlWCA(){
   $scope= GetIni('WCA_AUTH','scope');
   $url_refer = PageIndex().GetIni('WCA_AUTH','url_refer');
   if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false){
       $url_refer="http:".$url_refer;
   }else{
       $url_refer="https:".$url_refer;
   }
    $client_id = GetIni('WCA_AUTH','client_id');
    
    return "https://www.worldcubeassociation.org/oauth/authorize?client_id=$client_id&redirect_uri=".urlencode($url_refer)."&response_type=code&scope=$scope";
}