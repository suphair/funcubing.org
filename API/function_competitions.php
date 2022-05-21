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
                    c.rankedApproved is_approved,
                    c.show is_publish,
                    c.details,
                    c.logo,
                    dc.wcaid dc_wcaid,
                    coalesce(dc.name$RU, dc.name) dc_name,
                    competitor.wcaid is not null my_roles_competitor
                FROM unofficial_competitions c 
                left outer JOIN dict_competitors dc on dc.wid=c.competitor 
                left outer JOIN ( 
                    select distinct wca.wcaid,cr.competition
                        from `unofficial_competitors` cr 
                        join unofficial_fc_wca wca on wca.FCID = cr.FCID
                        where lower(wca.wcaid) =  lower('$wca_id') and '$wca_id') competitor
                    ON competitor.competition = c.id        
                WHERE lower('$competition_id') in (lower(c.secret), lower(c.rankedID), '')
                ORDER BY c.date desc, c.name");
    $competitions_key = [];
    foreach ($competitions as $competition) {
        $competition_key = new \stdClass();
        $competition_key->id = $competition->secret;
        $competition_key->name = $competition->name;
        $competition_key->url = 'https:' . PageIndex() . 'competitions/' . $competition->url;
        $competition_key->local_id = $competition->id;
        $competition_key->website = $competition->website;
        $competition_key->logo = $competition->logo;
        $competition_key->details = $competition->details ? $competition->details : null;
        $competition_key->start_date = $competition->start_date;
        $competition_key->end_date = $competition->end_date;
        $competition_key->is_publish = $competition->is_publish > 0;
        $competition_key->is_ranked = $competition->is_ranked > 0;
        $competition_key->is_approved = $competition->is_approved > 0;
        $competition_key->judges = null;
        $competition_key->organizers = [(object) [
                'wca_id' => $competition->dc_wcaid,
                'name' => $competition->dc_name,
                'main' => true,
        ]];
        $competition_key->my_roles = null;

        if ($wca_id) {
            $competition_key->my_roles = (object) [
                        'main_organizer' => $competition->dc_wcaid == $wca_id,
                        'organizer' => false,
                        'judge' => false,
                        'competitor' => $competition->my_roles_competitor > 0
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
                if ($wca_id) {
                    $competitions_key[$organizer->competition]->my_roles->organizer = (($competitions_key[$organizer->competition]->my_roles->organizer ?? false)
                            or $organizer->wcaid == $wca_id);
                }
            }
        }
    }

    $judges = \db::rows("SELECT 
                            cj.competition_id competition,
                            cj.judge wcaid,
                            coalesce(dc.name$RU, dc.name) name,
			    coalesce(jrd.role$RU, jrd.role) role
                        FROM unofficial_competition_judges cj
                        JOIN unofficial_judge_roles_dict jrd on jrd.id=cj.dict_judge_role
                        JOIN dict_competitors dc on dc.wcaid=cj.judge");
    foreach ($judges as $judge) {
        if ($competitions_key[$judge->competition] ?? false) {
            $competitions_key[$judge->competition]->judges ??= [];
            $competitions_key[$judge->competition]->judges[] = (object) [
                        'wca_id' => $judge->wcaid,
                        'name' => $judge->name,
                        'role' => $judge->role,
            ];
            if ($wca_id) {
                $competitions_key[$judge->competition]->my_roles->judge = (($competitions_key[$judge->competition]->my_roles->judge ?? false)
                        or $judge->wcaid == $wca_id);
            }
        }
    }

    return $competitions_key;
}
