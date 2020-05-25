<?php      
    $d0=10;
    $s=300;
    $D=5;
    $im= imagecreate(620, 320);
    $white=imagecolorallocate($im,255,255,255);  
    $black=imagecolorallocate($im,0,0,0);  

    //$im = imageCreateFromPng("Scramble/Template/Mirror.png");
    

    $Widths=array("M"=>1,"R"=>0.9,"L"=>1.1,"B"=>0.75,"F"=>1.25,"D"=>0.5,"U"=>1.5);
    $Colors=array(
        "D"=>array(255,255,255),
        "B"=>array(180,180,180),
        "R"=>array(160,160,160),
        
        "L"=>array(120,120,120),
        "F"=>array(80,80,80),
        "U"=>array(0,0,0)
        );
    
    $Elements=array(
                "U"=>array(
                    "R"=>array("r"=>"R","t"=>"U","b"=>"C"),
                    "D"=>array("r"=>"F","t"=>"U","b"=>"C"),
                    "L"=>array("r"=>"L","t"=>"U","b"=>"C"),
                    "U"=>array("r"=>"B","t"=>"U","b"=>"C"),
                    "r"=>array("r"=>"F","l"=>"R","t"=>"U","b"=>"R"),
                    "d"=>array("r"=>"L","l"=>"F","t"=>"U","b"=>"R"),
                    "l"=>array("r"=>"B","l"=>"L","t"=>"U","b"=>"R"),
                    "u"=>array("r"=>"R","l"=>"B","t"=>"U","b"=>"R"),
                ),
                "F"=>array(
                    "R"=>array("r"=>"R","t"=>"F","b"=>"C"),
                    "D"=>array("r"=>"D","t"=>"F","b"=>"C"),
                    "L"=>array("r"=>"L","t"=>"F","b"=>"C"),
                    "U"=>array("r"=>"U","t"=>"F","b"=>"C"),
                    "r"=>array("r"=>"D","l"=>"R","t"=>"F","b"=>"R"),
                    "d"=>array("r"=>"L","l"=>"D","t"=>"F","b"=>"R"),
                    "l"=>array("r"=>"U","l"=>"L","t"=>"F","b"=>"R"),
                    "u"=>array("r"=>"R","l"=>"U","t"=>"F","b"=>"R"),
                ),
                "R"=>array(
                    "R"=>array("r"=>"B","t"=>"R","b"=>"C"),
                    "D"=>array("r"=>"D","t"=>"R","b"=>"C"),
                    "L"=>array("r"=>"F","t"=>"R","b"=>"C"),
                    "U"=>array("r"=>"U","t"=>"R","b"=>"C"),
                    "r"=>array("r"=>"D","l"=>"B","t"=>"R","b"=>"R"),
                    "d"=>array("r"=>"F","l"=>"D","t"=>"R","b"=>"R"),
                    "l"=>array("r"=>"U","l"=>"F","t"=>"R","b"=>"R"),
                    "u"=>array("r"=>"B","l"=>"U","t"=>"R","b"=>"R"),
                ),
                "L"=>array(
                    "R"=>array("r"=>"F","t"=>"L","b"=>"C"),
                    "D"=>array("r"=>"D","t"=>"L","b"=>"C"),
                    "L"=>array("r"=>"B","t"=>"L","b"=>"C"),
                    "U"=>array("r"=>"U","t"=>"L","b"=>"C"),
                    "r"=>array("r"=>"D","l"=>"F","t"=>"L","b"=>"R"),
                    "d"=>array("r"=>"B","l"=>"D","t"=>"L","b"=>"R"),
                    "l"=>array("r"=>"U","l"=>"B","t"=>"L","b"=>"R"),
                    "u"=>array("r"=>"F","l"=>"U","t"=>"L","b"=>"R"),
                ),
                "D"=>array(
                    "R"=>array("r"=>"R","t"=>"D","b"=>"C"),
                    "D"=>array("r"=>"B","t"=>"D","b"=>"C"),
                    "L"=>array("r"=>"L","t"=>"D","b"=>"C"),
                    "U"=>array("r"=>"F","t"=>"D","b"=>"C"),
                    "r"=>array("r"=>"B","l"=>"R","t"=>"D","b"=>"R"),
                    "d"=>array("r"=>"L","l"=>"B","t"=>"D","b"=>"R"),
                    "l"=>array("r"=>"F","l"=>"L","t"=>"D","b"=>"R"),
                    "u"=>array("r"=>"R","l"=>"F","t"=>"D","b"=>"R"),
                ),
                "B"=>array(
                    "R"=>array("r"=>"L","t"=>"B","b"=>"C"),
                    "D"=>array("r"=>"D","t"=>"B","b"=>"C"),
                    "L"=>array("r"=>"R","t"=>"B","b"=>"C"),
                    "U"=>array("r"=>"U","t"=>"B","b"=>"C"),
                    "r"=>array("r"=>"D","l"=>"L","t"=>"B","b"=>"R"),
                    "d"=>array("r"=>"R","l"=>"D","t"=>"B","b"=>"R"),
                    "l"=>array("r"=>"U","l"=>"R","t"=>"B","b"=>"R"),
                    "u"=>array("r"=>"L","l"=>"U","t"=>"B","b"=>"R"),
                )
    );
    
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
               $Elements=Rotate($Elements,$circles,$move[0],true);
            }elseif($move[1]=="2"){
                $Elements=Rotate($Elements,$circles,$move[0],true);
                $Elements=Rotate($Elements,$circles,$move[0],true);
            }elseif($move[1]=='\''){
                $Elements=Rotate($Elements,$circles,$move[0],false);      
                
            }
        }else{
            $Elements=Rotate($Elements,$circles,$move[0],true);      
        }
        
    }
}




