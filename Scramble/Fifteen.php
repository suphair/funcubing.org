<?php 
$Ceil=100;
$Border=10;
$D=0;
    
$Coor=array(
    0=>array(0=>1,1=>2,2=>3,3=>4),
    1=>array(0=>5,1=>6,2=>7,3=>8),
    2=>array(0=>9,1=>10,2=>11,3=>12),
    3=>array(0=>13,1=>14,2=>15,3=>0)
    );

$X=array(3,3);
//$scramble=str_replace('\r','',$scramble);

foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and in_array($move[0],array("U","D","L","R"))){
        $direct=$move[0];
        if(isset($move[1]) and is_numeric($move[1])){
            $count=$move[1]; 
        }else{
            $count=1;
        }
        
        
       if($direct=='U'){
           for($i=1;$i<=$count;$i++){
               $Coor[$X[0]+$i-1][$X[1]]=$Coor[$X[0]+$i][$X[1]];  
           }
           $X[0]=$X[0]+$count;
       }
       
       if($direct=='D'){
           for($i=1;$i<=$count;$i++){
               $Coor[$X[0]-$i+1][$X[1]]=$Coor[$X[0]-$i][$X[1]];
           }
           $X[0]=$X[0]-$count;
       }
                
       if($direct=='R'){
           for($i=1;$i<=$count;$i++){
               $Coor[$X[0]][$X[1]-$i+1]=$Coor[$X[0]][$X[1]-$i];  
           }
           $X[1]=$X[1]-$count;
       }
       
        if($direct=='L'){
           for($i=1;$i<=$count;$i++){
               $Coor[$X[0]][$X[1]+$i-1]=$Coor[$X[0]][$X[1]+$i];  
           }
           $X[1]=$X[1]+$count;
       }
        
    }
}
$Coor[$X[0]][$X[1]]=0;



$im= imagecreate($Border*2+$Ceil*4+$D*3, $Border*2+$Ceil*4+$D*3);
$white=imagecolorallocate($im,255,255,255);
$black=imagecolorallocate($im,0,0,0);


$color=imagecolorallocate($im,0,0,0);
for($i=0;$i<4;$i++){
    for($j=0;$j<4;$j++){
            $dx=floor($i)*$D;
            $dy=floor($j)*$D;
        imagerectangle($im, $dx+$Border+$Ceil*$i,$dy+$Border+$Ceil*$j,$dx+$Border+$Ceil*($i+1),$dy+$Border+$Ceil*($j+1), $black);
         
        if($Coor[$i][$j]){
            imagefttext($im, $Ceil/2, 0, $dx+$Border+$Ceil*$i+$Ceil*($Coor[$i][$j]>9?0.1:.3), $dy+$Border+$Ceil*$j+$Ceil*.7, $color, 'arial.ttf', $Coor[$i][$j]);
        }else{
            imagefttext($im, $Ceil/2, 0, $dx+$Border+$Ceil*$i+$Ceil*.35, $dy+$Border+$Ceil*$j+$Ceil*.75, $color, 'arial.ttf', '•');
        }
    }
}

imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>