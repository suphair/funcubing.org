<?php
function DeleteFiles($folder){
    foreach(glob("$folder/*") as $name){
        if(!is_dir($name)){
            unlink($name); 
        }
    } 
}