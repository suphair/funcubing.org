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
$api_competitions = api\competitions($comp->secret);
$exports['competition'] = array_shift($api_competitions);

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
        $results['place'] = $competitor->place + 0;
        if ($comp->ranked and $competitor->FCID) {
            $results['FCID'] = $competitor->FCID;
        }
        $results['name'] = $competitor->name;
        foreach ($formats as $format) {
            $result_format = $competitor->$format;
            $results['display'][$format] = $result_format;
            $results[$format] = attempt_centiseconds($result_format);
        }
        foreach (range(1, $event->attempts) as $i) {
            if ($competitor->{"attempt$i"} != 'DNS') {
                $attempt = str_replace(['(', ')'], ['', ''], $competitor->{"attempt$i"});
                $results['display']['attempts'][] = $competitor->{"attempt$i"};
                $results['attempts'][] = attempt_centiseconds($attempt);
            }
        }
        $export[] = $results;
    }
    if ($export) {
        $round = $rounds_dict[$event->final ? 0 : $event->round]->fullName;
        $round_event = "$event->name, $round";
        $exports['results'][$round_event]['event'] = [
            'name' => $event->name,
            'code' => $event->code,
            'format' => $event->format,
            'result' => $event->result_code,
            'special' => $event->special == 1
        ];

        $exports['results'][$round_event]['round'] = [
            'this' => $event->round + 0,
            'total' => $rounds + 0,
            'name' => $round
        ];
        $exports['results'][$round_event]['competitors'] = $export;
    }
}
if (filter_input(INPUT_GET, 'format') == 'txt' and sizeof($events) == 1) {
    header("Content-type:  text/plain; charset=utf-8");
    foreach ($export as $row) {
        echo $row['name'];
        foreach ($row['display']['attempts'] as $attempt) {
            $attempt = str_replace(['(', ')'], ['', ''], $attempt);
            echo " $attempt";
        }
        echo "\n";
    }
    exit();
}
header("Content-type:  application/json; charset=utf-8");

echo json_encode($exports,
        JSON_PRETTY_PRINT +
        JSON_UNESCAPED_SLASHES +
        JSON_UNESCAPED_UNICODE);
exit();

function attempt_centiseconds($attempt) {
    if ($attempt == 'DNF' or $attempt == 'dnf') {
        return -1;
    }
    if ($attempt == 'DNS' or $attempt == 'dns') {
        return -2;
    }
    if (!$attempt) {
        return 0;
    }
    $attempt = str_replace([':', '.'], '', $attempt);
    $attempt = substr('000000' . $attempt, -6, 6);

    $minute = substr($attempt, 0, 2);
    $second = substr($attempt, 2, 2);
    $centisecond = substr($attempt, 4, 2);
    return $minute * 60 * 100 + $second * 100 + $centisecond;
}
