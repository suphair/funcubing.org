<?php

function ImageDiscipline($discipline,$size=100,$name=""){
    if(!$name)$name=$discipline;
    $filenameLocal= "./Image/Discipline/".$discipline.".jpg";
    if(file_exists($filenameLocal)){
        return  "<img  align='center' title='$name' width='".$size."px' src='".PageIndex()."Image/Discipline/".$discipline.".jpg?".date("YMd")."'>";
    }else{
        return  "<img  align='center' width='".$size."px' src='".PageIndex()."Image/None.png'>";
    }
}


function ImageCompetition($competition,$size=100,$name=""){
    if(!$name)$name=$competition;
    $filenameLocal= "./Image/Competition/".$competition.".jpg";
    if(file_exists($filenameLocal)){
        return  "<img align='center' title='$name'  width='".$size."px' src='".PageIndex()."Image/Competition/".$competition.".jpg'>";
    }else{
        return "";
    }
}

function ImageLogo($size=100){
    return  "<img width='".$size."px' src='".PageIndex()."Image/FC.png'>";
    
}


function ImageConfig($link,$config=false,$size=100){ 
        $class=$config?"config_enter":"config"; ?>
        <img  width='<?= $size ?>px' class="<?= $class ?>"
        onclick="this.className='<?= $class ?>'; document.location.href = '<?= $link ?>'"     
        onmouseover="this.className='config_select',
                    this.style.cursor='pointer';"
        onmouseout="this.className='<?= $class ?>'"
        src='<?= PageIndex() ?>Image/Config.jpg'>
    <?php
}

function ImageAdd($size=100){
  return  "<img width='".$size."px' src='".PageIndex()."Image/Add.png'>";  
}

function ImageConfigurate($size=100){
  return  "<img width='".$size."px' src='".PageIndex()."Image/Config.jpg'>";  
}


function GetParam($size,$font,$text){
    $return=array();
    $textbox = imagettfbbox($size, 0, $font, $text);
    $return['height']=$textbox[1] - $textbox[7];
    $return['weith']=$textbox[2] - $textbox[0];
    $return['dy']=$textbox[1];
    $return['dx']=$textbox[0];
    return $return;
} 