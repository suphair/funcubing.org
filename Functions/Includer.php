<?php


function Includer__(){
    $request=getRequest();
    if(isset($request[0])){
    if(!in_array($request[0],["Actions","Crons"])){
        if(sizeof($request)>=2){
            IncludeExists('Includes/'.$request[0].'/'.$request[1].'.php');
        }else if(sizeof($request)<2 and $request[0]){
            IncludeExists('Includes/Pages/'.$request[0].'.php');
        }
    }
    }else{
        include 'Includes/Pages/index.php';
    }

}

function IncluderAction(){
    $request=getRequest();   
    if(sizeof($request)>=2){
        if(in_array($request[0],["Actions","Crons"])){
            $request[1]= explode("?", $request[1])[0];
            
            SetPostValues($request[1]);
            if($request[0]=='Crons' and !( CheckAdmin() or $_SERVER['HTTP_USER_AGENT']=='Wget/1.17.1 (linux-gnu)' or strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false)){
                echo '!! only CRON';
                exit();
            }

            IncludeExists('Includes/'.$request[0].'/'.$request[1].'.php');
        }
    }
}


function IncludeExists($file){
    if(file_exists($file)){
        include $file;
    }else{
        echo ">>".$file."<<";
        exit();
    }
}

function getRequest(){
    
    global $request;
    $request=explode("/",str_replace("/".getIni("LOCAL","PageBase")."/","/",$_SERVER['REQUEST_URI']));
    unset($request[0]);
    foreach($request as $n=>$v){
        $request[$n]=explode('?fbclid',$v)[0];
        if(!$v) unset($request[$n]);
        
    }
    $request=array_values($request);
    return $request;
}

function getRequestString(){
    
    return str_replace("/".getIni("LOCAL","PageBase")."/","/",$_SERVER['REQUEST_URI']);
}

function Request(){
     global $request;
    return $request;
}