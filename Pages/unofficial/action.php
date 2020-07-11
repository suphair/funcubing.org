<?php

$me = wcaoauth::me();
$secret = db::escape(request(1));
$action = filter_input(INPUT_GET, 'action');

if (!$secret) {
    include 'action.wrong.php';
} elseif ($secret == 'competitor') {
    if ($action == 'certificate') {
        $competitor_id = db::escape(request(2));
        $competitor = unofficial\getCompetitor($competitor_id);
        if($competitor){
            include 'action.certificate.php';
        }else{
            include 'action.competitor.certificate.notfound.php';    
        }
    } else {
        include 'action.wrong.php';
    }
} elseif ($me->wca_id ?? FALSE) {

    $comp = unofficial\getCompetition($secret, $me);
    $comp_data = unofficial\getCompetitionData($comp->id);
    $events_dict = unofficial\getEventsDict();
    $formats_dict = unofficial\getFormatsDict();
    $rounds_dict = unofficial\getRoundsDict();
    $results_dict = unofficial\getResultsDict();

    if ($comp->my ?? FALSE) {
        switch ($action) {
            case 'cards':
                include 'action.cards.php';
                break;
            case 'result':
                include 'action.result.php';
                break;
            case 'certificates':
                include 'action.certificate.php';
                break;
            case 'export':
                include 'action.export.php';
                break;
            default:
                include 'action.wrong.php';
        }
    } elseif(!$comp) {
        include 'action.competition.notfound.php';
    }else{
        include 'action.accessdenied.php';
    }
} else {
    include 'action.accessdenied.php';
}
