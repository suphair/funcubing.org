<?php

$me = wcaoauth::me();
$secret = db::escape(request(1));
$action = filter_input(INPUT_GET, 'action');
$rounds_dict = unofficial\getRoundsDict();
$notAuthorized = in_array($action, ['result', 'projector', 'mobile', 'certificates','competitor']);

if (!$secret) {
    include 'action.wrong.php';
} elseif ($secret == 'competitor') {
    if ($action == 'certificate') {
        $competitor_id = db::escape(request(2));
        $competitor = unofficial\getCompetitor($competitor_id);
        if ($competitor) {
            include 'action.certificate.php';
        } else {
            include 'action.competitor.certificate.notfound.php';
        }
    } else {
        include 'action.wrong.php';
    }
} elseif ($me->wca_id ?? FALSE or $notAuthorized) {

    $comp = unofficial\getCompetition($secret, $me);
    $competition = api\get_competition($secret);
    $grand = $competition->grand ?? null;
    $comp_data = unofficial\getCompetitionData($comp->id ?? -1);
    $events_dict = unofficial\getEventsDict();
    $formats_dict = unofficial\getFormatsDict();
    $results_dict = unofficial\getResultsDict();
    if (!$comp) {
        include 'action.competition.notfound.php';
    } elseif ($grand->edit ?? false or $notAuthorized) {
        switch ($action) {
            case 'cards':
                include 'action.cards.php';
                break;
            case 'result':
                include 'action.result.php';
                break;
            case 'competitor':
                include 'action.competitor.php';
                break;
            case 'projector':
                include 'action.projector.php';
                break;
            case 'mobile':
                include 'action.mobile.php';
                break;
            case 'certificates':
                include 'action.certificate.php';
                break;
            case 'export':
                include 'action.export.php';
                break;
            case 'scoketaker':
                $event_code=db::escape(request(3));
                if($event_code=='333mbf'){
                    include 'action.scoketaker.333mbf.php';
                }else{
                    include 'action.scoketaker.php';
                }
                break;
            default:
                include 'action.wrong.php';
        }
    } else {
        include 'action.accessdenied.php';
    }
} else {
    include 'action.accessdenied.php';
}
