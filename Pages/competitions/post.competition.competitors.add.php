<?php

$competitors = explode(",", str_replace("\n", ",", filter_input(INPUT_POST, 'competitors')));

foreach ($competitors as $competitor) {

    $competitor_count = db::row("select count(*) count from `unofficial_competitors` where competition = $comp->id")->count ?? 0;

    if ($competitor_count < $comp->rankedCompetitors or!$comp->ranked) {
        $names = [];
        $events = [];
        $competitor = str_replace(chr(13), "", $competitor);
        $words = explode(" ", $competitor);
        foreach ($words as $n => $word) {
            if (!$word) {
                continue;
            }
            $event = $comp_data->event_dict->by_code[$word]->id ?? FALSE;
            if ($event) {
                $events[] = $event;
            } else {
                $names[] = $word;
            }
        }
        $FCID = false;
        $events = array_unique($events);
        $name = strip_tags(db::escape(implode(' ', $names)));
        $name = str_replace('ë', 'ё', $name);
        
        /*PATCH*/
        db::exec("UPDATE unofficial_competitors SET name = replace(name,'ë', 'ё')");
        
        if (strlen($name) == 4) {
            $FCID = $name;
            $row = db::row("SELECT name FROM unofficial_competitors WHERE FCID = '$FCID'");
            $name = $row->name ?? $name;
        }

        if ($name) {
            if (!$FCID) {
                db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name) VALUES ($comp->id,'$name')");
                if ($comp->ranked) {
                    $FCID = unofficial\set_fc_id(db::id(), $name);
                    $new_name = str_replace('*', $FCID, $name);
                    if ($new_name != $name) {
                        db::exec("UPDATE unofficial_competitors SET name = '$new_name' WHERE competition = $comp->id AND name = '$name'");
                        $name = $new_name;
                    }
                }
                $competitor_id = db::row("SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND name = '$name'")->id ?? FALSE;
            } else {
                db::exec("INSERT IGNORE INTO unofficial_competitors (competition, name,FCID) VALUES ($comp->id,'$name','$FCID')");
                $competitor_id = db::row("SELECT id FROM unofficial_competitors WHERE competition = $comp->id AND name = '$name' AND FCID='$FCID'")->id ?? FALSE;
            }
            unofficial\updateCompetitionCard($comp->id);
        }


        foreach ($events as $event_dict) {
            $round = db::row("SELECT unofficial_events_rounds.id "
                            . " FROM unofficial_events  "
                            . " JOIN unofficial_events_rounds on unofficial_events_rounds.event = unofficial_events.id "
                            . " WHERE unofficial_events_rounds.round = 1 "
                            . " AND unofficial_events.event_dict = $event_dict"
                            . " AND unofficial_events.competition = $comp->id");
            if ($round->id ?? FALSE and $competitor_id ?? FALSE) {
                db::exec("INSERT IGNORE INTO unofficial_competitors_round (competitor, round) VALUES ($competitor_id,$round->id)");
            }
        }
    }
}