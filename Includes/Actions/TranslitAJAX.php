<?php 
if(explode("=",explode("?",getRequest()[1])[1])[0]=='name'){
    $type='name';
}else{
    $type='surname';
}
$name=str_replace(" ","",(URLDecode(explode("=",explode("?",getRequest()[1])[1])[1]))); ?>
<?php if($name=='') exit();

$translit=Translit($name);
$translit97=Translit97($name);

?>

<h3><?= mb_convert_case($name, MB_CASE_TITLE, "UTF-8"); ?></h3>

<table style="white-space:nowrap">    
    <tr class="tr_title"><td colspan="3">Транслитерация</td></tr>
    <tr>
        <td class="message"><b><?= $translit ?></b></td>
        <td colspan="2">
            <a target="_blank" href="http://www.consultant.ru/document/cons_doc_LAW_198429/c956ff01bf42465d7052431dec215b77d0404875/">по правилам 2016 года</a>
    </tr>    
    <?php if($translit!=$translit97){ ?>
    <tr>
       <td><b><?= $translit97 ?></b></td>
        <td colspan="2">
            по правилам 1997 года
        </td>    
    </tr>   
    <?php } ?>
    <tr class="no_border"><td colspan="3">&nbsp;</td></tr>
    
    <tr class="tr_title">
       <td colspan="2">
            Участников в России, 
        </td>    
        <td>в Мире</td>
    </tr>  
<?php

    if($type=='name'){
        $sql="select 
count(distinct id) count, t.EN,sum(case when P.countryId='Russia' then 1 else 0 end) RU_count
from( 
select distinct trim(SUBSTRING_INDEX(name,' ',1)) EN from Persons where countryId='Russia' 
and name like '%($name %' 
UNION DISTINCT
select '$translit'
UNION DISTINCT
select '$translit97'
    )t 
left outer join Persons P on name like CONCAT(t.EN,' %')
group by EN
order by 3,1 desc";
    }    
    
if($type=='surname'){
        $sql="select 
count(distinct id) count, t.EN,sum(case when P.countryId='Russia' then 1 else 0 end) RU_count
from( select distinct trim(SUBSTRING_INDEX(SUBSTRING_INDEX(name,' (',1),' ',-1)) EN from Persons where countryId='Russia' 
and name like '% $name)' 
UNION DISTINCT
select '$translit'
UNION DISTINCT
select '$translit97'
)t 
left outer join Persons P on (name like CONCAT('% ',t.EN) or name like CONCAT('% ',t.EN,' (%'))
group by EN
order by 3,1 desc";
    }    
        
        DataBaseClassWCA::Query($sql);
        $rows=DataBaseClassWCA::getRows();?>
                <?php foreach($rows as $row){ ?>
                    <tr>
                        <td><b><?= $row['EN'] ?></b></td>
                        <td>
                            <?php if($row['RU_count'] ){ ?>
                                <a target="_blank" href="https://www.worldcubeassociation.org/persons?page=1&region=Russia&search=%26nbsp%3B<?= urlencode($row['EN']) ?>%26nbsp%3B"><?= $row['RU_count'] ?></a>
                            <?php }else{ ?>
                                0
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($row['count'] ){ ?>
                                <a target="_blank" href="https://www.worldcubeassociation.org/persons?page=1&search=%26nbsp%3B<?= urlencode($row['EN']) ?>%26nbsp%3B"><?= $row['count'] ?></a>
                                <?php }else{ ?>
                                0
                            <?php } ?>
                        </td>
                    </tr>
        <?php } ?>
</table>
<?php exit(); ?>