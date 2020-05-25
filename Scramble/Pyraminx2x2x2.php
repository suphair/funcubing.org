<?php 
$im_temp = imageCreateFromPng("Scramble/Template/Piraminx2x2x2.png");
$im = ImageCreate (2010, 1020); 

$Colors=array(
      'Red'=> imagecolorallocate($im,255,0,0),
      'Green'=> imagecolorallocate($im,49,127,67),
      'Blue'=> imagecolorallocate($im,0,0,255),
      'Yellow'=> imagecolorallocate($im,255,255,0),
      'Black' => imagecolorallocate($im,0,0,0),
       'White' => imagecolorallocate($im,255,255,255)
  );

  imagecopy($im, $im_temp, 0, 0, 0, 0, 2010, 1020);

$CoorColor['U']['B']='Yellow';
$CoorColor['U']['R']='Yellow';
$CoorColor['U']['L']='Yellow';

$CoorColor['R']['U']='Yellow';
$CoorColor['R']['F']='Green';
$CoorColor['R']['r']='Blue';

$CoorColor['B']['U']='Yellow';
$CoorColor['B']['r']='Blue';
$CoorColor['B']['l']='Red';

$CoorColor['L']['U']='Yellow';
$CoorColor['L']['F']='Green';
$CoorColor['L']['l']='Red';


$CoorColor['r']['D']='Blue';
$CoorColor['r']['R']='Blue';
$CoorColor['r']['B']='Blue';

$CoorColor['l']['D']='Red';
$CoorColor['l']['L']='Red';
$CoorColor['l']['B']='Red';

$CoorColor['F']['D']='Green';
$CoorColor['F']['R']='Green';
$CoorColor['F']['L']='Green';

$CoorColor['D']['F']='Green';
$CoorColor['D']['r']='Blue';
$CoorColor['D']['l']='Red';
  
  $circles=array(
            'R'=>array(
                array('FL','RU','rB','Dl'),
                array('FR','Rr','rD','DF'),
                array('FD','RF','rR','Dr'),     
            ),
            'U'=>array(
                array('RF','UL','Bl','rD'),
                array('RU','UB','Br','rR'),
                array('Rr','UR','BU','rB'),     
            ),
             'B'=>array(
                array('DF','rR','BU','lL'),
                array('Dr','rB','Bl','lD'),
                array('Dl','rD','Br','lB'),     
            ),
      );
              
  
$scramble=str_replace("\\r","",$scramble);
$scramble=str_replace('\\',"",$scramble);
foreach(explode(" ",$scramble) as $move){
    $move=trim($move);
    if($move<>"" and in_array($move[0],array("R","U","B"))){
        if(isset($move[1])){
            if($move[1]=='\''){
                $CoorColor=Rotate($CoorColor,$circles,$move[0],false);          
            }elseif($move[1]=='2'){
                $CoorColor=Rotate($CoorColor,$circles,$move[0],true);          
                $CoorColor=Rotate($CoorColor,$circles,$move[0],true);          
            }else{
                $CoorColor=Rotate($CoorColor,$circles,$move[0],true);      
            }
        }else{
            $CoorColor=Rotate($CoorColor,$circles,$move[0],true);      
        }
    }
}  

$sides=array();
$sides['UB']=array('x'=>580,'y'=>520);
$sides['UR']=array('x'=>600,'y'=>680);
$sides['UL']=array('x'=>560,'y'=>680);

$sides['DF']=array('x'=>1440,'y'=>360);
$sides['Dl']=array('x'=>1460,'y'=>340);
$sides['Dr']=array('x'=>1420,'y'=>340);


$sides['RU']=array('x'=>820,'y'=>840);
$sides['RF']=array('x'=>860,'y'=>860);
$sides['Rr']=array('x'=>900,'y'=>840);


$sides['BU']=array('x'=>580,'y'=>480);
$sides['Bl']=array('x'=>560,'y'=>320);
$sides['Br']=array('x'=>600,'y'=>320);

$sides['LU']=array('x'=>310,'y'=>850);
$sides['LF']=array('x'=>290,'y'=>860);
$sides['Ll']=array('x'=>280,'y'=>850);

$sides['lD']=array('x'=>1600,'y'=>180);
$sides['lL']=array('x'=>1800,'y'=>180);
$sides['lB']=array('x'=>1720,'y'=>160);

$sides['rD']=array('x'=>1160,'y'=>180);
$sides['rR']=array('x'=>1140,'y'=>180);
$sides['rB']=array('x'=>1140,'y'=>160);


$sides['FR']=array('x'=>1440,'y'=>670);
$sides['FL']=array('x'=>1550,'y'=>670);
$sides['FD']=array('x'=>1440,'y'=>660);

if($CoorColor['U']['B']==$CoorColor['U']['R']){
    imagefill($im,580, 680, $Colors[$CoorColor['U']['B']]);       
}else{
    imagefill($im,580, 680, $Colors['Black']);       
}
imagefill($im,$sides['UB']['x'], $sides['UB']['y'], $Colors[$CoorColor['U']['B']]);       
imagefill($im,$sides['UR']['x'], $sides['UR']['y'], $Colors[$CoorColor['U']['R']]);       
imagefill($im,$sides['UL']['x'], $sides['UL']['y'], $Colors[$CoorColor['U']['L']]);       


