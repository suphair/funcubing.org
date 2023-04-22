<?php

$color = filter_input(INPUT_POST, 'color');
$pixel = filter_input(INPUT_POST, 'pixel');
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);

mosaic\update_session($color, $pixel, $amount);

