<?php

function GenerateDino(){
     $str="";
    $move=array("R","L");
    $ext=array(" ","'");
    $prev="";
    for($i=1;$i<=4;$i++){
        $m=rand(1,4); 
        for($j=1;$j<=4;$j++){
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
        $str.="x ";
        
        if($i==2){
            $str.=" & ";
       }
    }
    return $str;
}