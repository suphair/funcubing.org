<?php

$wide = filter_input(INPUT_POST, 'wide', FILTER_VALIDATE_INT);
$high = filter_input(INPUT_POST, 'high', FILTER_VALIDATE_INT);
$display = filter_input(INPUT_POST, 'display');

mosaic\update_display($wide, $high, $display);
