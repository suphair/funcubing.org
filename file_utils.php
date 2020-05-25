<?php

function RequireDir($dir){
    foreach (scandir($dir) as $filename){
        if(strpos($filename,".php")){
            require_once "$dir/$filename";
        }
    }
}

function GetIni($section,$param){
    $config = parse_ini_file('config.ini', true);
    if(isset($config[$section][$param])){
        return $config[$section][$param];
    }else{
        return "";
    }
}