<?php

function GenerateScramble($Discipline){
    if($Discipline=='Kilominx'){
        return GenerateKilominx();
    }elseif($Discipline=='Ivy'){
        return GenerateIvy();
    }elseif($Discipline=='2x2x3'){
        return Generate2x2x3();
    }elseif($Discipline=='3x3x2'){
        return Generate3x3x2();
    }elseif($Discipline=='Redi'){
        return GenerateRedi();
    }elseif($Discipline=='Dino'){
        return GenerateDino();
    }elseif($Discipline=='Fifteen'){
        include 'Fifteen.php';
        return $scrs[array_rand($scrs)];
    }elseif($Discipline=='8x8'){
        return GenerateNxN(8,100,10);
    }elseif($Discipline=='9x9'){
        return GenerateNxN(9,100,10);
    }elseif($Discipline=='Pyraminx4x4x4'){
        return GeneratePyraminx4x4x4();
    }else{
        return false;
    }

}


function SolverIvy($Corners,$Centers,$Solve){
    
   
    $tmpCorners=$Corners;
    $tmpCenters=$Centers;

    
    foreach($Solve as $move){
        if($move[0]=='R'){
           if($move[1]==' '){ 
                $tmpCenters=array(
                   'r'=>$tmpCenters['R'],
                   'D'=>$tmpCenters['r'],
                   'R'=>$tmpCenters['D'],
                   'l'=>$tmpCenters['l'],
                   'L'=>$tmpCenters['L'],
                   'U'=>$tmpCenters['U']);
                $tmpCorners['R']++;
           }else{
                $tmpCenters=array(
                   'r'=>$tmpCenters['D'],
                   'D'=>$tmpCenters['R'],
                   'R'=>$tmpCenters['r'],
                   'l'=>$tmpCenters['l'],
                   'L'=>$tmpCenters['L'],
                   'U'=>$tmpCenters['U']);
                $tmpCorners['R']--;
           }
        }
        if($move[0]=='L'){
            if($move[1]==' '){ 
                $tmpCenters=array(
                   'L'=>$tmpCenters['l'],
                   'l'=>$tmpCenters['D'],
                   'D'=>$tmpCenters['L'],
                   'r'=>$tmpCenters['r'],
                   'R'=>$tmpCenters['R'],
                   'U'=>$tmpCenters['U']);
                 $tmpCorners['L']++;
            }else{
                $tmpCenters=array(
                   'L'=>$tmpCenters['D'],
                   'l'=>$tmpCenters['L'],
                   'D'=>$tmpCenters['l'],
                   'r'=>$tmpCenters['r'],
                   'R'=>$tmpCenters['R'],
                   'U'=>$tmpCenters['U']);
                $tmpCorners['L']--;
            }
        }
        if($move[0]=='U'){
           if($move[1]==' '){                            
                $tmpCenters=array(
                   'r'=>$tmpCenters['l'],
                   'l'=>$tmpCenters['U'],
                   'U'=>$tmpCenters['r'],
                   'R'=>$tmpCenters['R'],
                   'L'=>$tmpCenters['L'],
                   'D'=>$tmpCenters['D']);
                 $tmpCorners['U']++;
           }else{
                $tmpCenters=array(
                   'r'=>$tmpCenters['U'],
                   'l'=>$tmpCenters['r'],
                   'U'=>$tmpCenters['l'],
                   'R'=>$tmpCenters['R'],
                   'L'=>$tmpCenters['L'],
                   'D'=>$tmpCenters['D']);
                 $tmpCorners['U']--;
           }
        }
        if($move[0]=='F'){
           if($move[1]==' '){ 
                $tmpCenters=array(
                   'R'=>$tmpCenters['U'],
                   'L'=>$tmpCenters['R'],
                   'U'=>$tmpCenters['L'],
                   'r'=>$tmpCenters['r'],
                   'l'=>$tmpCenters['l'],
                   'D'=>$tmpCenters['D']);                
                $tmpCorners['F']++;
 
            }else{
                $tmpCenters=array(
                   'R'=>$tmpCenters['L'],
                   'L'=>$tmpCenters['U'],
                   'U'=>$tmpCenters['R'],
                   'r'=>$tmpCenters['r'],
                   'l'=>$tmpCenters['l'],
                   'D'=>$tmpCenters['D']);                                
                $tmpCorners['F']--;
 
           }
        }
    
    }

 /*   echo "<br>";    
    echo (implode(" ",$Solve)." /  ");
    print_r($tmpCorners);
    print_r($tmpCenters);
*/
    return CheckSolveIvy($tmpCorners,$tmpCenters);
    
}

function CheckSolveIvy($Corners,$Centers){
    
   foreach($Corners as $n=>$t){
        if($t % 3 !=0) return false;
   }
    
    foreach($Centers as $n=>$t){
        if($t!=" " and $n!=$t)  return false; 
    }
    return true;
    
    
}



function CheckSolve2x2x3($scramble,$solve){
    foreach(['F','L','R','B'] as $side){
        foreach([1,2,3] as $row){
            foreach([1,2] as $column){
                $cube[$side][$row][$column]=$side;
            }
        }
    }
        
    
    $moves=[];
    $scramble=str_replace("&","",$scramble);
    
    foreach(explode(' ',$scramble) as $c){
        if(trim($c)!==''){
            $moves[]=trim($c);
        }
    }
    foreach(explode(' ',$solve) as $c){
        if(trim($c)!==''){
            $moves[]=trim($c);
        }
    }
    
  
    $Center23=array(
        'F'=>array('Color'=>2),
        'L'=>array('Color'=>3),
        'R'=>array('Color'=>4),
        'B'=>array('Color'=>5),
  
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
  
  foreach($Center23 as $n=>$center){
    foreach ($Coor23 as $c=>$coor){
       $CoorColor[$n][$c]=$center['Color'];  
    }
  }
     
  $circles=array(
        'R2'=>array(
            array('RR','RL'),
            array('FR','BL'),

            array('Fu','Bd'),
            array('Fr','Bl'),              

            array('Ru','Rd'),
            array('Rr','Rl'),
        ),          
        'L2'=>array(
            array('LR','LL'),
            array('FL','BR'),

            array('Fl','Br'),
            array('Fd','Bu'),              

            array('Lu','Ld'),
            array('Lr','Ll'),
        ),
        'F2'=>array(
            array('FR','FL'),
            array('RL','LR'),

            array('Lu','Rd'),
            array('Lr','Rl'), 

            array('Fu','Fd'),
            array('Fr','Fl'),
        ),

        'U'=>array(
            array('Fu','Lu','Bu','Ru'),
            array('Fl','Ll','Bl','Rl'),
        ),

        'D'=>array(
            array('Fd','Rd','Bd','Ld'),
            array('Fr','Rr','Br','Lr'),
        )
      
    );
              
    foreach($moves as $move){
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
    foreach($CoorColor as $side=>$cells){
        $cells= array_unique($cells);
        if(sizeof($cells)>1) return false;
    }
    
    return true;
}



function DeleteScramble($ID){
    foreach(DataBaseClass::SelectTableRows('Scramble', "Event=$ID") as $scramble ){
        $filename="Image/Scramble/".$scramble['Scramble_ID'].".png";
        
        if(file_exists($filename)){
            unlink($filename);     
        }
        DataBaseClass::Query("Delete from `Scramble` where `ID`='".$scramble['Scramble_ID']."' ");    
        
    }
    
}