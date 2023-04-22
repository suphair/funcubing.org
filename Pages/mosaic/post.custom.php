<?php

$layer = filter_input(INPUT_POST, 'layer', FILTER_VALIDATE_INT);
$color = filter_input(INPUT_POST, 'color');

mosaic\set_custom_color($layer, $color);
mosaic\update_custom_layers();
