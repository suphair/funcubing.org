<?php

namespace api;

function projector($id, $event_code, $round) {

    $competition = get_competition($id);
    $comp_data = \unofficial\getCompetitionData($competition->local_id);
    $event_dict = $comp_data->event_dict->by_code[$event_code]->id ?? FALSE;
    $event = $comp_data->rounds[$event_dict][$round]->round->id ?? FALSE;
    $event_round = $comp_data->event_rounds[$event] ?? false;
    if (!$event_round) {
        die("Round not found [$event_code] [$round]");
    }
    $rounds_dict = \unofficial\getRoundsDict();
    $event = \unofficial\getEventByEventround($event_round->id);
    $round_name = $rounds_dict[$round == $event_round->rounds ? 0 : $round]->fullName;
    $competitors = \unofficial\getCompetitorsByEventround($event_round->id, $event);

    $json = [];
    $json['update_at'] = date('Y-m-d\TH:i:s\Z', strtotime($event_round->update_at));

    $formats = [];
    if ($event->format == 'average') {
        $formats['average'] = t('Average', 'Среднее');
        ;
    }
    if ($event->format == 'mean') {
        $formats['mean'] = t('Mean', 'Среднее');
        ;
    }
    $formats['best'] = t('Best', 'Лучшая');
    ;

    $json['header'] = [
        'event_name' => $event->name,
        'event_code' => $event->code,
        'round_name' => $round_name,
        'round_number' => $round+0,
        'formats' => $formats
    ];
    $json_competitors = [];
    foreach ($competitors as $competitor) {
        $json_competitor = [
            'is_podium' => boolval($competitor->podium),
            'goto_next_round' => boolval($competitor->next_round),
            'place' => $competitor->place + 0,
            'name' => trim($competitor->name_full),
            'fc_id' => $competitor->FCID
        ];

        $attempts = [];
        for ($i = 1; $i <= $event->attempts; $i++) {
            $attempt = $competitor->{"attempt$i"};
            $attempts[] = $attempt;
        }
        $json_competitor['attempts'] = $attempts;

        $json_competitor['formats']=[];
        if ($event->format == 'average') {
            $json_competitor['formats']['average'] = $competitor->average;
        }
        if ($event->format == 'mean') {
            $json_competitor['formats']['mean'] = $competitor->mean;
        }
        $json_competitor['formats']['best'] = $competitor->best;

        $json_competitors[] = $json_competitor;
    }

    $json['competitors'] = $json_competitors;

    return $json;
}
