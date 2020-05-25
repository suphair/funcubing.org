<?php   
$Ceil=50;
$Border=10;
$D=10;

$Center=array(
        'F'=>array('x'=>3,'y'=>3,'Color'=>'Green'),
        'D'=>array('x'=>3,'y'=>5,'Color'=>'Yellow'),
        'L'=>array('x'=>1,'y'=>3,'Color'=>'Orange'),
      
        'U'=>array('x'=>3,'y'=>1,'Color'=>'White'),
        'R'=>array('x'=>5,'y'=>3,'Color'=>'Red'),
        'B'=>array('x'=>7,'y'=>3,'Color'=>'Blue'),
  
  );
  
  
  $Coor=array(
        'u'=>array('x'=>0,'y'=>-1),
        'r'=>array('x'=>0,'y'=>0),
        'd'=>array('x'=>-1,'y'=>0),
        'l'=>array('x'=>-1,'y'=>-1),
  );
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  $circles=array(
            'R'=>array(
                array('Ru','Rr','Rd','Rl'),
                array('Fu','Uu','Bd','Du'),
                array('Fr','Ur','Bl','Dr'),
                  
            ),
            'U'=>array(
                array('Uu','Ur','Ud','Ul'),
                array('Bu','Ru','Fu','Lu'),
                array('Bl','Rl','Fl','Ll'),
            ),
            'F'=>array(
                array('Fu','Fr','Fd','Fl'),
                array('Ud','Rl','Du','Lr'),
                array('Ur','Rd','Dl','Lu'),
            )
      );
              
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);
foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and in_array($move[0],array("U","R","F"))){
        $direct=true;

        
        if(isset($move[1])){
            if($move[1]==" "){
               $CoorColor=Rotate($CoorColor,$circles,$move[0],true);
            }elseif($move[1]=="2"){
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
  



$im= imagecreate($Border*2+$Ceil*2*4+$D*3, $Border*2+$Ceil*2*3+$D*2);
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
    imagesetthickness($im,2);
    $dx=floor($Rect[0]/2)*$D;
    $dy=floor($Rect[1]/2)*$D;
    imagefilledrectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $Rect[4]);
    imagerectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $black);
}

imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 

?>