<?php
function svg_green($size,$title){
    ob_start(); ?>
    <svg width="<?= $size ?>" height="<?= $size ?>">
                    <title><?=$title ?></title>
                    <circle cx="<?=$size/2 ?>" cy="<?= $size/2 ?>" r="<?= $size/2 ?>" fill="var(--light_green)"/>
                </svg>
   <?php
   $return=ob_get_contents();
   ob_end_clean();
   return $return;
}

function svg_red($size,$title){
    ob_start(); ?>
    <svg width="<?= $size ?>" height="<?= $size ?>">
                    <title><?=$title ?></title>
                    <rect x="0" y="<?= $size/3 ?>" width="<?= $size ?>" height="<?= $size/3 ?>" fill="var(--light_red)"/>
                </svg>
   <?php
   $return=ob_get_contents();
   ob_end_clean();
   return $return;
}

function svg_blue($size,$title){
    ob_start(); ?>
    <svg width="<?= $size ?>" height="<?= $size ?>">
                    <title><?=$title ?></title>
                    <rect x="<?= $size/3 ?>" y="0" width="<?= $size/3 ?>" height="<?= $size ?>" fill="var(--light_blue)"/>    
                    <rect x="0" y="<?= $size/3 ?>" width="<?= $size ?>" height="<?= $size/3 ?>" fill="var(--light_blue)"/>
                </svg>
   <?php
   $return=ob_get_contents();
   ob_end_clean();
   return $return;
}

