<?php
function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

function PageIndex(){
    return "//".$_SERVER['HTTP_HOST'].str_replace("index.php","",$_SERVER['PHP_SELF']);
}

function PageLocal(){
    return str_replace("index.php","",$_SERVER['PHP_SELF']);
}

Function HeaderExit(){
    if(isset($_SERVER['HTTP_REFERER']) and str_replace(PageIndex(),"",$_SERVER['HTTP_REFERER'])!=$_SERVER['HTTP_REFERER']){
        header('Location: '.$_SERVER['HTTP_REFERER']); 
    }else{
        echo 'access denied';
    }
    exit();         
}


function getTermination ($num,$str,$o1,$o2,$o5)
{
    $number = substr($num, -2);
    if($number > 10 and $number < 15)
    {
        $term = $o5;
    }
    else
    {                      
        $number = substr($number, -1);
         
        if($number == 0) $term = $o5;
        if($number == 1 ) $term = $o1;
        if($number > 1 ) $term = $o2;
        if($number > 4 ) $term = $o5;
    }
    echo  $num.' '.$str.$term;
}
