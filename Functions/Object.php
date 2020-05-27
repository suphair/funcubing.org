<?php
function arrayToObject($arData) {
    if (is_array($arData)) {
        return (object) array_map(__FUNCTION__, $arData);
    } else {
        return $arData;
    }
}

function objectToArray($oStdClass) {
    if (is_object($oStdClass)) {
        $oStdClass = get_object_vars($oStdClass);
    }

    if (is_array($oStdClass)) {
        return array_map(__FUNCTION__, $oStdClass);
    }
    else {
        return $oStdClass;
    }
}

function objectImplodeValue($glue,$list,$key){
    $values=[];
    foreach($list as $element){
        $values[]=$element->$key;
    }
    return implode($glue, $values); 
}