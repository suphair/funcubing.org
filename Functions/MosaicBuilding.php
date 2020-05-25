<?php 

function DeleteDirectoryFiles($id){
    foreach(glob("Images/$id/*") as $name){
        if(is_dir($name)){
            foreach(glob("$name/*") as $name2){
                if(is_dir($name2)){
                    foreach(glob("$name2/*") as $name3){            
                        unlink($name3);        
                    }
                    rmdir($name2);        
                }else{
                    unlink($name2);        
                }
            }
            rmdir($name);
        }else{
            unlink($name);        
        }
    }
    if(file_exists("Images/$id")){
        rmdir("Images/$id");
    }
}


function rand_str($length = 12){
    $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, strlen($chars)) - 1, 1);
    }
return $string;
}

function color_hsl($a, $b){
   $a1=array($a['red'],$a['green'],$a['blue']);
   $b1=array($b['red'],$b['green'],$b['blue']);
   $a2=rgb2hsl($a1);
   $b2=rgb2hsl($b1); 
   
    if($a2[1]>$b2[1]) return 1;
    if($a2[1]<$b2[1]) return -1;    
    
    return 0;
}

function rgb2hsl($rgb) {
    list($r, $g, $b) = $rgb;
   
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $chroma = $max - $min;
    $l = ($max + $min) / 2;
    if ($chroma == 0){
        $h = 0;
        $s = 0;
    }else{
        switch($max) {
            case $r:
                $h_ = fmod((($g - $b) / $chroma), 6);
                if($h_ < 0) $h_ = (6 - fmod(abs($h_), 6)); // Bugfix: fmod() returns wrong values for negative numbers
                break;
            
            case $g:
                $h_ = ($b - $r) / $chroma + 2;
                break;
            
            case $b:
                $h_ = ($r - $g) / $chroma + 4;
                break;
            default:
                break;
        }
        $h = $h_ / 6;
        $s = 1 - abs(2 * $l - 1);
    }
    return array($h, $s, $l);
}