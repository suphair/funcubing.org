<?php

function Generate2x2x3(){
    do{
        $solve=Generate2x2x3Attempt(); 
    }while(!$solve);
    return $solve;    
}


function Generate2x2x3Attempt(){
    $move=array("D ","D'","D2","U ","U'","U2","F2","R2","L2");
    $str="";
    $prev="";
    $Us=0;
    $Fs=0;
    $Ds=0;
    $UDs=0;
    $LRs=0;
    $Lenght=12;
    for($i=1;$i<=$Lenght;$i++){
      $rand=$move[array_rand($move)];
        if($prev!=$rand[0]){
            $pLRs=$LRs;
            $pUDs=$UDs;
            $pUs=$Us;
            $pDs=$Ds;
            $pFs=$Fs;

            switch($rand[0]){
                case "U": $UDs++; $Fs=0; $Us++; $Ds=0; $LRs=0;  break;
                case "D": $UDs++; $Fs=0; $Us=0; $Ds++; $LRs=0;  break;
                case "F": $UDs=0; $Fs++; $Us=0; $Ds=0; $LRs=0;  break;
                case "R": $UDs=0; $Fs=0; $Us=0; $Ds=0; $LRs++;  break;
                case "L": $UDs=0; $Fs=0; $Us=0; $Ds=0; $LRs++;  break;
            }
            if($UDs==3 or $Fs==2 or $Us==2 or $Ds==2 or $LRs==2){
                $i--;    

                $UDs=$pUDs;
                $Fs=$pFs;
                $Us=$pUs;
                $Ds=$pDs;
                $LRs=$pLRs;
            }else{                
                $str.=$rand." ";
                $prev=$rand[0];

                if($i==ceil($Lenght/2)){
                    $str.=" & ";
                }
            }
        }else{
            $i--;
        }
    }

    $str=trim($str);    
    
    if(CheckSolve2x2x3($str,"")){
        return $str;
    }
    
    $min_solve=4;
    for($d=1;$d<=$min_solve;$d++){
        $HelperAlgs= file("2x2x3Helper/algs$d.txt",true);

        foreach($HelperAlgs as $alg){ 
            if(CheckSolve2x2x3($str,$alg)){
                return false;
                //return $str.' '.$alg."(".$d.")";
            }  
        }
    }
    
    return $str;
}