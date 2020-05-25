<?php

function IconAttempt($discipline,$n){
    $dir="Image/".$discipline;
    if(@dir("Image/".$discipline)){
        foreach (scandir($dir) as $filename){
            if(strpos($filename,$n."_")===0){
                return $dir."/".$filename;
            }
        }
    }
    return false;    
}
function IconAttempt_DisciplineName($name,$discipline,$k){
    
    $name=str_replace(".png","",$name);
    $name=str_replace("Image/","",$name);
    $name=str_replace("$discipline/","",$name);
    $name=str_replace($k."_","",$name);
    return $name;
    
}