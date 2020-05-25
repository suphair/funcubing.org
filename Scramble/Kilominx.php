<?php 
$im = imageCreateFromPng("Scramble/Template/Kilominx.png");
  
  $Colors=array(
      'Red'=> imagecolorallocate($im,255,0,0),
      'Green'=> imagecolorallocate($im,49,127,67),
      'White'=> imagecolorallocate($im,255,255,255),
      'Blue'=> imagecolorallocate($im,0,0,255),
      'Yellow'=> imagecolorallocate($im,255,255,0),
      'Violet'=> imagecolorallocate($im,139,0,255),
      
      'Grey'=> imagecolorallocate($im,128,128,128),
      'Pink'=> imagecolorallocate($im,255,192,203),
      'LightGreen'=> imagecolorallocate($im,144,238,144),
      'Orange'=> imagecolorallocate($im,255,165,0),
      'LightBlue'=> imagecolorallocate($im,66,170,255),
      'LightYellow'=> imagecolorallocate($im,230,215,176),
    
  );
  
  $Center=array(
        1=>array('x'=>406,'y'=>654,'P'=>'D','Color'=>'Green'),
        2=>array('x'=>174,'y'=>484,'P'=>'D','Color'=>'Violet'),
        3=>array('x'=>263,'y'=>212,'P'=>'D','Color'=>'Yellow'),
        4=>array('x'=>548,'y'=>213,'P'=>'D','Color'=>'Blue'),
        5=>array('x'=>641,'y'=>481,'P'=>'D','Color'=>'Red'),
        6=>array('x'=>406,'y'=>413,'P'=>'U','Color'=>'White'),
      
        7=>array('x'=>872,'y'=>407,'P'=>'U','Color'=>'Pink'),
        8=>array('x'=>1101,'y'=>250,'P'=>'U','Color'=>'LightGreen'),
        9=>array('x'=>1335,'y'=>412,'P'=>'U','Color'=>'Orange'),
        'A'=>array('x'=>1245,'y'=>680,'P'=>'U','Color'=>'LightBlue'),
        'B'=>array('x'=>960,'y'=>681,'P'=>'U','Color'=>'LightYellow'),
        'C'=>array('x'=>1100,'y'=>489,'P'=>'D','Color'=>'Grey'),
  );
  
  
  $Coor=array(
      'D'=>array(
            1=>array('x'=>0,'y'=>50),
            2=>array('x'=>-50,'y'=>0),
            3=>array('x'=>-50,'y'=>-50),
            4=>array('x'=>50,'y'=>-50),
            5=>array('x'=>50,'y'=>0)
        ),
        'U'=>array(
            1=>array('x'=>0,'y'=>-50),
            2=>array('x'=>50,'y'=>0),
            3=>array('x'=>50,'y'=>50),
            4=>array('x'=>-50,'y'=>50),
            5=>array('x'=>-50,'y'=>0)
        )
  );
  
  $CoorColor=array();
  foreach($Center as $n=>$center){
    foreach ($Coor[$center['P']] as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
  
  $circles=array(
          'U'=>array(
                array('61','62','63','64','65'),
                array('42','53','14','25','31'),
                array('35','41','52','13','24'),
          ),
          
          'R'=>array(
                array('71','73','75','72','74'),
                array('54','84','B5','44','C2'),
                array('55','85','B1','45','C3'),
                
                array('94','14','33','A4','61'),
                array('95','15','34','A5','62'),
                array('91','11','35','A1','63'),
                
                array('52','82','B3','42','C5'),
                array('53','83','B4','43','C1'),
                array('51','81','B2','41','C4'),
          ),
          
          'D'=>array(
                array('C1','C3','C5','C2','C4'),
                array('B2','84','A1','73','95'),
                array('B1','83','A5','72','94'),
              
                array('81','A3','75','92','B4'),
                array('82','A4','71','93','B5'),
                array('85','A2','74','91','B3'),
              
                array('44','22','55','33','11'),
                array('43','21','54','32','15'),
                array('45','23','51','34','12'),
          )
      );
              
  
foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and in_array($move[0],array("U","R","D"))){
        $direct=true;
        if(isset($move[1])){
            $direct=false; 
            if($move[1]=="+"){
               $direct=true; 
            }
        }
        $CoorColor=Rotate($CoorColor,$circles,$move[0],$direct);              
    }
}
   
  foreach($Center as $n=>$center){
    foreach ($Coor[$center['P']] as $c=>$coor){
        imagefill($im,($center['x']+$coor['x']), ($center['y']+$coor['y']), $Colors[$CoorColor[$n][$c]]);       
        //imageellipse($im, $center['x']+$coor['x'], $center['y']+$coor['y'], 100, 100,$Colors['Red']);
    }
  }
  
 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
?>