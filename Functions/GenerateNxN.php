<?php
function GenerateNxN($size,$lenght,$cut){
    
    $Sides=array('R','L','U','D','B','F');
    $Moves=array("","'","2");
    $layers_lock_left=array();
    if($size%2==0){
        $layers_lock_left[]=($size/2)."L";
        $layers_lock_left[]=($size/2)."D";
        $layers_lock_left[]=($size/2)."B";
    }
    $Layers=array();
    for($i=1;$i<=$size/2;$i++){
        $Layers[]=$i;
    }
    
    
    $layers_lock=array();
    $side_lock='';
    $Prev='';
    $tmp='';
    for($i=0;$i<100;$i++){
        $Side=$Sides[array_rand($Sides)];
        $Layer=$Layers[array_rand($Layers)];
        if(!$side_lock){
            $side_lock=$Side;
        }
        
        if(!in_array($Layer.$Side,$layers_lock) and !in_array($Layer.$Side,$layers_lock_left)){
            $Move=$Moves[array_rand($Moves)];
            $tmp.=$Layer.$Side.'w'.$Move.' '; 
            
            if(in_array($side_lock,array('R','L')) and !in_array($Side,array('R','L'))){$side_lock=$Side;$layers_lock=array();}
            if(in_array($side_lock,array('U','D')) and !in_array($Side,array('U','D'))){$side_lock=$Side;$layers_lock=array();}
            if(in_array($side_lock,array('B','F')) and !in_array($Side,array('F','B'))){$side_lock=$Side;$layers_lock=array();}
            
            $layers_lock[]=$Layer.$Side;
        }else{
            $i--;
        }
    }
    $tmp=str_replace(
            array('1Rw','1Lw','1Uw','1Dw','1Bw','1Fw'),
            array('R','L','U','D','B','F'),$tmp);
    
    $ms=explode(" ",$tmp);
    $res='';
    foreach($ms as $i=>$m){
        $res.=substr($m.'    ',0,5);
        if($i%10==9 and $i!=(sizeof($ms)-2)) $res.=" & ";
    }
   
    return trim($res);
    
}