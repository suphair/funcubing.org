<?php 
function Translit($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            ['а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ['a','b','v','g','d','e','e','zh','z','i', 'i','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch',"ie",'y',"",'e','iu','ia']
            ,$word);
    return mb_convert_case($result, MB_CASE_TITLE, "UTF-8");
} 

function Translit97($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            ['а','б','в','г','д','ье','е','ьё','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ['a','b','v','g','d','ye','e','ye','e','zh','z','i', 'y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch',"",'y',"",'e','yu','ya']
            ,$word);
    return mb_convert_case($result, MB_CASE_TITLE, "UTF-8");
}

?>