<?php 
$Ceil=100;
$Border=10;
$D=10;
  
$Center=array(
        'U'=>array('x'=>1,'y'=>0,'Color'=>'White'),
        'D'=>array('x'=>1,'y'=>2,'Color'=>'Yellow'),  
        'F'=>array('x'=>1,'y'=>1,'Color'=>'Green'),
        'L'=>array('x'=>0,'y'=>1,'Color'=>'Orange'),
        'R'=>array('x'=>2,'y'=>1,'Color'=>'Red'),
        'B'=>array('x'=>3,'y'=>1,'Color'=>'Blue'),
  );
    
  
  $Coor=array(
        'U'=>array(array('x'=>0,'y'=>0),array('x'=>1,'y'=>0),array('x'=>0.5,'y'=>0.5)),
        'R'=>array(array('x'=>1,'y'=>0),array('x'=>1,'y'=>1),array('x'=>0.5,'y'=>0.5)),
        'D'=>array(array('x'=>0,'y'=>1),array('x'=>1,'y'=>1),array('x'=>0.5,'y'=>0.5)),
        'L'=>array(array('x'=>0,'y'=>0),array('x'=>0,'y'=>1),array('x'=>0.5,'y'=>0.5)),
  );
  
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
     
  $circles=array(
            'R'=>array(
                array('FU','UR','RL'),
                array('FR','UD','RU'),
            ),
            'L'=>array(
                array('FU','LR','UL'),
                array('FL','LU','UD'),
            ),
            'x'=>array(
                array('FU','UU','BD','DU'),
                array('FR','UR','BL','DR'),
                array('FL','UL','BR','DL'),
                array('FD','UD','BU','DD'),
                array('LR','LU','LL','LD'),
                array('RL','RU','RR','RD'),
            )
      
      );
              
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);

foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>""){
        if(in_array($move[0],array("R","L","x"))){
            if(isset($move[1])){
                if($move[1]==" "){
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
  

$im= imagecreate($Border*2+$Ceil*4+$D*3, $Border*2+$Ceil*3+$D*2);
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
  foreach ($Coor as $c=>$coor){
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

    $dx=floor($minX)*$D;
    $dy=floor($minY)*$D;
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