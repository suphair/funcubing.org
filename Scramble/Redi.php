<?php 
$Ceil=50;
$Border=10;
$D=20;
  
$Center=array(
        'F'=>array('x'=>3,'y'=>3,'Color'=>'Green'),
        'D'=>array('x'=>3,'y'=>6,'Color'=>'Yellow'),
        'L'=>array('x'=>0,'y'=>3,'Color'=>'Orange'),
      
        'U'=>array('x'=>3,'y'=>0,'Color'=>'White'),
        'R'=>array('x'=>6,'y'=>3,'Color'=>'Red'),
        'B'=>array('x'=>9,'y'=>3,'Color'=>'Blue'),
  
  );
  
  
$Coors=array(
            'u'=>array(array('x'=>0,'y'=>0),array('x'=>1,'y'=>0),array('x'=>1,'y'=>1),array('x'=>0,'y'=>1)),
            'r'=>array(array('x'=>2,'y'=>0),array('x'=>3,'y'=>0),array('x'=>3,'y'=>1),array('x'=>2,'y'=>1)),
            'd'=>array(array('x'=>2,'y'=>2),array('x'=>3,'y'=>2),array('x'=>3,'y'=>3),array('x'=>2,'y'=>3)),
            'l'=>array(array('x'=>0,'y'=>2),array('x'=>1,'y'=>2),array('x'=>1,'y'=>3),array('x'=>0,'y'=>3)),
            'U'=>array(array('x'=>1.5,'y'=>1.5),array('x'=>1,'y'=>1),array('x'=>1,'y'=>0),array('x'=>2,'y'=>0),array('x'=>2,'y'=>1)),
            'R'=>array(array('x'=>1.5,'y'=>1.5),array('x'=>2,'y'=>1),array('x'=>3,'y'=>1),array('x'=>3,'y'=>2),array('x'=>2,'y'=>2)),
            'D'=>array(array('x'=>1.5,'y'=>1.5),array('x'=>2,'y'=>2),array('x'=>2,'y'=>3),array('x'=>1,'y'=>3),array('x'=>1,'y'=>2)),
            'L'=>array(array('x'=>1.5,'y'=>1.5),array('x'=>1,'y'=>2),array('x'=>0,'y'=>2),array('x'=>0,'y'=>1),array('x'=>1,'y'=>1)),
);        

  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coors as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  $circles=array(
            'R'=>array(
                array('Ud','Ru','Fr'),
                array('UR','RL','FU'),
                array('UD','RU','FR'),      
            ),
            'L'=>array(
                array('Ul','Fu','Lr'),
                array('UL','FU','LR'),
                array('UD','FL','LU'),      
            ),
      
            'x'=>array(
                array('UU','BD','DU','FU'),
                array('UR','BL','DR','FR'),
                array('UL','BR','DL','FL'),
                array('UD','BU','DD','FD'),
                array('Uu','Bd','Du','Fu'),
                array('Ur','Bl','Dr','Fr'),
                array('Ul','Br','Dl','Fl'),
                array('Ud','Bu','Dd','Fd'),
                array('LU','LL','LD','LR'),
                array('Lu','Ll','Ld','Lr'),
                array('RU','RR','RD','RL'),
                array('Ru','Rr','Rd','Rl'),
            ),
      );
              
  
foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and in_array($move[0],array("R","L","x"))){
        $direct=true;
        if(isset($move[1])){
            $direct=false; 
        }
        if($move!='x'){
            $CoorColor=Rotate($CoorColor,$circles,$move[0],$direct);               
        }else{
            $CoorColor=Rotate($CoorColor,$circles,$move[0],$direct);                  
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
  
  
   
$Polygons=array();  
foreach($Center as $n=>$center){
  foreach ($Coors as $c=>$coor){
      $pairs=array();
      foreach($coor as $xy){$pairs[]=array($center['x']+$xy['x'],$center['y']+$xy['y']) ;}
      $Polygons[]=array($pairs,$Colors[$CoorColor[$n][$c]]);
  }
}

foreach($Polygons as $Polygon){
    imagesetthickness($im,2);
    
    $minX=10000;
    $minY=10000;
    foreach($Polygon[0] as $point){
        if($minX>$point[0])$minX=$point[0];
        if($minY>$point[1])$minY=$point[1];
    }

    $dx=floor($minX/3)*$D;
    $dy=floor($minY/3)*$D;
    $Points=array();
    foreach($Polygon[0] as $point){
        $point[0]=$dx+$Border+$Ceil*$point[0];
        $point[1]=$dy+$Border+$Ceil*$point[1];
        $Points[]=$point[0];
        $Points[]=$point[1];
    }
    
    imagefilledpolygon($im,$Points,sizeof($Points)/2,$Polygon[1]);
    imagepolygon($im,$Points,sizeof($Points)/2,$black);
}

 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>