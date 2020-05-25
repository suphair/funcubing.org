<?php

function ResultString($r){
    $string="";
    if(isset($r['Attempt_IsDNF']) and $r['Attempt_IsDNF']){
        $string='DNF';
    }elseif(isset($r['Attempt_IsDNS']) and $r['Attempt_IsDNS']){
        $string='DNS';
    }else{
        if($r['Attempt_Minute']){
            $string=sprintf( "%d:%02d.%02d", $r['Attempt_Minute'],$r['Attempt_Second'],$r['Attempt_Milisecond']);
        }elseif($r['Attempt_Second']){
            $string=sprintf( "%2d.%02d", $r['Attempt_Second'],$r['Attempt_Milisecond']);
        }else{
            $string=sprintf( "0.%02d", $r['Attempt_Milisecond']);
        }
    }
    if($string=="0.00")$string="";
    return $string;
}
