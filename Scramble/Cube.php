<?php 
$Ceil=50;
$Border=10;
$D=10;

$Center=array(
        'F'=>array('x'=>3,'y'=>3,'Color'=>'Green'),
        'D'=>array('x'=>3,'y'=>6,'Color'=>'Yellow'),
        'L'=>array('x'=>0,'y'=>3,'Color'=>'Orange'),
      
        'U'=>array('x'=>3,'y'=>0,'Color'=>'White'),
        'R'=>array('x'=>6,'y'=>3,'Color'=>'Red'),
        'B'=>array('x'=>9,'y'=>3,'Color'=>'Blue'),
  
  );
  
  
  $Coor=array(
        'C'=>array('x'=>1,'y'=>1),
        'U'=>array('x'=>1,'y'=>0),
        'D'=>array('x'=>1,'y'=>2),
        'R'=>array('x'=>2,'y'=>1),
        'L'=>array('x'=>0,'y'=>1),
        'l'=>array('x'=>0,'y'=>0),
        'u'=>array('x'=>2,'y'=>0),
        'r'=>array('x'=>2,'y'=>2),
        'd'=>array('x'=>0,'y'=>2),
  );
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  $circles=array(
            'R'=>array(
                array('RU','RR','RD','RL'),
                array('Ru','Rr','Rd','Rl'),
                array('FR','UR','BL','DR'),
                array('Fu','Uu','Bd','Du'),
                array('Fr','Ur','Bl','Dr'),
                  
            ),
            'L'=>array(
                array('LU','LR','LD','LL'),
                array('Lu','Lr','Ld','Ll'),
                array('FL','DL','BR','UL'),
                array('Fl','Dl','Br','Ul'),
                array('Fd','Dd','Bu','Ud'),
            ),
            'U'=>array(
                array('UU','UR','UD','UL'),
                array('Uu','Ur','Ud','Ul'),
                array('BU','RU','FU','LU'),
                array('Bu','Ru','Fu','Lu'),
                array('Bl','Rl','Fl','Ll'),
            ),
            'D'=>array(
                array('DU','DR','DD','DL'),
                array('Du','Dr','Dd','Dl'),
                array('BD','LD','FD','RD'),
                array('Bd','Ld','Fd','Rd'),
                array('Br','Lr','Fr','Rr'),
            ),
            'F'=>array(
                array('FU','FR','FD','FL'),
                array('Fu','Fr','Fd','Fl'),
                array('UD','RL','DU','LR'),
                array('Ud','Rl','Du','Lr'),
                array('Ur','Rd','Dl','Lu'),
            ),
            'B'=>array(
                array('BU','BR','BD','BL'),
                array('Bu','Br','Bd','Bl'),
                array('LL','DD','RR','UU'),
                array('Ll','Dd','Rr','Uu'),
                array('Ld','Dr','Ru','Ul'),
            ),
      );
              
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);
foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and in_array($move[0],array("U","R","B","L","D","F"))){
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

$im= imagecreate($Border*2+$Ceil*3*4+$D*3, $Border*2+$Ceil*3*3+$D*2);
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
    $dx=floor($Rect[0]/3)*$D;
    $dy=floor($Rect[1]/3)*$D;
    imagefilledrectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $Rect[4]);
    imagerectangle($im, $dx+$Border+$Ceil*$Rect[0],$dy+$Border+$Ceil*$Rect[1],$dx+$Border+$Ceil*$Rect[2],$dy+$Border+$Ceil*$Rect[3], $black);
}
  
 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>