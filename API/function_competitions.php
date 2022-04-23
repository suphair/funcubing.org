<?php

namespace api;

function competitions($competition_id = false) {
    $wca_id = get_me()['wca_id'] ?? false;
    $RU = t('', 'RU');
    $competitions = \db::rows("SELECT 
                    c.id,
                    c.name,
                    coalesce(c.rankedID,c.secret) url,
                    c.website,
                    c.date start_date,
                    coalesce(c.rankedID,c.secret) secret,
                    coalesce(c.date_to,c.date) end_date,
                    c.ranked is_ranked,
                    c.show is_publish,
                    dc.wcaid dc_wcaid,
                    coalesce(dc.name$RU, dc.name) dc_name,
                    dcSJ.wcaid dcSJ_wcaid,
                    coalesce(dcSJ.name$RU, dcSJ.name) dcSJ_name,
                    dcJJ.wcaid dcJJ_wcaid,
                    coalesce(dcJJ.name$RU, dcJJ.name) dcJJ_name
                FROM unofficial_competitions c 
                JOIN dict_competitors dc on dc.wid=c.competitor 
                LEFT OUTER JOIN dict_competitors dcSJ on dcSJ.wcaid=c.rankedJudgeSenior 
                LEFT OUTER JOIN dict_competitors dcJJ on dcJJ.wcaid=c.rankedJudgeJunior 
                WHERE lower('$competition_id') in (lower(c.secret), lower(c.rankedID), '')
                ORDER BY c.date desc");
    $competitions_key = [];
    foreach ($competitions as $competition) {
        $competition_key = new \stdClass();
        $competition_key->id = $competition->secret;
        $competition_key->name = $competition->name;
        $competition_key->url = 'https:' . PageIndex() . 'competitions/' . $competition->url;
        $competition_key->website = $competition->website;
        $competition_key->start_date = $competition->start_date;
        $competition_key->end_date = $competition->end_date;
        $competition_key->is_ranked = $competition->is_ranked > 0;
        $competition_key->is_publish = $competition->is_publish > 0;
        $competition_key->organizers = [(object) [
                'wca_id' => $competition->dc_wcaid,
                'name' => $competition->dc_name,
                'main' => true
        ]];
        if ($competition->dcSJ_wcaid or$competition->dcJJ_wcaid) {
            $competition_key->judges ?? [];
        }
        if ($competition->dcSJ_wcaid) {
            $competition_key->judges[] = (object) [
                        'wca_id' => $competition->dcSJ_wcaid,
                        'name' => $competition->dcSJ_name,
                        'main' => true
            ];
        }
        if ($competition->dcJJ_wcaid) {
            $competition_key->judges[] = (object) [
                        'wca_id' => $competition->dcJJ_wcaid,
                        'name' => $competition->dcJJ_name,
                        'main' => false
            ];
        }

        $competitions_key[$competition->id] = $competition_key;
    }
    $organizers = \db::rows("SELECT 
                            o.competition,
                            dc.wcaid,
                            coalesce(dc.name$RU, dc.name) name
                        FROM unofficial_organizers o
                        JOIN dict_competitors dc on dc.wcaid=o.wcaid ");
    foreach ($organizers as $organizer) {
        if ($competitions_key[$organizer->competition] ?? false) {
            $main_organizer = $competitions_key[$organizer->competition]->organizers[0]->wca_id;
            if ($main_organizer != $organizer->wcaid) {
                $competitions_key[$organizer->competition]->organizers[] = (object) [
                            'wca_id' => $organizer->wcaid,
                            'name' => $organizer->name,
                            'main' => false
                ];
            }
        }
    }

    foreach ($competitions_key as $c => $competition_key) {
        if (!$competition_key->is_publish) {
            $delete = true;
            if ($wca_id) {
                foreach ($competition_key->organizers as $organizer) {
                    if ($organizer->wca_id == $wca_id) {
                        $delete = false;
                    }
                }
            }
            if ($delete) {
                unset($competitions_key[$c]);
            }
        }
    }

    return $competitions_key;
}
