<?php
function GeneratePyraminx4x4x4(){
    $Sides=['R','L','U','B'];
    $Moves=["","'"];
    
    $Layers=[1,2];
    
    $layers_lock=array();
    $side_lock='';
    $small_lock=false;
    $Prev='';
    $tmp='';
    for($i=0;$i<40;$i++){
        $Side=$Sides[array_rand($Sides)];
        
        if(!$side_lock){
            $side_lock=$Side;
        }
        
        if($small_lock){
            $Layer=2;
            $small_lock=false;
        }else{
            $Layer=$Layers[array_rand($Layers)];
            $small_lock=($Layer==1);
        }
        
        if(!in_array($Layer.$Side,$layers_lock)){
            $Move=$Moves[array_rand($Moves)];
            $tmp.=$Layer.$Side.'w'.$Move.' '; 
            
            if($side_lock!=$Side){$side_lock=$Side;$layers_lock=array();}
            $layers_lock[]=$Layer.$Side;
        }else{
            $i--;
        }
    }
    $tmp=str_replace(
            array('1Rw','1Lw','1Uw','1Bw'),
            array('R','L','U','B'),$tmp);
    $tmp=str_replace(
            array('2Rw','2Lw','2Uw','2Bw'),
            array('Rw','Lw','Uw','Bw'),$tmp);
 
    foreach(['l','r','b','u'] as $corner){
        $r=rand(1,3);
        if($r==2){
            $tmp.=$corner.' ';
        }
        if($r==3){
            $tmp.=$corner."' ";
        }
    }
    
    return trim($tmp);
}