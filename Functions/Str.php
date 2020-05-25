<?php
function html_spellcount($num, $one, $two = false, $many = false) {
    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }
	if (!$num){
		return $num.' '.$many;
	}
    return $num.' '.html_spellcount_only($num, $one, $two, $many);
}
function html_spellcount_only($num, $one, $two = false, $many = false) {
    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }
	if (strpos($num, '.') !== false){
		return $two;
	}
    if ($num%10 == 1 && $num%100 != 11){
        return $one;
    }
    elseif($num%10 >= 2 && $num%10 <= 4 && ($num%100 < 10 || $num%100 >= 20)){
        return $two;
    }
    else{
        return $many;
    }
    return $one;
}

function Echo_format($str){
    $str=str_replace("\n","<br>",$str);
    
    
    $regex = "(((https?|ftp)\:\/\/)|(www))";//Scheme
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,4})";//Host or IP
    $regex .= "(\:[0-9]{2,5})?";//Port
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";//Path
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?";//GET Query
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?";//Anchor
    $str=str_replace
    (
        array('href="','http://http://','http://https://','http:///'),
        array('href="http://','http://','https://','/'),
        preg_replace('/'.$regex.'/i','<a href="\0" target="_blank" class="lgray">\0</a>',$str)
    );
    
    
    $regex="((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})"; 
    $str=preg_replace('/'.$regex.'/i','<a href="mailto:\0" target="_blank" class="lgray">\0</a>',$str);
    
    return $str;
}

function Short_Name($str){
    
    return trim(explode("(",$str)[0]);
    
}

