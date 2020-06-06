<?php

function RequireDir($dir) {
    foreach (scandir($dir) as $filename) {
        if (strpos($filename, ".php")) {
            require_once "$dir/$filename";
        }
    }
}
