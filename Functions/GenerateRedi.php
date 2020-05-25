<?php

function GenerateRedi(){
     $str="";
    $move=array("R","L");
    $ext=array(" ","'");
    $prev="";
    for($i=1;$i<=8;$i++){
        $m=rand(2,5); 
        for($j=1;$j<=5;$j++){
            if($j<=$m){
                $rand=$move[array_rand($move)].$ext[array_rand($ext)];
                if($prev!=$rand[0]){
                    $str.=$rand." ";
                    $prev=$rand[0];
                }else{
                   $j--; 
                }
            }else{
                //$str.="   ";
            }
        }
        $str.="x  ";
        
        if($i%3==0){
            //$str.=" & ";
       }
    }
    return $str;
}