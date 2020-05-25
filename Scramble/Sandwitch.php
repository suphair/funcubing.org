<?php 
$im = imageCreateFromPng("Scramble/Template/Cube.png");
  
  $Colors=array(
      'Red'=> imagecolorallocate($im,255,0,0),
      'Green'=> imagecolorallocate($im,49,127,67),
      'White'=> imagecolorallocate($im,255,255,255),
      'Blue'=> imagecolorallocate($im,0,0,255),
      'Yellow'=> imagecolorallocate($im,255,255,0),
      'Orange'=> imagecolorallocate($im,255,165,0)
  );
    
  $Center=array(
        'D'=>array('x'=>300,'y'=>500,'Color'=>'Red'),      
        'U'=>array('x'=>300,'y'=>100,'Color'=>'Blue'),
      
        'F'=>array('x'=>300,'y'=>300,'Color'=>'Yellow'),
        'L'=>array('x'=>100,'y'=>300,'Color'=>'Yellow'),
        'R'=>array('x'=>500,'y'=>300,'Color'=>'Yellow'),
        'B'=>array('x'=>700,'y'=>300,'Color'=>'Yellow'),
  );
  
  $Coor=array(
        'C'=>array('x'=>0,'y'=>0),
        'U'=>array('x'=>0,'y'=>-75),
        'D'=>array('x'=>0,'y'=>75),
        'R'=>array('x'=>75,'y'=>0),
        'L'=>array('x'=>-75,'y'=>0),
        'u'=>array('x'=>75,'y'=>-75),
        'r'=>array('x'=>75,'y'=>75),
        'd'=>array('x'=>-75,'y'=>75),
        'l'=>array('x'=>-75,'y'=>-75),
  );
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    if(in_array($n,array('U','D'))){  
        foreach ($Coor as $c=>$coor){
           $CoorColor[$n][$c]=$center['Color'];  
        }
    }else{
        foreach ($Coor as $c=>$coor){
           if(in_array($c,array('U','u','l'))){ 
                $CoorColor[$n][$c]=$Center['U']['Color'];  
           }elseif(in_array($c,array('D','r','d'))){ 
                $CoorColor[$n][$c]=$Center['D']['Color'];
           }else{
                $CoorColor[$n][$c]=$center['Color'];      
           }
        }
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
  
  foreach($Center as $n=>$center){
    foreach ($Coor as $c=>$coor){
        imagefill($im,$center['x']+$coor['x'], $center['y']+$coor['y'], $Colors[$CoorColor[$n][$c]]);       
        //imageellipse($im, $center['x']+$coor['x'], $center['y']+$coor['y'], 100, 100,$Colors['Red']);
    }
  }
  
 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>