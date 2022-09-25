<?php

function autoCheck() {
    $urls = [
        'api/',
        'api/competitions',
        'api/competitions/ASUOpen2022',
        'api/competitions/SyktyvkarOpen2022/registrations',
        'api/competitions/SyktyvkarOpen2022/events',
        'api/competitions/SyktyvkarOpen2022/results',
        'competitions',
        'competitions?show=ranked',
        'competitions/SyktyvkarOpen2022',
        'competitions/SyktyvkarOpen2022/events',
        'competitions/SyktyvkarOpen2022/competitors',
        'competitions/SyktyvkarOpen2022/event/333/1',
        'competitions/SyktyvkarOpen2022/event/333/1?action=projector',
        'competitions/SyktyvkarOpen2022/event/333/1?action=mobile',
        'competitions/SyktyvkarOpen2022/event/333/1?action=result',
        'competitions/SyktyvkarOpen2022/event/333/1?action=cards',
        'competitions/rankings',
        'competitions/rankings/competitors',
        'competitions/rankings/competitions',
        'competitions/rankings/delegates',
        'competitions/rankings/333',
        'competitions/rankings/333/average',
        'competitions/rankings/competitor/AZ01',
        'competitions/competitor/2812?action=certificate',
        'competitions/competitor/2777',
        'competitions/competitor/2777?action=certificate'
    ];
    $complete = [];
    foreach ($urls as $url) {
        set_error_handler(function() {
            return false;
        });
        file_get_contents(config::get('AUTOCHECK', 'protocol') . ':' . PageIndex() . $url);
        if (!isset($complete[$http_response_header[0]])) {
            $complete[$http_response_header[0]] = 0;
        }
        $complete[$http_response_header[0]]++;
    }
    return json_encode($complete);
}
