<?php

namespace api;

function competitions($competition_id = false) {

    $me = get_me();
    $wca_id = $me->wca_id ?? false;
    $wca_id = $wca_id ? $wca_id : ($me->wid ?? false);
    $RU = t('', 'RU');
    $competitions = \db::rows("SELECT 
                    c.id,
                    c.name,
                    c.city,
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
                    competitor.wcaid is not null my_roles_competitor,
                    competitors.count competitors_count,
                    p.code points
                FROM unofficial_competitions c 
                left outer join unofficial_points_dict p on p.id = c.points
                left outer JOIN dict_competitors dc on dc.wid=c.competitor 
                left outer JOIN ( 
                    select distinct wca.wcaid,cr.competition
                        from `unofficial_competitors` cr 
                        join unofficial_fc_wca wca on wca.FCID = cr.FCID
                        where lower(wca.wcaid) =  lower('$wca_id') and '$wca_id') competitor
                    ON competitor.competition = c.id        
                left outer JOIN (
                    SELECT count(*) count, competition FROM unofficial_competitors
                    GROUP BY competition) competitors
                    ON competitors.competition = c.id
                WHERE lower('$competition_id') in (lower(c.secret), lower(c.rankedID), '')
                ORDER BY c.date desc, c.name");

    $competitions_key = [];
    foreach ($competitions as $competition) {

        $competition_key = new \stdClass();
        $competition_key->id = $competition->secret;
        $competition_key->name = $competition->name;
        $competition_key->city = t(transliterate($competition->city), $competition->city);
        $competition_key->url = (\config::isLocalhost() ? 'http:' : 'https:') . PageIndex() . 'competitions/' . $competition->url;
        $competition_key->local_id = $competition->id;
        $competition_key->website = $competition->website;
        $competition_key->logo = $competition->logo;
        $competition_key->details = $competition->details ? $competition->details : null;
        $competition_key->start_date = $competition->start_date;
        $competition_key->end_date = $competition->end_date;
        $competition_key->is_publish = $competition->is_publish > 0;
        $competition_key->is_ranked = $competition->is_ranked > 0;
        $competition_key->is_approved = $competition->is_approved > 0;
        $competition_key->points = $competition->points;
        $competition_key->delegates = null;
        $competition_key->organizers = [(object) [
                'wca_id' => $competition->dc_wcaid,
                'name' => $competition->dc_name,
                'main' => true,
        ]];
        $competition_key->my_roles = null;
        $competition_key->competitors_count = $competition->competitors_count;

        if ($wca_id) {
            $competition_key->my_roles = (object) [
                        'main_organizer' => $competition->dc_wcaid == $wca_id,
                        'organizer' => false,
                        'delegate' => false,
                        'competitor' => $competition->my_roles_competitor > 0
            ];
        }
        $competitions_key[$competition->id] = $competition_key;
    }
    $organizers = \db::rows("SELECT 
                            o.competition,
                            dc.wcaid,
                            dc.wid,
                            coalesce(dc.name$RU, dc.name) name
                        FROM unofficial_organizers o
                        JOIN dict_competitors dc on o.wcaid in (coalesce(dc.wcaid,'X'),coalesce(dc.wid,'X')) 
                        ORDER BY 3");
    foreach ($organizers as $organizer) {
        if ($competitions_key[$organizer->competition] ?? false) {
            $main_organizer = $competitions_key[$organizer->competition]->organizers[0]->wca_id;
            if ($main_organizer != $organizer->wcaid) {
                $competitions_key[$organizer->competition]->organizers[] = (object) [
                            'wca_id' => $organizer->wcaid,
                            'wid' => $organizer->wid,
                            'name' => $organizer->name,
                            'main' => false
                ];
                if ($wca_id) {
                    $competitions_key[$organizer->competition]->my_roles->organizer = (($competitions_key[$organizer->competition]->my_roles->organizer ?? false)
                            or $organizer->wcaid == $wca_id or $organizer->wid == $wca_id);
                }
            }
        }
    }

    $delegates = \db::rows("SELECT 
                            cj.competition_id competition,
                            cj.delegate wcaid,
                            j.vk vk,
                            j.telegram telegram,
                            j.phone phone,
                            j.email email,
                            coalesce(dc.name$RU, dc.name) name,
			    coalesce(jrd.role$RU, jrd.role) role
                        FROM unofficial_competition_delegates cj
                        LEFT OUTER JOIN unofficial_delegates j on j.wcaid=cj.delegate
                        JOIN unofficial_delegate_roles_dict jrd on jrd.id=cj.dict_delegate_role
                        JOIN dict_competitors dc on dc.wcaid=cj.delegate");
    foreach ($delegates as $delegate) {
        if ($competitions_key[$delegate->competition] ?? false) {
            $competitions_key[$delegate->competition]->delegates ??= [];
            $competitions_key[$delegate->competition]->delegates[] = (object) [
                        'wca_id' => $delegate->wcaid,
                        'name' => $delegate->name,
                        'role' => $delegate->role,
                        'vk' => $delegate->vk,
                        'telegram' => $delegate->telegram,
                        'phone' => $delegate->phone,
                        'email' => $delegate->email
            ];
            if ($wca_id) {
                $competitions_key[$delegate->competition]->my_roles->delegate = (($competitions_key[$delegate->competition]->my_roles->delegate ?? false)
                        or $delegate->wcaid == $wca_id);
            }
        }
    }

    $sheets = \db::rows("SELECT 
                            competition_id competition,
                            content,
                            title,
                            sheet
                        FROM unofficial_competition_sheets 
                        WHERE is_archive = 0
                        ORDER BY `order`");
    foreach ($sheets as $sheet) {
        if ($competitions_key[$sheet->competition] ?? false) {
            $competitions_key[$sheet->competition]->sheets ??= [];
            $competitions_key[$sheet->competition]->sheets[] = (object) [
                        'sheet' => $sheet->sheet,
                        'title' => $sheet->title,
                        'content' => $sheet->content
            ];
        }
    }


    if ($wca_id) {
        foreach ($competitions_key as $competition_id => $competition_key) {

            $admin = $me->is_admin ?? false;
            $federation = $me->is_federation ?? false;
            $main_organizer = $competition_key->my_roles->main_organizer ?? false;
            $organizer = $competition_key->my_roles->organizer ?? false;
            $delegate = $competition_key->my_roles->delegate ?? false;

            $edit_grand = ($main_organizer or $organizer or $delegate);
            
            $view_grand = $edit_grand; 

            $setting_grand = ($main_organizer or $delegate);

            $federation_grand = $federation;

            $admin_grand = false;
            if ($competition_key->is_approved) {
                $edit_grand = false;
                $setting_grand = false;
            }

            if ($federation and $competition_key->is_ranked) {
                $edit_grand = true;
                $setting_grand = true;
            }

            if ($admin) {
                $edit_grand = true;
                $view_grand = true;
                $setting_grand = true;
                $federation_grand = true;
                $admin_grand = true;
            }

            $competitions_key[$competition_id]->grand = (object) [
                        'edit' => $edit_grand,
                        'view' => $view_grand,
                        'setting' => $setting_grand,
                        'federation' => $federation_grand,
                        'admin' => $admin_grand
            ];
        }
    }


    return $competitions_key;
}
