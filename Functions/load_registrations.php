<?php

function load_registrations() {
    $competitions = db::rows("select 
                    `unofficial_competitions`.`id`,
                    `unofficial_competitions`.`name`,
                    `unofficial_competitions`.`adapter`,
                    `unofficial_competitions`.`adapter_url`,
                    `unofficial_competitions`.`ranked`,
                    `unofficial_competitions`.`rankedID`
            from 
                    `unofficial_competitions`
            where 
                    `unofficial_competitions`.`date` > now() 
                    and `unofficial_competitions`.`adapter` is not null
            ");
    $results = [];


    foreach ($competitions as $competition) {
        $adapter = "adapter\\" . $competition->adapter;
        if (function_exists($adapter)) {
            $url = $competition->adapter_url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status != 200) {
                sendMail(
                        config::get('Support', 'email'), "FunCubing: $competition->name (load_registrations)"
                        , "$competition->name $url: status=$status"
                );
                continue;
            }
            $dataJson = json_decode($data, JSON_UNESCAPED_UNICODE);
            $registrations = $adapter($dataJson);
            $new = [];
            $delete = [];

            foreach ($registrations as $r => $registration) {

                $renameRU = db::row("SELECT nameRU "
                                . " from `unofficial_rename` "
                                . "     where UPPER(description) = UPPER('$competition->rankedID') AND ( "
                                . "     ('$registration->wca_id'<>'' AND UPPER(wcaid) = UPPER('$registration->wca_id'))"
                                . "         or UPPER(name) = UPPER('$registration->name')"
                                . "     )")->nameRU ?? false;
                if ($renameRU) {
                    $registration->name = $renameRU;
                }



                $registration->description = $registration->name . '/' . $registration->wca_id;
                $registration->non_resident = ($registration->name == transliterate($registration->name)) + 0;
                if ($registration->non_resident) {
                    if ($registration->wca_id) {
                        $find_FCID = db::row("SELECT fw.WCAID,fw.FCID,uc.name
                        FROM unofficial_fc_wca fw 
                        JOIN `unofficial_competitors` uc on uc.FCID=fw.FCID
                        WHERE fw.WCAID='$registration->wca_id'");
                        if ($find_FCID) {
                            $registration->name = $find_FCID->name;
                            $registration->non_resident = 0;
                        } else {
                            $registration->name .= $registration->wca_id ? (': ' . $registration->wca_id) : '';
                        }
                    }
                }
                $registrations[$r] = $registration;
            }

            $registrations_name = [];
            foreach ($registrations as $registration) {
                $registrations_name[] = $registration->name;
            }

            foreach (db::rows("SELECT id,name FROM unofficial_competitors where competition=$competition->id") as $row) {
                if (!in_array($row->name, $registrations_name)) {
                    db::exec("DELETE FROM unofficial_competitors_round WHERE competitor = $row->id");
                    db::exec("DELETE FROM unofficial_competitors WHERE id = $row->id");
                    $delete[] = $row->name;
                }
            }

            foreach ($registrations as $registration) {
                db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name, non_resident, description) "
                        . "VALUES ($competition->id,'$registration->name',$registration->non_resident,'$registration->description')");
                $id = db::id();
                if ($competition->ranked and $id and!$registration->non_resident) {
                    unofficial\set_fc_id($id, $registration->name);
                }
                unofficial\updateCompetitionCard($competition->id);
                if ($id) {
                    $new[] = $registration->name;
                }

                $competitor = db::row("SELECT id,FCID FROM unofficial_competitors WHERE competition = $competition->id AND name = '$registration->name'");
                $competitor_id = $competitor->id ?? FALSE;
                if ($competitor_id) {
                    foreach ($registration->events as $event => $bool) {
                        $round = db::row("SELECT unofficial_events_rounds.id "
                                        . " FROM unofficial_events  "
                                        . " JOIN unofficial_events_rounds on unofficial_events_rounds.event = unofficial_events.id "
                                        . " JOIN `unofficial_events_dict` on `unofficial_events_dict`.id = unofficial_events.event_dict "
                                        . " WHERE unofficial_events_rounds.round = 1 "
                                        . " AND unofficial_events_dict.code = '$event'"
                                        . " AND unofficial_events.competition = $competition->id");
                        if ($round->id ?? FALSE) {
                            if ($bool) {
                                db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor, round) VALUES ($competitor_id,$round->id)");
                            } else {
                                db::exec("DELETE IGNORE FROM unofficial_competitors_round"
                                        . " WHERE competitor = $competitor_id AND round = $round->id ");
                            }
                        }
                    }
                    $fc_id = $competitor->FCID ?? false;
                    if ($fc_id) {
                        unofficial\set_wca($fc_id, $registration->wca_id, $registration->non_wca);
                    }
                }
            }
            $results[$competition->id] = [
                'status' => $status,
                'count' => sizeof($dataJson),
                'new' => sizeof($new),
                'delete' => sizeof($delete)
            ];

            if (sizeof($new) or sizeof($delete)) {
                sendMail(
                        config::get('Admin', 'email'), "FunCubing: $competition->name (load_registrations)"
                        , 'new:' . print_r($new, true) . '<br>delete:' . print_r($delete, true)
                );
            }
        } else {
            user_error("$adapter not exists");
        }
    }

    return json_encode($results,
            JSON_UNESCAPED_SLASHES +
            JSON_UNESCAPED_UNICODE);
}
