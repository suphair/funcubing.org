<?php

mosaic\value::init();
$action = filter_input(INPUT_GET, 'action');

if ($action == 'pdf') {
    include 'action.pdf.php';
}
