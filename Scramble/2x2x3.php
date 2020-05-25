<?php 
$Ceil=50;
$Border=10;
$D=10;

$Center22=array(
        'U'=>array('x'=>3,'y'=>1,'Color'=>'White'),
        'D'=>array('x'=>3,'y'=>6,'Color'=>'Yellow'),  
  );
  
$Center23=array(
        'F'=>array('x'=>3,'y'=>3.5,'Color'=>'Green'),
        'L'=>array('x'=>1,'y'=>3.5,'Color'=>'Orange'),
        'R'=>array('x'=>5,'y'=>3.5,'Color'=>'Red'),
        'B'=>array('x'=>7,'y'=>3.5,'Color'=>'Blue'),
  
  );
  
  
  $Coor22=array(
        'u'=>array('x'=>0,'y'=>-1),
        'r'=>array('x'=>0,'y'=>0),
        'd'=>array('x'=>-1,'y'=>0),
        'l'=>array('x'=>-1,'y'=>-1),
  );
  
  $Coor23=array(
        'u'=>array('x'=>0,'y'=>-1.5),
        'r'=>array('x'=>0,'y'=>.5),
        'd'=>array('x'=>-1,'y'=>.5),
        'l'=>array('x'=>-1,'y'=>-1.5),
        'R'=>array('x'=>0,'y'=>-.5),
        'L'=>array('x'=>-1,'y'=>-.5),
  );
  
  
  $CoorColor=array();
  foreach($Center22 as $n=>$center){
    foreach ($Coor22 as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  foreach($Center23 as $n=>$center){
    foreach ($Coor23 as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
     
  $circles=array(
            'R2'=>array(
                array('RR','RL'),
                array('FR','BL'),
                
                array('Uu','Du'),
                array('Ur','Dr'),
                
                array('Fu','Bd'),
                array('Fr','Bl'),              
                
                array('Ru','Rd'),
                array('Rr','Rl'),
            ),          
            'L2'=>array(
                array('LR','LL'),
                array('FL','BR'),
                
                array('Ul','Dl'),
                array('Ud','Dd'),
                
                array('Fl','Br'),
                array('Fd','Bu'),              
                
                array('Lu','Ld'),
                array('Lr','Ll'),
            ),
            'F2'=>array(
                array('FR','FL'),
                array('RL','LR'),
                
                array('Ur','Dl'),
                array('Ud','Du'),
                
                array('Lu','Rd'),
                array('Lr','Rl'), 
                
                array('Fu','Fd'),
                array('Fr','Fl'),
            ),
            
            'U'=>array(
                array('Uu','Ur','Ud','Ul'),
                array('Fu','Lu','Bu','Ru'),
                array('Fl','Ll','Bl','Rl'),
            ),
            
            'D'=>array(
                array('Du','Dr','Dd','Dl'),
                array('Fd','Rd','Bd','Ld'),
                array('Fr','Rr','Br','Lr'),
            )
      
      );
              
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);

foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>""){
        if(in_array($move,array("R2","F2","L2"))){
            $CoorColor=Rotate($CoorColor,$circles,$move,true);  
        }elseif(in_array($move[0],array("U","D"))){
            if(isset($move[1])){
                if($move[1]==" "){
                   $CoorColor=Rotate($CoorColor,$circles,$move[0],true);
                }elseif($move[1]=='2'){
                    $CoorColor=Rotate($CoorColor,$circles,$move[0],true);      
                    $CoorColor=Rotate($CoorColor,$circles,$move[0],true);      
                }elseif($move[1]=='\''){
                    $CoorColor=Rotate($CoorColor,$circles,$move[0],false);      
                }
            }else{
                $CoorColor=Rotate($CoorColor,$circles,$move[0],true);      
            }
        }
    }
    
}


$im= imagecreate($Border*2+$Ceil*2*4+$D*3, $Border*2+$Ceil*7+$D*2);
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


foreach($Center22 as $n=>$center){
  foreach ($Coor22 as $c=>$coor){
        $Rects[]=array($center['x']+$coor['x'],$center['y']+$coor['y'],$center['x']+$coor['x']+1,$center['y']+$coor['y']+1,$Colors[$CoorColor[$n][$c]]);
  }
}

foreach($Center23 as $n=>$center){
  foreach ($Coor23 as $c=>$coor){
        $Rects[]=array($center['x']+$coor['x'],$center['y']+$coor['y'],$center['x']+$coor['x']+1,$center['y']+$coor['y']+1,$Colors[$CoorColor[$n][$c]]);
  }
}


foreach($Rects as $Rect){
    imagesetthickness($im,2);
    $dx=0;
    if($Rect[0]>=2)$dx+=$D;
    if($Rect[0]>=4)$dx+=$D;
    if($Rect[0]>=6)$dx+=$D;
    $dy=0;
    if($Rect[1]>=2)$dy+=$D;
    if($Rect[1]>=5)$dy+=$D;
    
    imagefilledrectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $Rect[4]);
    imagerectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $black);
}

imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>