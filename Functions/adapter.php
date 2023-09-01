<?php

namespace adapter;

function common($input) {
    //{
    //  "name": string,
    //  "surname": string,
    //  "wca_id":  string,
    //  "non_wca":  bool,
    //  "events": [string]
    //}    

    $registrations = [];
    foreach ($input as $row) {
        $row = (object) $row;
        $registration = (object) [
                    'name' => trim($row->name),
                    'wca_id' => strtoupper($row->wca_id),
                    'non_wca' => $row->non_wca ? 1 : 0,
                    'events' => []
        ];
        $events = [];
        foreach ($row->events as $event) {
            $events[$event] = 1;
        }
        $registration->events = $events;
        $registrations[$registration->name] = $registration;
    }
    return $registrations;
}

function speedcubes($input) {
    //{
    //  "name": string,
    //  "surname": string,
    //  "wca_id":  string,
    //  "non_wca":  bool,
    //  "events": [string]
    //}    
    $event_map = [
        '222' => '222',
        '333' => '333',
        '444' => '444',
        '555' => '555',
        'oh' => '333oh',
        'clock' => 'clock',
        'pyra' => 'pyram',
        'skewb' => 'skewb',
        'ivi' => 'ivy',
        'minx' => 'minx',
        'sq1' => 'sq1',
        'blind' => '333bf'
    ];

    $registrations = [];
    foreach ($input as $row) {
        $row = (object) $row;
        $registration = (object) [
                    'name' => trim($row->name) . ' ' . trim($row->surname),
                    'wca_id' => strtoupper($row->wcaid),
                    'non_wca' => !$row->wcaid + 0,
                    'events' => []
        ];
        $events = [];
        foreach ($event_map as $from => $to) {
            $events[$to] = ($row->$from ?? false) + 0;
        }
        $registration->events = $events;
        $registrations[$registration->name] = $registration;
    }
    return $registrations;
}
