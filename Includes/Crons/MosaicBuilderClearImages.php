<?php

$time = time();
echo '<br>';
foreach (scandir('Images/MosaciBuilding') as $file) {
    $dir="Images/MosaciBuilding/$file";
    if (strpos($file, '.') === false) {
        $days = floor(($time - filectime($dir)) / 60 / 60 / 24);
        $count=0;
        foreach(scandir($dir) as $f){
            if (strpos($f, '.') === false) {
               $count++; 
            }
        }
        echo "$count : ";
        if ($days > 1 or $count==0) {
            delDir($dir);
            echo "del $file $days<br>";
        } else {
            echo "safe $file $days<br>";
        }
    }
}
AddLog('MosaicBuilderClearImages', 'CronReload', sizeof($competitions));
exit();