$Center=array(
        'F'=>array('x'=>$d0+$s*(0+0.5),'y'=>$d0+$s*(0+0.5),'Color'=>$white),
        'R'=>array('x'=>$d0+$s*(1+0.5),'y'=>$d0+$s*(0+0.5),'Color'=>$black),
  
);

$Coor=array(
        'U'=>array('x'=>0,'y'=>-$s/10),
        'D'=>array('x'=>0,'y'=>$s/10),
        'R'=>array('x'=>$s/10,'y'=>0),
        'L'=>array('x'=>-$s/10,'y'=>0),
        'r'=>array('x'=>1,'y'=>1),
        'd'=>array('x'=>-1,'y'=>1),
        'l'=>array('x'=>-1,'y'=>-1),
        'u'=>array('x'=>1,'y'=>-1),
    
    );
   
$RL=array(
    'Fu'=>array('x'=>'r','y'=>'l'),
    'Fr'=>array('y'=>'r','x'=>'l'),
    'Fd'=>array('x'=>'r','y'=>'l'),
    'Fl'=>array('y'=>'r','x'=>'l'),
    
    'Ru'=>array('x'=>'r','y'=>'l'),
    'Rr'=>array('y'=>'r','x'=>'l'),
    'Rd'=>array('x'=>'r','y'=>'l'),
    'Rl'=>array('y'=>'r','x'=>'l'), 
);  

 foreach($Center as $n=>$center){
    $color=imagecolorallocate($im,$Colors[$n][0],$Colors[$n][1],$Colors[$n][2]);
    
    
    imagefilledrectangle($im,
       $center['x']-$s/10, $center['y']-$s/10,
       $center['x']+$s/10, $center['y']+$s/10,
       $color); 
    
    //imagefill($im, $center['x'], $center['y'], $color);
    imagettftext($im, 50, 0, $Center[$n]['x']-25, $Center[$n]['y']+25, $Center[$n]['Color'], 'arial.ttf', $n);     
     
    foreach ($Coor as $c=>$coor){
        if($Elements[$n][$c]['b']=='C'){
            $el=$Elements[$n][$c]['t'];
            $color_el=imagecolorallocate($im,$Colors[$el][0],$Colors[$el][1],$Colors[$el][2]);
            if($c=='R'){
                imagefilledrectangle($im,
                        $center['x']+$coor['x']+$D,
                        $center['y']-$s/10,
                        $center['x']+$coor['x']+$s/5*$Widths[$Elements[$n][$c]['r']]+$D,
                        $center['y']+$s/10,
                        $color_el);
                imagerectangle($im,
                        $center['x']+$coor['x']+$D,
                        $center['y']-$s/10,
                        $center['x']+$coor['x']+$s/5*$Widths[$Elements[$n][$c]['r']]+$D,
                        $center['y']+$s/10,
                        $black);
            }
            if($c=='L'){
                imagefilledrectangle($im,
                        $center['x']+$coor['x']-$D,
                        $center['y']-$s/10,
                        $center['x']+$coor['x']-$s/5*$Widths[$Elements[$n][$c]['r']]-$D,
                        $center['y']+$s/10,
                        $color_el);   
                imagerectangle($im,
                        $center['x']+$coor['x']-$D,
                        $center['y']-$s/10,
                        $center['x']+$coor['x']-$s/5*$Widths[$Elements[$n][$c]['r']]-$D,
                        $center['y']+$s/10,
                        $black);
            }

            if($c=='U'){
                imagefilledrectangle($im,
                        $center['x']-$s/10,
                        $center['y']+$coor['y']-$D,
                        $center['x']+$s/10,
                        $center['y']+$coor['y']-$s/5*$Widths[$Elements[$n][$c]['r']]-$D,
                        $color_el);       
                imagerectangle($im,
                        $center['x']-$s/10,
                        $center['y']+$coor['y']-$D,
                        $center['x']+$s/10,
                        $center['y']+$coor['y']-$s/5*$Widths[$Elements[$n][$c]['r']]-$D,
                        $black);
            }
            if($c=='D'){
                imagefilledrectangle($im,
                        $center['x']-$s/10,
                        $center['y']+$coor['y']+$D,
                        $center['x']+$s/10,
                        $center['y']+$coor['y']+$s/5*$Widths[$Elements[$n][$c]['r']]+$D,
                        $color_el);   
                imagerectangle($im,
                        $center['x']-$s/10,
                        $center['y']+$coor['y']+$D,
                        $center['x']+$s/10,
                        $center['y']+$coor['y']+$s/5*$Widths[$Elements[$n][$c]['r']]+$D,
                        $black);
            }
        }
        
        if($Elements[$n][$c]['b']=='R'){  
                $x1=$center['x']+$s/10*$coor['x'];
                $x2=$center['x']+$s/10*$coor['x']+$coor['x']*$s/5*$Widths[$Elements[$n][$c][$RL[$n.$c]['x']]];
                $y1=$center['y']+$s/10*$coor['y'];
                $y2=$center['y']+$s/10*$coor['y']+$coor['y']*$s/5*$Widths[$Elements[$n][$c][$RL[$n.$c]['y']]];
                
                if($c=='u'){$x1+=$D;$x2+=$D;$y1-=$D;$y2-=$D;}
                if($c=='r'){$x1+=$D;$x2+=$D;$y1+=$D;$y2+=$D;}
                if($c=='d'){$x1-=$D;$x2-=$D;$y1+=$D;$y2+=$D;}
                if($c=='l'){$x1-=$D;$x2-=$D;$y1-=$D;$y2-=$D;}
                
                if($x1>$x2){
                    $xs=$x2;
                    $xe=$x1;
                }else{
                    $xs=$x1;
                    $xe=$x2;
                }
                if($y1>$y2){
                    
                    $ys=$y2;
                    $ye=$y1;
                }else{
                    $ys=$y1;
                    $ye=$y2;
                }
                
                $el=$Elements[$n][$c]['t'];
                $color_el=imagecolorallocate($im,$Colors[$el][0],$Colors[$el][1],$Colors[$el][2]);
     
                imagefilledrectangle($im,
                $xs,
                $ys,
                $xe,
                $ye,
                $color_el); 
                
                imagerectangle($im,
                $xs,
                $ys,
                $xe,
                $ye,
                $black); 
                
        }
    }
    
    
    /*
    foreach ($Coor as $c=>$coor){
        if($Elements[$n][$c]['b']=='C'){
            $el=$Elements[$n][$c]['t'];
            $color=imagecolorallocate($im,$Colors[$el][0],$Colors[$el][1],$Colors[$el][2]);
            //imagefill($im, $center['x']+$coor['x']*1.1, $center['y']+$coor['y']*1.1, $color);
        }
        if($Elements[$n][$c]['b']=='R'){
            $el=$Elements[$n][$c]['t'];
            $color=imagecolorallocate($im,$Colors[$el][0],$Colors[$el][1],$Colors[$el][2]);
            //imagefill($im, $center['x']+$coor['x']*$s/7, $center['y']+$coor['y']*$s/7, $color);
        }
    }
     */
            
  }
  
 
 imagePNG($im,"Image/Scramble/".$Scrumble_ID.".png");
 
 


?>