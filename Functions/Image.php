<?php
function GetParam($size,$font,$text){
    $return=array();
    $textbox = imagettfbbox($size, 0, $font, $text);
    $return['height']=$textbox[1] - $textbox[7];
    $return['weith']=$textbox[2] - $textbox[0];
    $return['dy']=$textbox[1];
    $return['dx']=$textbox[0];
    return $return;
} 