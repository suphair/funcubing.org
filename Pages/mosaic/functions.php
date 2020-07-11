<?php

namespace mosaic;

function cron() {
    $time = time();
    $details = ['old' => 0, 'empty' => 0, 'safe' => 0];
    $dirMain = 'Images/mosaic';
    foreach (getFiles($dirMain) as $file) {
        $dir = "$dirMain/$file";
        if (!sizeof(getFiles($dir))) {
            $details['empty'] ++;
            deleteDir($dir);
            continue;
        }
        if (floor(($time - filectime($dir)) / 60 / 60 / 24) > 1) {
            $details['old'] ++;
            deleteDir($dir);
            continue;
        }
        $details['safe'] ++;
    }
    return json_encode($details);
}

function deleteDir($dir) {
    foreach (getFiles($dir) as $file) {
        is_dir("$dir/$file") ?
                        deleteDir("$dir/$file") :
                        unlink("$dir/$file");
    }
    return rmdir($dir);
}

function getFiles($dir) {
    return array_diff(
            scandir($dir)
            , ['.', '..']
    );
}
