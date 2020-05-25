<?php

$Ceil=50;
$Border=40;
$D=40;

$Center=array(
       'F'=>array('x'=>$size,'y'=>$size,'Color'=>'Green'),
        'D'=>array('x'=>$size,'y'=>$size*2,'Color'=>'Yellow'),
        'L'=>array('x'=>0,'y'=>$size,'Color'=>'Orange'),
      
        'U'=>array('x'=>$size,'y'=>0,'Color'=>'White'),
        'R'=>array('x'=>$size*2,'y'=>$size,'Color'=>'Red'),
        'B'=>array('x'=>$size*3,'y'=>$size,'Color'=>'Blue'),
  
  );
  
   $Coor=array();
   for($x=0;$x<$size;$x++)
   for($y=0;$y<$size;$y++)
        $Coor[$x.'-'.$y]=array('x'=>$x,'y'=>$y);
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  $circles=array();
  
  for($i=0;$i<$size/2;$i++){
    for($j=0;$j<floor($size/2);$j++){
      foreach(array("U","R","B","L","D","F") as $L){  
        $circles['1'.$L][]=array(
             array($L,$i.'-'.$j),array($L,($size-$j-1).'-'.$i),array($L,($size-$i-1).'-'.($size-$j-1)),array($L,$j.'-'.($size-$i-1))
        );
      }
    }
  }
  
  for($i=0;$i<$size;$i++){
     $j=$size-$i-1;
     
     
     for($l=1;$l<=$size/2;$l++){
        $circles[$l.'R'][]=array(
        array('F',($size-$l)."-$i"),
        array('U',($size-$l)."-$i"),
        array('B',($l-1)."-$j"),
        array('D',($size-$l)."-$i"));
        
        $circles[$l.'L'][]=array(
        array('U',($l-1)."-$i"),
        array('F',($l-1)."-$i"),
        array('D',($l-1)."-$i"),
        array('B',($size-$l)."-$j"));
        
        $circles[$l.'U'][]=array(
        array('R',"$i-".($l-1)),
        array('F',"$i-".($l-1)),
        array('L',"$i-".($l-1)),
        array('B',"$i-".($l-1)));
        
        $circles[$l.'D'][]=array(
        array('F',"$j-".($size-$l)),
        array('R',"$j-".($size-$l)),
        array('B',"$j-".($size-$l)),
        array('L',"$j-".($size-$l)));
        
        $circles[$l.'F'][]=array(
        array('L',($size-$l)."-$i"),
        array('U',"$j-".($size-$l)),
        array('R',($l-1)."-$j"),
        array('D',"$i-".($l-1)));
        
        $circles[$l.'B'][]=array(
        array('U',"$i-".($l-1)),
        array('L',($l-1)."-$j"),
        array('D',"$j-".($size-$l)),
        array('R',($size-$l)."-$i"));
        
     }
  }
  
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);

foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    
    if($move<>"" and in_array($move[0],array("U","R","B","L","D","F"))){
        $move=str_replace(array("U","R","B","L","D","F"),array("1Uw","1Rw","1Bw","1Lw","1Dw","1Fw"),$move);
    }


    if($move<>"" and isset($move[1]) and in_array($move[1],array("R","L","U","D","F","B"))){
        $direct=true;
        if(isset($move[3])){
            if($move[3]==" "){
                for($l=1;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],true);}
            }elseif($move[3]=="2"){
                for($l=1;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],true);}
                for($l=1;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],true);}
            }elseif($move[3]=='\''){
               for($l=1;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],false);}
                
            }
        }else{
            for($l=1;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],true);}
        }
        
    }
}  
    
$im= imagecreate($Border*2+$Ceil*$size*4+$D*3, $Border*2+$Ceil*$size*3+$D*2);
$white=imagecolorallocate($im,255,255,255);
$black=imagecolorallocate($im,0,0,0);

$Colors=array(
    'Red'=> imagecolorallocate($im,255,0,0),
    'Green'=> imagecolorallocate($im,49,127,67),
    'White'=> imagecolorallocate($im,255,255,255),
    'Blue'=> imagecolorallocate($im,0,0,255),
    'Yellow'=> imagecolorallocate($im,255,255,0),
    'Orange'=> imagecolorallocate($im,255,165,0)
);


foreach($Center as $n=>$center){
  foreach ($Coor as $c=>$coor){
        $Rects[]=array($center['x']+$coor['x'],$center['y']+$coor['y'],$center['x']+$coor['x']+1,$center['y']+$coor['y']+1,$Colors[$CoorColor[$n][$c]]);
  }
}


foreach($Rects as $Rect){
    imagesetthickness($im,5);
    $dx=floor($Rect[0]/$size)*$D;
    $dy=floor($Rect[1]/$size)*$D;
    imagefilledrectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $Rect[4]);
    imagerectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $black);
}
  
 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
