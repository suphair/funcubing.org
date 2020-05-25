<?php 
function GenerateKilominx(){
    $moveR=array("R++","R--");
    $moveD=array("D++","D--");
    $moveU=array("U ","U'");
    $str="";
    for($j=1;$j<=3;$j++){
        for($i=1;$i<=5;$i++){
            $str.=$moveR[array_rand($moveR)]." ";  
            $str.=$moveD[array_rand($moveD)]." ";
        }
        $str.=$moveU[array_rand($moveU)]." ";
        if($j==1 || $j==2){
            $str.=" & ";
        }
    }

    $str=trim($str);
    return $str;
}