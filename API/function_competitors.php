<?php

namespace api;

function competitors($competitor_id = false) {

    $competitors = \db::rows("
        SELECT 
            c.name,
            c.FCID fc_id,
            c.FCID is not null as is_ranked,
            c.competition,
            comp.name competition_name,
            wca.wca_name name_EN,
            comp.id competition_id,
            c.id,
            coalesce(comp.rankedID,comp.secret) competition_id,
            result.best single,
            replace(replace(best,'.',''),':','') + 0 single_order,
            replace(replace(average,'.',''),':','') + 0 average_order,
            result.average,
            ed.code event,
            wca.wcaid,
            wca.nonwca
        FROM unofficial_competitors c 
                LEFT OUTER JOIN unofficial_competitions comp on comp.id=c.competition
                LEFT OUTER JOIN `unofficial_competitors_round` round on round.competitor=c.id
                LEFT OUTER JOIN `unofficial_competitors_result` result on result.competitor_round=round.id
                LEFT OUTER JOIN `unofficial_events_rounds` r on r.id=round.round
                LEFT OUTER JOIN `unofficial_events` event on event.id=r.event
                LEFT OUTER JOIN `unofficial_events_dict` ed on ed.id=event.event_dict
                LEFT OUTER JOIN unofficial_fc_wca wca on wca.FCID = c.FCID
        WHERE lower('$competitor_id') in (lower(coalesce(c.FCID,'XXXX')), lower(c.ID), '')
            and coalesce(ed.special, false) = false 
        ORDER BY c.name desc, comp.date desc, ed.order");

    $competitors_key = [];
    foreach ($competitors as $competitor) {
        $competitors_key[$competitor->name] ??= (object)
                [
                    'name' => $competitor->name,
                    'wca_id' => null,
                    'fc_id' => null,
        ];
        if ($competitor->is_ranked) {
            if (!$competitor->name_EN) {
                $competitor->name_EN = transliterate($competitor->name);
            }
            $competitors_key[$competitor->name]->fc_id = $competitor->fc_id;
            $competitors_key[$competitor->name]->name = "$competitor->name_EN ($competitor->name)";
            if ($competitor->nonwca) {
                $competitors_key[$competitor->name]->wca_id = false;
            } elseif (!$competitor->wcaid) {
                $competitors_key[$competitor->name]->wca_id = null;
            } else {
                $competitors_key[$competitor->name]->wca_id = $competitor->wcaid;
            }
        }
        if (!isset($competitors_key[$competitor->name]->competitions[$competitor->competition_id])) {
            $competition = (object) [
                        'competitor_id' => $competitor->id + 0,
                        'competition_id' => $competitor->competition_id,
                        'competition_name' => $competitor->competition_name,
                        'is_ranked' => $competitor->is_ranked == 1,
            ];
            $competitors_key[$competitor->name]->competitions[$competitor->competition_id] = $competition;
        }

        if ($competitor->single_order) {
            if (!isset($competitors_key[$competitor->name]->competitions[$competitor->competition_id]->personal_records[$competitor->event])) {
                $competitors_key[$competitor->name]->competitions[$competitor->competition_id]->personal_records[$competitor->event] = new \stdClass();
            }
            $prev_single_order = $single_orders[$competitor->name][$competitor->competition_id][$competitor->event] ?? false;
            if (!$prev_single_order or $prev_single_order > $competitor->single_order) {
                $competitors_key[$competitor->name]->competitions[$competitor->competition_id]->personal_records[$competitor->event]->single = attempt_centiseconds($competitor->single);
                $single_orders[$competitor->name][$competitor->competition_id][$competitor->event] = $competitor->single_order;
            }

            if (!isset($competitors_key[$competitor->name]->personal_records[$competitor->event])) {
                $competitors_key[$competitor->name]->personal_records[$competitor->event] = new \stdClass();
            }
            $prev_single_order_total = $single_orders_total[$competitor->name][$competitor->event] ?? false;
            if (!$prev_single_order_total or $prev_single_order_total > $competitor->single_order) {
                $competitors_key[$competitor->name]->personal_records[$competitor->event]->single = attempt_centiseconds($competitor->single);
                $single_orders_total[$competitor->name][$competitor->event] = $competitor->single_order;
            }
        }

        if ($competitor->average_order) {
            if (!isset($competitors_key[$competitor->name]->competitions[$competitor->competition_id]->personal_records[$competitor->event])) {
                $competitors_key[$competitor->name]->competitions[$competitor->competition_id]->personal_records[$competitor->event] = new \stdClass();
            }
            $prev_average_order = $average_orders[$competitor->name][$competitor->competition_id][$competitor->event] ?? false;
            if (!$prev_average_order or $prev_average_order > $competitor->average_order) {
                $competitors_key[$competitor->name]->competitions[$competitor->competition_id]->personal_records[$competitor->event]->average = attempt_centiseconds($competitor->average);
                $average_orders[$competitor->name][$competitor->competition_id][$competitor->event] = $competitor->average_order;
            }

            if (!isset($competitors_key[$competitor->name]->personal_records[$competitor->event])) {
                $competitors_key[$competitor->name]->personal_records[$competitor->event] = new \stdClass();
            }
            $prev_average_order_total = $average_orders_total[$competitor->name][$competitor->event] ?? false;
            if (!$prev_average_order_total or $prev_average_order_total > $competitor->average_order) {
                $competitors_key[$competitor->name]->personal_records[$competitor->event]->average = attempt_centiseconds($competitor->average);
                $average_orders_total[$competitor->name][$competitor->event] = $competitor->average_order;
            }
        }
    }
    return $competitors_key;
}
