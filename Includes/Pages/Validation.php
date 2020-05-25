<h1>Validation</h1>
<?php 


function replacment($search, $replace, $text, $c){
    if($c > substr_count($text, $search)){
       return false;
   }
   else{
       $arr = explode($search, $text);
       $result = '';
       $k = 1;
        foreach($arr as $value){
            $k == $c ? $result .= $value.$replace : $result .= $value.$search;
            $k++;
        }
        $pos = strripos($result,$search);
        $result = substr_replace($result,'', $pos, $pos + 3);
        return $result;
   };
}

function checkOnlyKir($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            [' ','-','а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ''
            ,$word);
    return $result;
}


function checkOnlyKirNumber($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            [0,1,2,3,4,5,6,7,8,9,' ','-','а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ''
            ,$word);
    return $result;
}


function checkOnlyLatNumber($word){
    $result=str_replace(
            [0,1,2,3,4,5,6,7,8,9,
                ' ','-','a','b','c','d','e','f','g','h', 'i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                'A','B','C','D','E','F','G','H', 'I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
            ''
            ,$word);
    return $result;
}

function checkOnlyLat($word){
    $result=str_replace(
            [' ','-','a','b','c','d','e','f','g','h', 'i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                'A','B','C','D','E','F','G','H', 'I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
            ''
            ,$word);
    return $result;
}




function Translate1997($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            ['а','б','в','г','д','ье','е','ьё','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ['a','b','v','g','d','ye','e','ye','e','zh','z','i', 'y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch',"",'y',"",'e','yu','ya']
            ,$word);
    return $result;
}

function Translate2010($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            ['а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ['a','b','v','g','d','e','e','zh','z','i', 'i','k','l','m','n','o','p','r','s','t','u','f','kh','tc','ch','sh','shch',"''",'y',"'",'e','iu','ia']
            ,$word);
    return $result;
}



function Translate2012($word){
    $word= mb_strtolower($word);
    $result=str_replace(
            ['а','б','в','г','д','е','ё','ж', 'з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'],
            ['a','b','v','g','d','e','e','zh','z','i', 'i','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch',"ie",'y',"",'e','iu','ia']
            ,$word);
    return $result;
}

?>


<?php


$fix=[];

$fix[]=['ks','x'];
$fix[]=['ndr','nder'];
$fix[]=['ii','y'];
$fix[]=['ei','y'];
$fix[]=['iy','y'];
$fix[]=['f','ph'];
$fix[]=['e','yo'];
$fix[]=['ye','e'];
$fix[]=['ok','ock'];
$fix[]=['kh','h'];
$fix[]=['iia','ia'];
$fix[]=['tc','c'];
$fix[]=['ia','ya'];
$fix[]=['ya','a'];
$fix[]=['ye','ie'];
$fix[]=['ey','ay'];
$fix[]=['ii','ij'];
$fix[]=['iy','i'];
$fix[]=['zh','j'];
$fix[]=['g','gh'];
$fix[]=['ai','ay'];
$fix[]=['ya','ea'];
$fix[]=['ya','ja'];
$fix[]=['ya','ia'];
$fix[]=['ts','c'];
$fix[]=['yi','ii'];
$fix[]=['v','w'];
$fix[]=['ey','ej'];
$fix[]=['ko','co'];
$fix[]=['yy','y'];
$fix[]=['yy','iy'];
$fix[]=['yu','iu'];
$fix[]=['ik','ick'];
$fix[]=['ak','ack'];
$fix[]=['tsy','tsi'];
$fix[]=['yh','ih'];
$fix[]=['ts','tc'];
$fix[]=['ts','tz'];
$fix[]=['yu','ju'];
$fix[]=['ay','ai'];
$fix[]=['u','ju'];
$fix[]=['k','kh'];
$fix[]=['o','io'];
$fix[]=['u','iu'];
$fix[]=['iy','ii'];
$fix[]=['ey','ei'];
$fix[]=['z','s'];
$fix[]=['chik','chyk'];
$fix[]=['tsi','tsy'];
$fix[]=['i','ee'];
$fix[]=['ekh','yekh'];
$fix[]=['ers','yers'];
$fix[]=['evd','yevd'];

$fix[]=['viktor','victor'];
$fix[]=['mikhail','michael'];
$fix[]=['mikhail','michail'];
$fix[]=['dmitrii','dmitri'];
$fix[]=['dmitrii','dmitry'];
$fix[]=['evgenii','eugenii'];
$fix[]=['evgeny','eugene'];
$fix[]=['natali','natalie'];
$fix[]=['alisa','alice'];
$fix[]=['gamal','gamel'];
$fix[]=['petr','peter'];
$fix[]=['egor','yegor'];
$fix[]=['efim','yefim'];
$fix[]=['artur','arthur'];
$fix[]=['oskar','oscar'];
$fix[]=['georgy','george'];
$fix[]=['bogdan','bohdan'];
$fix[]=['dominik','dominic'];
 

$years=['1997','2010','2012'];

DataBaseClassWCA::Query("select distinct 
    id,
SUBSTRING_INDEX(name, ' (', 1) EN,
replace(name,SUBSTRING_INDEX(name, '(', 1),'') RU,
`Persons`.`gender`
from Persons 
where countryId='Russia' and name like '%(%'
and id 
not in
('2019VARA03',
    '2018DYBU01',
    '2018BOGD03',
    '2018BORI03',
    '2018CHKH01',
    '2018DULS01',
    '2018FAND01',
    '2018GALY01',
    '2018KADY01',
    '2018ALEX08',
    '2018BERE05',
    '2018GOLO06',
    '2018ISAK04',
    '2018IVAN08',
    '2018KOST08',
    '2018KOVI02',
    '2018KULA01',
    '2018KURB02',
    '2018LUKI05',
    '2018MARI05',
    '2018MOIS02',
    '2018MYRT01',
    '2018NURG01',
    '2018OKLA01',
    '2018PAVL19',
    '2018PETR18',
    '2018PLOH01',
    '2018PODI01',
    '2018POLI03',
    '2018POPO14',
    '2018ROZH01',
    '2018RUDI03',
    '2018SALO01',
    '2018SCHE04',
    '2018SCHE07',
    '2018SCHU05',
   
    '2018SERG05',
    '2018SHIP02',
    '2018SICH03',
    '2018SIRA01',
    '2018SMIS01',
    '2018SUZE01',
    '2018TERE03',
    '2018TYLE04',
    '2018YAQU02',
    '2018YESI02',
    '2018ZOLO06',
    '2019AITB01',
    '2019ALIY01',
    '2019ANDR42',
    '2019ARTE05',
    '2019AVDE01',
    '2019BARU02',
    '2019BATU02',
    '2019BAYE01',
    '2019BERE05',
    '2019BAYM01',
    '2019CHEG01',
    '2019DYBO01',
    '2019EVSE01',
    '2019FILI07',
    '2019FRIE04',
    '2019GELZ01',
    '2019GILY01',
    '2019GORB05',
    '2019GORE01',
    '2019GREB01',
    '2019ILYI01',
    '2019KAMA06',
    '2019KOLO01',
    '2019KORO01',
    '2019KRIV03',
    '2019KRYA01',
    '2019LUBE01',
    '2019MARK07',
    '2019MATV03',
    '2019MAYE01',
    '2019MISH02',
    '2019NEZD01',
    '2019NIKO12',
    '2019NOVO01',
    '2019SESU01',
    '2019SMIR02',
    '2019SOGO01',
    '2019SOKO08',
    '2019SYRO01',
    '2019TRYH01',
    '2019VAKR01',
    '2019VALI02',
    '2019YALL01',
    '2019YOUN03',
    '2019ZAKH03',
    '2019ZHUC05','','')
order by 1 ");
$n=0;
foreach(DataBaseClassWCA::getRows() as $row)
if(!in_array($row['id'],[
    #+++
    '2015KOLE02',
    '2017MEDE02',
    '2018LEBE07',
    '2018REND01',
    '2019ORLO03'
    
    ])){    
    
$row['RU']=str_replace(html_entity_decode("́", ENT_NOQUOTES, 'UTF-8'),'',$row['RU']);    



$Translate['1997']=Translate1997(strtolower(str_replace(['(',')'], '', $row['RU'])));
$Translate['2010']=Translate2010(strtolower(str_replace(['(',')'], '', $row['RU'])));
$Translate['2012']=Translate2012(strtolower(str_replace(['(',')'], '', $row['RU'])));

$year=false;


foreach($years as $y){
    if(strtolower($row['EN'])==$Translate[$y]){
        $year=$y;
    }
}

if(!$year){
    foreach($years as $y){
        $tmp[$y]=$Translate[$y];
        foreach($fix as $x){
            $substr_count=substr_count($tmp[$y],$x[0]);
            for($i=1;$i<=$substr_count;$i++){
                $l=levenshtein($tmp[$y],strtolower($row['EN']));
                $new=replacment($x[0],$x[1],$tmp[$y],$i);
                $l_new=levenshtein($new,strtolower($row['EN']));
                if($l_new<$l){
                    $tmp[$y]=$new;
                    $i--;
                }
                
                #echo "$l_new $new<br>";
            }
        }
        if(strtolower($row['EN'])==$tmp[$y]){
            $year='±'.$y;
        }
    }
}


if($year){ ?>
<!--<p>
  <font color="green"><b><?= $row['id'] ?></b></font>
  <font color="green"><?= $row['EN'] ?> <?= $row['RU'] ?> <b><?= $year ?></b></font>
</p>-->    
<?php }else{ $n++;?>
<p>
  <font color="red"><b><?= $row['id'] ?></b> </font>
<!--[2012: <?= mb_convert_case($Translate['2012'], MB_CASE_TITLE, "UTF-8")?>]
[2010: <?= mb_convert_case($Translate['2010'], MB_CASE_TITLE, "UTF-8")?>] 
[1997: <?= mb_convert_case($Translate['1997'], MB_CASE_TITLE, "UTF-8")?>] 
<br>--> <?= mb_convert_case($tmp['1997'], MB_CASE_TITLE, "UTF-8") ?> - 
<font color="red"><?= $row['EN'] ?></font> <?= $row['RU'] ?>
</p>
<?php }
} ?>
<h1 color=red><?= $n?></h1>
<?php

exit();

?>
<h2>Competition Not Lat</h2>
<?php
DataBaseClassWCA::Query("Select name "
        . " from Competitions "
        . " where countryId in('Russia','Belarus','Ukraine') ");
foreach(DataBaseClassWCA::getRows() as $row){
    if($kir=checkOnlyLatNumber($row['name'])){?>
<p>
  <?= $row['name']; ?> [<?= $kir ?>]
</p>    
<?php } 
}?>





<h2>Not KIR</h2>
<?php
DataBaseClassWCA::Query("Select name,Id, "
        . " SUBSTRING_INDEX(replace(replace(name,SUBSTRING_INDEX(name, '(', 1),''),'(',''),')', 1) RU, "
        . " name from Persons "
        . " where countryId='Russia' and name like '%(%'");
foreach(DataBaseClassWCA::getRows() as $row){
    if($kir=checkOnlyKir($row['RU'])){?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b> <?= $row['RU']; ?> [<?= $kir ?>]
</p>    
<?php } 
}?>




<h2>Not LAT</h2>
<?php
DataBaseClassWCA::Query("Select name, Id, "
        . " trim(SUBSTRING_INDEX(name, '(', 1)) EN, "
        . " name from Persons "
        . " where countryId='Russia'");
foreach(DataBaseClassWCA::getRows() as $row){
    if($lat=checkOnlyLat($row['EN'])){?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b> <?= str_replace($lat,"<font color=red>$lat</font>",$row['EN']); ?> { <font color=red><?= $lat ?></font> = &amp;#<?= IntlChar::ord($lat) ?>; }
</p>    
<?php } 
}?>

<h2>NOT )</h2>

<?php DataBaseClassWCA::Query("select Id,name
from Persons where name like '%(%'
and name not like '%(%)%' and countryId='Russia'");
foreach(DataBaseClassWCA::getRows() as $row){ ?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b>
</p>    
<?php }?>

<h2>CASE TITLE</h2>

<?php DataBaseClassWCA::Query("select Id,name
from Persons where countryId='Russia'");
foreach(DataBaseClassWCA::getRows() as $row){
if($row['name']!==  mb_convert_case($row['name'], MB_CASE_TITLE, "UTF-8")){ ?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b>
</p>    
<?php }
} ?>

<h2>" )"</h2>

<?php DataBaseClassWCA::Query("select Id,name
from Persons where name like '% )%'");
foreach(DataBaseClassWCA::getRows() as $row){ ?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b>
</p>    
<?php } ?>


<h2>Short names</h2>

<?php DataBaseClassWCA::Query("select Id,name from `Persons` where 
(
name like '%Alex %Алексей%'
or name like '%Dima%'
or name like '%Kostya%'
or name like '%Misha%'
or (name like '%Yura %' and name not like '%zy%')
)
and countryId='Russia'");
foreach(DataBaseClassWCA::getRows() as $row){ ?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b>
</p>    
<?php } ?>

<h2>Wrong translite names</h2>

<?php DataBaseClassWCA::Query("select Id,name from `Persons` where 
(
name like '%rck%'
or name like '%xs%'
)
and countryId='Russia'");
foreach(DataBaseClassWCA::getRows() as $row){ ?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b>
</p>    
<?php } ?>

<h2>Other errors</h2>

<?php DataBaseClassWCA::Query("select Id,name from `Persons` where 
(
name like '%Danik%'
or name like '%Andey%'
or name like '%Ormonov%'
)
and countryId='Russia'");
foreach(DataBaseClassWCA::getRows() as $row){ ?>
<p>
  <?= $row['name']; ?> <b><?= $row['Id']; ?></b>
</p>    
<?php } ?>


