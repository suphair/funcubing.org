<?php

namespace api;

function events($competition_id) {
    $events = \db::rows("
            select 
                    ed.code event,
                    er.round,
                    e.rounds,
                    coalesce(e.name, ed.name) name,
                    ed.special is_special,
                    rd.fullName round_name,
                    er.cutoff,
                    er.time_limit,
                    er.cumulative time_limit_cumulative,
                    er.next_round_value,
                    er.next_round_procent is_percent,
                    fd.name format,
                    fd.cutoff_name format_cutoff,
                    resd.name result
                    from `unofficial_events_rounds` er
            join `unofficial_events` e on e.`id`= er.`event`
            join `unofficial_competitions` c on c.`id`= e.`competition`
            join `unofficial_events_dict` ed on ed.`id` = e.`event_dict`
            join `unofficial_formats_dict` fd on fd.`id` = e.`format_dict`
            join `unofficial_results_dict` resd on resd.`id` = coalesce(e.`result_dict`,ed.`result_dict`)
            join `unofficial_rounds_dict` rd on 
                (case when er.`round` = e.rounds then 3 else er.`round` end) = rd.`id`
            where lower('$competition_id') in (lower(c.secret), lower(c.rankedID), '')
            order by ed.order, er.round
            ");
    $events_key = [];
    foreach ($events as $event) {
        $event_key = new \stdClass();
        $event_key->id = "{$event->event}_{$event->round}";
        $event_key->competition_id = $competition_id;
        $event_key->event = $event->event;
        $event_key->name = $event->name;
        $event_key->is_special = $event->is_special == 1;
        $event_key->round = (object) [
                    'this' => $event->round,
                    'total' => $event->rounds,
                    'name' => $event->round_name];
        if ($event->rounds > $event->round) {
            $event_key->advance_to_next_round = (object) [
                        'value' => $event->next_round_value + 0,
                        'is_percent' => $event->is_percent == 1];
        }
        $event_key->cutoff = $event->cutoff ? $event->cutoff : null;
        if ($event->time_limit) {
            $event_key->time_limit = (object) [
                        'value' => $event->time_limit,
                        'is_cumulative' => $event->time_limit_cumulative == 1
            ];
        } else {
            $event_key->time_limit = null;
        }
        $event_key->format = $event->cutoff ? "$event->format_cutoff / $event->format" : $event->format;
        $event_key->result = $event->result;

        $events_key[] = $event_key;
    }

    return $events_key;
}
