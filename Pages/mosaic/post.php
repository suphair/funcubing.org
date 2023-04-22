<?php

mosaic\value::init();
if (filter_input(INPUT_GET, 'setting') !== NULL) {
    include 'post.setting.php';
}
if (filter_input(INPUT_GET, 'image') !== NULL) {
    include 'post.image.php';
}
if (filter_input(INPUT_GET, 'reset') !== NULL) {
    include 'post.reset.php';
}
if (filter_input(INPUT_GET, 'custom') !== NULL) {
    include 'post.custom.php';
}
if (filter_input(INPUT_GET, 'choose') !== NULL) {
    include 'post.choose.php';
}
if (filter_input(INPUT_GET, 'display') !== NULL) {
    include 'post.display.php';
}