if($CoorColor['D']['F']==$CoorColor['D']['r']){
    imagefill($im,1440, 350, $Colors[$CoorColor['D']['F']]);       
}else{
    imagefill($im,1440, 350, $Colors['Black']);       
}
imagefill($im,$sides['DF']['x'], $sides['DF']['y'], $Colors[$CoorColor['D']['F']]);       
imagefill($im,$sides['Dr']['x'], $sides['Dr']['y'], $Colors[$CoorColor['D']['r']]);       
imagefill($im,$sides['Dl']['x'], $sides['Dl']['y'], $Colors[$CoorColor['D']['l']]);       



if($CoorColor['R']['U']==$CoorColor['R']['F']){
   imagefill($im,1150, 1010, $Colors['White']);      
   imagefill($im,860, 850, $Colors['Black']);      
   imagefill($im,$sides['RU']['x'], $sides['RU']['y'], $Colors[$CoorColor['R']['U']]);       
}else{
   imagefill($im,1150, 1010, $Colors['Black']);     
   imagefill($im,860, 850, $Colors[$CoorColor['R']['U']]);
   imagefill($im,$sides['RU']['x'], $sides['RU']['y'], $Colors[$CoorColor['R']['U']]);       
   imagefill($im,$sides['RF']['x'], $sides['RF']['y'], $Colors[$CoorColor['R']['U']]);       
   imagefill($im,$sides['Rr']['x'], $sides['Rr']['y'], $Colors[$CoorColor['R']['U']]);       
}


if($CoorColor['B']['U']==$CoorColor['B']['l']){
   imagefill($im,580, 10, $Colors['White']);      
   imagefill($im,580, 350, $Colors['Black']);      
   imagefill($im,$sides['BU']['x'], $sides['BU']['y'], $Colors[$CoorColor['B']['U']]);       
}else{
   imagefill($im,580, 10, $Colors['Black']);     
   imagefill($im,580, 350, $Colors[$CoorColor['B']['U']]);
   imagefill($im,$sides['BU']['x'], $sides['BU']['y'], $Colors[$CoorColor['B']['U']]);       
   imagefill($im,$sides['Bl']['x'], $sides['Bl']['y'], $Colors[$CoorColor['B']['U']]);       
   imagefill($im,$sides['Br']['x'], $sides['Br']['y'], $Colors[$CoorColor['B']['U']]);       
}

if($CoorColor['L']['U']==$CoorColor['L']['l']){
   imagefill($im,2, 1010, $Colors['White']);      
   imagefill($im,290, 850, $Colors['Black']);      
   imagefill($im,$sides['LU']['x'], $sides['LU']['y'], $Colors[$CoorColor['L']['U']]);       
}else{
   imagefill($im,2, 1010, $Colors['Black']);     
   imagefill($im,290, 850, $Colors[$CoorColor['L']['U']]);
   imagefill($im,$sides['LU']['x'], $sides['LU']['y'], $Colors[$CoorColor['L']['U']]);       
   imagefill($im,$sides['Ll']['x'], $sides['Ll']['y'], $Colors[$CoorColor['L']['U']]);       
   imagefill($im,$sides['LF']['x'], $sides['LF']['y'], $Colors[$CoorColor['L']['U']]);       
}
 

if($CoorColor['l']['D']==$CoorColor['l']['L']){
   imagefill($im,2008, 10, $Colors['White']);      
   imagefill($im,1725, 180, $Colors['Black']);      
   imagefill($im,$sides['lD']['x'], $sides['lD']['y'], $Colors[$CoorColor['l']['D']]);       
}else{
   imagefill($im,2008, 10, $Colors['Black']);     
   imagefill($im,1725, 180, $Colors[$CoorColor['l']['D']]);
   imagefill($im,$sides['lD']['x'], $sides['lD']['y'], $Colors[$CoorColor['l']['D']]);       
   imagefill($im,$sides['lL']['x'], $sides['lL']['y'], $Colors[$CoorColor['l']['D']]);       
   imagefill($im,$sides['lB']['x'], $sides['lB']['y'], $Colors[$CoorColor['l']['D']]);       
}


if($CoorColor['r']['D']==$CoorColor['r']['R']){
   imagefill($im,865, 10, $Colors['White']);      
   imagefill($im,1153, 180, $Colors['Black']);      
   imagefill($im,$sides['rD']['x'], $sides['rD']['y'], $Colors[$CoorColor['r']['D']]);       
}else{
   imagefill($im,865, 10, $Colors['Black']);     
   imagefill($im,1153, 180, $Colors[$CoorColor['r']['D']]);
   imagefill($im,$sides['rD']['x'], $sides['rD']['y'], $Colors[$CoorColor['r']['D']]);       
   imagefill($im,$sides['rR']['x'], $sides['rR']['y'], $Colors[$CoorColor['r']['D']]);       
   imagefill($im,$sides['rB']['x'], $sides['rB']['y'], $Colors[$CoorColor['r']['D']]);       
}

if($CoorColor['F']['D']==$CoorColor['F']['R']){
   imagefill($im,1440, 1010, $Colors['White']);      
   imagefill($im,1440, 680, $Colors['Black']);      
   imagefill($im,$sides['FD']['x'], $sides['FD']['y'], $Colors[$CoorColor['F']['D']]);       
}else{
   imagefill($im,1440, 1010, $Colors['Black']);     
   imagefill($im,1440, 680, $Colors[$CoorColor['F']['D']]);
   imagefill($im,$sides['FD']['x'], $sides['FD']['y'], $Colors[$CoorColor['F']['D']]);       
   imagefill($im,$sides['FR']['x'], $sides['FR']['y'], $Colors[$CoorColor['F']['D']]);       
   imagefill($im,$sides['FL']['x'], $sides['FL']['y'], $Colors[$CoorColor['F']['D']]);       
}

imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
 
?>