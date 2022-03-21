<?php

$event_code = request(3);
$round = request(4);

$events = [];
$event_dict = $comp_data->event_dict->by_code[$event_code]->id ?? FALSE;
$event = $comp_data->rounds[$event_dict][$round]->round->id ?? FALSE;
if ($comp_data->event_rounds[$event]->id ?? FALSE) {
    $events[] = $comp_data->event_rounds[$event];
} elseif ($event_code) {
    die("event [$event_code] with round [$round] not found");
} else {
    $events = $comp_data->event_rounds;
}

$exports = [];
$exports[$comp->name]['competition'] = [
    'website' => $comp->website,
    'name' => $comp->name,
    'details' => $comp->details,
    'date_from' => $comp->date,
    'date_to' => $comp->date_to,
    'owner' => [
        'name' => $comp->competitor_name,
        'wcaid' => $comp->competitor_wcaid,
        'country' => $comp->competitor_country
    ]
];
foreach ($events as $event_round) {

    $competitors = unofficial\getCompetitorsByEventround($event_round->id);
    $event = unofficial\getEventByEventround($event_round->id);
    foreach ($competitors as $c => $competitor) {
        if (!$competitor->place) {
            unset($competitors[$c]);
        }
    }
    $competitors = array_values($competitors);
    $rounds = $event_round->rounds;
    $export = [];
    $formats = array_unique(['best', $event->format]);
    foreach ($competitors as $competitor) {
        $results = [];
        $results['place'] = $competitor->place;
        if ($comp->ranked and $competitor->FCID) {
            $results['FCID'] = $competitor->FCID;
        }
        foreach ($formats as $format) {
            $results[$format] = str_replace('-cutoff', '', $competitor->$format);
        }
        foreach (range(1, $event->attempts) as $i) {
            if ($competitor->$format != '-cutoff' or $competitor->{"attempt$i"} != 'dns') {
                $results['attempts'][$i] = $competitor->{"attempt$i"};
            }
        }
        $export[$competitor->name] = $results;
    }
    if ($export) {
        $round = $rounds_dict[$event->final ? 0 : $event->round]->fullName;
        $round_event = "$event->name, $round";
        $exports[$comp->name]['results'][$round_event]['event'] = [
            'name' => $event->name,
            'code' => $event->code,
            'format' => $event->format,
            'result' => $event->result
        ];

        $exports[$comp->name]['results'][$round_event]['round'] = [
            'this' => $event->round,
            'total' => $rounds,
            'name' => $round,
            'comment' => $event->comment
        ];
        $exports[$comp->name]['results'][$round_event]['competitors'] = $export;
    }
}
if (filter_input(INPUT_GET, 'format') == 'txt' and sizeof($events) == 1) {
    header("Content-type:  text/plain; charset=utf-8");
    foreach ($export as $competitor => $row) {
        echo $competitor;
        foreach ($row['attempts'] as $attempt) {
            echo " $attempt";
        }
        echo "\n";
    }
    exit();
}
header("Content-type:  application/json; charset=utf-8");
echo json_encode($exports, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
exit();
