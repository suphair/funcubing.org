<?php

$step = filter_input(INPUT_POST, 'step', FILTER_VALIDATE_INT);
$code = filter_input(INPUT_POST, 'code');

mosaic\set_layer($code);
