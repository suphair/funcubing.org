<?php ob_clean();
header('Content-Type: application/json; charset=utf-8');
$registrations=[];
foreach($comp_data->competitors as $registration){
    foreach($registration->events as $event_id=>$tmp){
        $events[]=[
            'code'=>$comp_data->events[$event_id]->event_code,
            'name'=>$comp_data->events[$event_id]->name,
            ];
    }
    $registrations[]= (object)[
        'name'=>$registration->name,
        'events'=>$events
        ];
}

echo json_encode($registrations,
                JSON_PRETTY_PRINT +
                JSON_UNESCAPED_SLASHES +
                JSON_UNESCAPED_UNICODE
        );
exit();
?>