<?php

function DeleteFiles($folder) {
    foreach (glob("$folder/*") as $name) {
        if (!is_dir($name)) {
            unlink($name);
        }
    }
}

function delDir($dir) {
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        (is_dir($dir . '/' . $file)) ? delDir($dir . '/' . $file) : unlink($dir . '/' . $file);
    }
    return rmdir($dir);
}
