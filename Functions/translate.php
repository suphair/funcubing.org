<?php

function t($eng, $rus) {
    if ($_SESSION['lang'] == 'RU') {
        return $rus;
    } else {
        return $eng;
    }
}
