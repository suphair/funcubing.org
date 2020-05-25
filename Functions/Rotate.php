<?php
 function Rotate($CoorColor,$circles,$move,$direct){
    foreach($circles[$move] as $circle){
        if(!$direct){
            $tmp=$CoorColor[$circle[0][0]][$circle[0][1]];
            for($i=0;$i<sizeof($circle)-1;$i++){
                $CoorColor[$circle[$i][0]][$circle[$i][1]]=$CoorColor[$circle[$i+1][0]][$circle[$i+1][1]];
            }
            $CoorColor[$circle[sizeof($circle)-1][0]][$circle[sizeof($circle)-1][1]]=$tmp;   
        }else{            
            $tmp=$CoorColor[$circle[sizeof($circle)-1][0]][$circle[sizeof($circle)-1][1]];
            for($i=sizeof($circle)-1;$i>0;$i--){
                $CoorColor[$circle[$i][0]][$circle[$i][1]]=$CoorColor[$circle[$i-1][0]][$circle[$i-1][1]];
            }
           $CoorColor[$circle[0][0]][$circle[0][1]]=$tmp;
        }
      }
      
     
   return $CoorColor;    
  }
  
 