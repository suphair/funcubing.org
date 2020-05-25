<?php
$checks=[
    ['link'=>"https://www.worldcubeassociation.org/delegates",
     'reg'=> '|Russia[\s\S]+"name">(.*)<\/div>|Um',
     'type'=>'delegates'],
    ['link'=>"https://www.worldcubeassociation.org/regulations/history/",
     'reg'=> '|>([0-9]{4}-[0-9]{2}-[0-9]{2})<|Um',
     'type'=>'regulations'],
    ['link'=>"https://www.worldcubeassociation.org/results/records?region=Russia&show=mixed+history&years=only+2019",
     'reg'=> '~class=" cubing-icon.*span>\s+(\S*)\s+<[\S\s]*record.*>(.*)<[\S\s]*(single|average).*>(.*)<[\S\s]*persons.*>(.*)<[\S\s]*competitions.*>(.*)<~Um',
     'type'=>'results'],
    ['link'=>"https://www.worldcubeassociation.org/competitions?utf8=%E2%9C%93&region=Russia&search=&state=present&year=all+years&from_date=&to_date=&delegate=&display=list",
     'reg'=> '~list-group-item[\s\S]*class="date"[\s\S]*<\/i>\s*(\S[\s\S]*\S)\s*<[\s\S]*competition-info[\s\S]*competitions\/(.*)">(.*)<[\s\S]*<\/div>[\s\S]*,(.*)\s*<~Um',
     'type'=>'competitions'],
];



$res=[];
foreach($checks as $check){
    $str= file_get_contents($check['link']);
    preg_match_all($check['reg'],$str,$out);
    unset($out[0]);

    foreach($out as $a){
        foreach($a as $n=>$b){
            $res[$check['type']][$n][]=$b;
        }
    }

    foreach($res[$check['type']] as $n=>$a){
        $res[$check['type']][$n]= implode(";",$a);
    }
    
}
DataBaseClass::Query("select coalesce(max(run_id),0)+1 count from WCACheker");

$run_id=DataBaseClass::getRow()['count'];

foreach($res as $type=>$datas){
    foreach($datas as $data){    
        DataBaseClass::Query("Insert into WCACheker (type,object,run_id) values ('$type','$data',$run_id)");
    }
}

DataBaseClass::Query("
select current.type,current.object,'new' action from
(select * from WCACheker where run_id=".($run_id).") current 
    left outer join 
(select * from WCACheker where run_id=".($run_id-1).")prev
on current.object=prev.object and current.type=prev.type 
where prev.ID  is null
union
select prev.type,prev.object,'delete' action  from
( select * from WCACheker where run_id=".($run_id).") current 
    right outer join 
(select * from WCACheker where run_id=".($run_id-1).")prev
on current.object=prev.object and current.type=prev.type 
where current.ID is null
");

$actions=[];
foreach(DataBaseClass::getRows() as $row){
    $actions[]="{$row['type']} - {$row['action']} / {$row['object']}";
}

if(sizeof($actions)){
    SendMail('suphair@gmail.com','ChekerWCA', implode("<br>",$actions));
}

DataBaseClass::Query("delete from WCACheker where run_id<".($run_id-1));

exit();