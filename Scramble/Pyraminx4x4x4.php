<?php 
$Ceil=100;
$Border=10;
$D=40;
  
$Center=array(
        'D'=>array('t'=>-1, 'x'=>8.6,'y'=>8.4,'Color'=>'Yellow'),  
        'F'=>array('t'=>1, 'x'=>4.4,'y'=>0,'Color'=>'Green'),
        'L'=>array('t'=>-1, 'x'=>4,'y'=>4,'Color'=>'Red'),
        'R'=>array('t'=>-1, 'x'=>8.8,'y'=>4,'Color'=>'Blue'),
  );
    
  
  $Coor=[
        '1-0'=>[['x'=>0,'y'=>0],['x'=>1,'y'=>1],['x'=>0,'y'=>1]],
        '2-0'=>[['x'=>0,'y'=>1],['x'=>1,'y'=>2],['x'=>0,'y'=>2]],
        '3-0'=>[['x'=>0,'y'=>2],['x'=>1,'y'=>3],['x'=>0,'y'=>3]],
        '4-0'=>[['x'=>0,'y'=>3],['x'=>1,'y'=>4],['x'=>0,'y'=>4]],
      
        '2-2'=>[['x'=>1,'y'=>1],['x'=>2,'y'=>2],['x'=>1,'y'=>2]],
        '3-2'=>[['x'=>1,'y'=>2],['x'=>2,'y'=>3],['x'=>1,'y'=>3]],
        '4-2'=>[['x'=>1,'y'=>3],['x'=>2,'y'=>4],['x'=>1,'y'=>4]],
      
        '3-4'=>[['x'=>2,'y'=>2],['x'=>3,'y'=>3],['x'=>2,'y'=>3]],
        '4-4'=>[['x'=>2,'y'=>3],['x'=>3,'y'=>4],['x'=>2,'y'=>4]],
      
        '4-6'=>[['x'=>3,'y'=>3],['x'=>4,'y'=>4],['x'=>3,'y'=>4]],
      
        '2-1'=>[['x'=>1,'y'=>2],['x'=>0,'y'=>1],['x'=>1,'y'=>1]],
        '3-1'=>[['x'=>1,'y'=>3],['x'=>0,'y'=>2],['x'=>1,'y'=>2]],
        '4-1'=>[['x'=>1,'y'=>4],['x'=>0,'y'=>3],['x'=>1,'y'=>3]],
      
        '3-3'=>[['x'=>2,'y'=>3],['x'=>1,'y'=>2],['x'=>2,'y'=>2]],
        '4-3'=>[['x'=>2,'y'=>4],['x'=>1,'y'=>3],['x'=>2,'y'=>3]],
      
        '4-5'=>[['x'=>3,'y'=>4],['x'=>2,'y'=>3],['x'=>3,'y'=>3]],
  ];
  
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
     
  $circles=[
            '0U'=>[
                [['F','1-0'],['L','4-6'],['R','4-0']],
            ],
            '1U'=>[
                [['F','2-0'],['L','4-4'],['R','3-0']],
                [['F','2-1'],['L','4-5'],['R','4-1']],
                [['F','2-2'],['L','3-4'],['R','4-2']],
            ],
            '2U'=>[
                [['F','3-0'],['L','4-2'],['R','2-0']],
                [['F','3-1'],['L','4-3'],['R','3-1']],
                [['F','3-2'],['L','3-2'],['R','3-2']],
                [['F','3-3'],['L','3-3'],['R','4-3']],
                [['F','3-4'],['L','2-2'],['R','4-4']],
            ],
      
      
            '0R'=>[
                [['F','4-6'],['R','1-0'],['D','4-6']],
            ],
            '1R'=>[
                [['F','4-4'],['R','2-0'],['D','3-4']],
                [['F','4-5'],['R','2-1'],['D','4-5']],
                [['F','3-4'],['R','2-2'],['D','4-4']],
            ],
            '2R'=>[
                [['F','4-2'],['R','3-0'],['D','2-2']],
                [['F','4-3'],['R','3-1'],['D','3-3']],
                [['F','3-2'],['R','3-2'],['D','3-2']],
                [['F','3-3'],['R','3-3'],['D','4-3']],
                [['F','2-2'],['R','3-4'],['D','4-2']],
            ],
      
            '0L'=>[
                [['F','4-0'],['D','4-0'],['L','1-0']],
            ],
            '1L'=>[
                [['F','3-0'],['D','4-2'],['L','2-0']],
                [['F','4-1'],['D','4-1'],['L','2-1']],
                [['F','4-2'],['D','3-0'],['L','2-2']],
            ],    
            
            '2L'=>[
                [['F','2-0'],['D','4-4'],['L','3-0']],
                [['F','3-1'],['D','4-3'],['L','3-1']],
                [['F','3-2'],['D','3-2'],['L','3-2']],
                [['F','4-3'],['D','3-1'],['L','3-3']],
                [['F','4-4'],['D','2-0'],['L','3-4']],
            ],
      
            '0B'=>[
                [['D','1-0'],['R','4-6'],['L','4-0']],
            ],
            '1B'=>[
                [['D','2-0'],['R','3-4'],['L','4-2']],
                [['D','2-1'],['R','4-5'],['L','4-1']],
                [['D','2-2'],['R','4-4'],['L','3-0']],
            ],
            '2B'=>[
                [['D','3-0'],['R','2-2'],['L','4-4']],
                [['D','3-1'],['R','3-3'],['L','4-3']],
                [['D','3-2'],['R','3-2'],['L','3-2']],
                [['D','3-3'],['R','4-3'],['L','3-1']],
                [['D','3-4'],['R','4-2'],['L','2-0']],
            ],
            
      ];
              
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);

foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and isset($move[1]) and $move[1]=='w'){
        $move=str_replace(["U","R","B","L","D","F"],["2U","2R","2B","2L"],$move);
    }elseif($move<>"" and (!isset($move[1]) or $move[1]!='w')){
        $move=str_replace(["U","R","B","L","D","F"],["1Uw","1Rw","1Bw","1Lw"],$move);
    }
    
    if($move<>"" and in_array($move[0],['u','r','b','l'])){
        $move=str_replace(['u','r','b','l'],["0Uw","0Rw","0Bw","0Lw"],$move);
    }
    
    if($move<>"" and isset($move[1]) and in_array($move[1],array("R","L","U","B"))){
        $direct=true;
        if(!isset($move[3])){
            $move[3]=" ";
        }
        
        if($move[3]==" "){
            for($l=0;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],true);}
        }elseif($move[3]=='\''){
           for($l=0;$l<=$move[0];$l++){$CoorColor=Rotate($CoorColor,$circles,$l.$move[1],false);}
        }
        
        
    }
} 
  

$im= imagecreate($Border*2+$Ceil*8.8, $Border*2+$Ceil*7.2);
$white=imagecolorallocate($im,250,255,255);
$black=imagecolorallocate($im,0,0,0);

  $Colors=array(
      'Red'=> imagecolorallocate($im,255,0,0),
      'Green'=> imagecolorallocate($im,49,127,67),
      'Blue'=> imagecolorallocate($im,0,0,255),
      'Yellow'=> imagecolorallocate($im,255,255,0),
  );
  

$Polygons=array();  
foreach($Center as $n=>$center){
  foreach ($Coor as $c=>$coor){
      $pairs=array();
      foreach($coor as $xy){
          $pairs[]=array($center['x']+$xy['x']-($center['y']+$xy['y'])/2,($center['y']+$xy['y']*$center['t'])*(sqrt(3)/2)) ;
      }
      $Polygons[]=array($pairs,$Colors[$CoorColor[$n][$c]]);
  }
}
foreach($Polygons as $Polygon){
    imagesetthickness($im,2);
    
    $Points=array();
    
    foreach($Polygon[0] as $point){
        $point[0]=$Border+$Ceil*$point[0];
        $point[1]=$Border+$Ceil*$point[1];
        $Points[]=$point[0];
        $Points[]=$point[1];
    }
    

    
    imagefilledpolygon($im,$Points,sizeof($Points)/2,$Polygon[1]);
    imagepolygon($im,$Points,sizeof($Points)/2,$black);
}
    
 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>