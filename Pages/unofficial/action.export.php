<?php

$event_code = request(3);
$round = request(4);

$events = [];
$event_dict=$comp_data->event_dict->by_code[$event_code]->id ?? FALSE;
$event = $comp_data->rounds[$event_dict][$round]->round->id ?? FALSE;
if ($comp_data->event_rounds[$event]->id ?? FALSE) {
    $events[] = $comp_data->event_rounds[$event];
} elseif ($event_code) {
    die("event [$event_code] with round [$round] not found");
} else {
    $events = $comp_data->event_rounds;
}

$exports=[];
foreach ($events as $event_round) {

    $competitors = unofficial\getCompetitorsByEventround($event_round->id);
    $event = unofficial\getEventByEventround($event_round->id);
    
    foreach ($competitors as $c => $competitor) {
        if (!$competitor->place) {
            unset($competitors[$c]);
        }
    }
    $competitors = array_values($competitors);
    
    $export=[];
    foreach($competitors as $competitor){
        $results=[];
        foreach(range(1,$event->attempts) as $i){
        $results[]=$competitor->{"attempt$i"};
    }
    $export[$competitor->name]=$results;
    } 
    if($export){
    $exports[$event->name][$event->round]=$export;
    }
}
echo json_encode($exports,JSON_PRETTY_PRINT);
exit();
