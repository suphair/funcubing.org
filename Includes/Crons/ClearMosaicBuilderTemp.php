<?php
$time=time();
echo '<br>';
foreach( scandir('Images') as $file ){
    if(strpos($file,'.') === false){
        $days=floor(($time-filectime("Images/$file"))/60/60/24);
        if($days>1){
            delDir("Images/$file");
            echo "del $file $days<br>";
        }else{
            echo "safe $file $days<br>";
        }
    }
}

exit();
