<?php

function GenerateIvy(){
    do{
        //$t1 = time();
        $solve=GenerateIvyAttempt();
        //$t2 =  time();
        //echo ($t2-$t1). " <br> ";
        
    }while(!$solve);
    return $solve;    
}

function GenerateIvyAttempt(){
    $Corners=array(
        "U"=>rand(-1,1),
        "F"=>rand(-1,1),
        "R"=>rand(-1,1),
        "L"=>rand(-1,1)
    );
    
    $positionCenters=array("U","R","D","L"," "," ");
    $Centers=array("U"=>" ","R"=>" ","r"=>" ","L"=>" ","l"=>" ","D"=>" ");

    
    foreach($Centers as $c=>$tmp){
        $r=array_rand($positionCenters);
        $Centers[$c]=$positionCenters[$r];
        unset($positionCenters[$r]);
    }
    
    if(CheckSolveIvy($Corners,$Centers)){
        return false;
    }
    $max_depth=7;
    $Cut=rand(6,7);
    $min_solve=3;
    for($d=1;$d<=$min_solve;$d++){
        $IvyHelperAlgs= file("IvyHelper/algs$d.txt",true);
        $IvyHelperPrimes= file("IvyHelper/primes$d.txt",true);

        foreach($IvyHelperAlgs as $alg){
            foreach($IvyHelperPrimes as $prime){    
                $solver=array();
                for($i=0;$i<$d;$i++){
                    $solver[]=substr($alg,$i,1).substr($prime,$i,1);
                }
                
                if(SolverIvy($Corners,$Centers,$solver)){
                    //echo "FALSE $d";
                    //exit();
                    return false;
                }  
            }
        }
    }
    
    for($d=$Cut;$d<=$max_depth;$d++){
        $IvyHelperAlgs= file("IvyHelper/algs$d.txt",true);
        $IvyHelperPrimes= file("IvyHelper/primes$d.txt",true);

        foreach($IvyHelperAlgs as $alg){
            foreach($IvyHelperPrimes as $prime){    
                $solver=array();
                for($i=0;$i<$d;$i++){
                    $solver[]=substr($alg,$i,1).substr($prime,$i,1);
                }
                
                if(SolverIvy($Corners,$Centers,$solver)){
                    return(implode(" ",$solver));
                }  
            }
        }
    }
    echo '!!';
    print_r($Corners);
    print_r($Centers);
    
    $moves=array("R","L","U","F");
    $primes=array(" ","'");

    $solvers_move=array();
    $solvers_prime=array();
    $b=true;
    if(CheckSolveIvy($Corners,$Centers)){
        $b=false;
    }

    foreach($moves as $m=>$move){
        $solvers_move[0][]=$m;
    }
    foreach($primes as $p=>$prime){
        $solvers_prime[0][]=$p;
    }


    foreach($solvers_move[0] as $move){
        foreach($solvers_prime[0] as $prime){
            $solver=array($moves[$move]. $primes[$prime]);
            if(SolverIvy($Corners,$Centers,$solver)){
               $b=false;
            }  
        }
    }

    if(!$b){
        return GenerateIvy(); 
    }

    $Cut=rand(6,7);
    for($j=1;$j<8;$j++){
        foreach($solvers_move[$j-1] as $move){
            foreach($moves as $m=>$tmp1){
               if($m!=substr($move,strlen($move)-1)){
                    $solvers_move[$j][]=$move.$m;
                }
            }


            foreach($solvers_prime[$j-1] as $prime){
                foreach($primes as $p=>$tmp2){    
                    $solvers_prime[$j][]=$prime.$p;
                }  
            }

            foreach($solvers_move[$j] as $move){
                foreach($solvers_prime[$j] as $prime){
                    $solver=array();
                    $strPrime=strlen($prime);
                    for($i=0;$i<$strPrime;$i++){  
                        $solver[]=$moves[$move[$i]]. $primes[$prime[$i]];
                    }
                    if(SolverIvy($Corners,$Centers,$solver)){

                        if(sizeof($solver)<3){
                            return GenerateScramble("Ivy"); 
                        }elseif(sizeof($solver)>=$Cut){
                            return(implode(" ",$solver));
                        }
                    }  
                }
            }
        }
    }
}