<?php

namespace api;

function registrations($competition_id) {
    $registrations = \db::rows("
            select 
                    ct.`name`,
                    ct.`FCID`,
                    ed.code event,
                    ct.id id,
                    wca.wcaid,
                    wca.nonwca
                    from `unofficial_competitors_round` cr
            join `unofficial_events_rounds` er on er.`id`=cr.`round`
            join `unofficial_events` e on e.`id`= er.`event`
            join `unofficial_competitions` c on c.`id`= e.`competition`
            join `unofficial_events_dict` ed on ed.`id` = e.`event_dict`
            join `unofficial_competitors` ct on ct.id = cr.competitor
            left outer join `unofficial_fc_wca` wca on wca.FCID = ct.FCID AND ct.FCID is not null
            where er.`round`= 1
                and lower('$competition_id') in (lower(c.secret), lower(c.rankedID), '')
            order by ct.name, ed.order
            ");

    $registrations_key = [];
    foreach ($registrations as $registration) {
        if (!isset($registrations_key[$registration->id])) {
            $registration_key = (object) [
                        'id' => $registration->id + 0,
                        'competition_id' => $competition_id
            ];
            if ($registration->FCID) {
                $registration_key->fc_id = $registration->FCID;
            }
            if ($registration->nonwca) {
                $registration_key->wca_id = false;
            } elseif (!$registration->wcaid) {
                $registration_key->wca_id = null;
            } else {
                $registration_key->wca_id = $registration->wcaid;
            }
            $registration_key->name = $registration->name;
            $registration_key->event_ids = [];
            $registrations_key[$registration->id] = $registration_key;
        }
        $registrations_key[$registration->id]->event_ids[] = $registration->event;
    }

    return $registrations_key;
}
