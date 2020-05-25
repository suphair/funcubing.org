<?php
function Generate3x3x2(){
    $move['R']=array("Rw2","R2 ");
    $move['U']=array("U ","U'","U2");
    $move['D']=array("D ","D'");
    $str="";
    $prev="";
    $Ls=0;
    $Rs=0;
    $Us=0;
    $Bs=0;
    $Fs=0;
    $Pattern="R U R U R U R U R D & R U R U R U R U R D & R U R U R U R U R D";
    $Pattern="R U R U R U R U R D & R U R U R U R U R D";
    
    $str='';
    for($l=0;$l<strlen($Pattern);$l++){
       if(isset($move[$Pattern[$l]])){           
          $str.=$move[$Pattern[$l]][array_rand($move[$Pattern[$l]])]; 
       }else{
           $str.=$Pattern[$l];
       } 
    }
    $str=trim($str);  
    return $str;
}