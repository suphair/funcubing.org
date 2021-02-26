

<link href="<?= PageIndex() ?>Styles/unofficial.css" rel="stylesheet">
<?php
$me = wcaoauth::me() ?? FALSE;
$secret = db::escape(request(1));
if ($secret == 'competitor') {
    $competitor_id = request(2);
    $competitor = unofficial\getCompetitor($competitor_id);
    if ($competitor) {
        include 'competitor.php';
    } else {
        include 'competitor.notfound.php';
    }
} elseif ($secret) {
    $comp = unofficial\getCompetition($secret, $me);

    if ($comp->id ?? FALSE) {
        $comp_data = unofficial\getCompetitionData($comp->id);
        $events_dict = unofficial\getEventsDict();
        $formats_dict = unofficial\getFormatsDict();
        $rounds_dict = unofficial\getRoundsDict();
        $results_dict = unofficial\getResultsDict();

        $section = db::escape(request(2));
        switch ($section) {
            case 'registrations':
                if(db::escape(request(3))=='api'){
                    $include = 'competition.registrations.api.php';
                }
                elseif ($comp->my or $comp->organizer) {
                    $include = 'competition.registrations.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'setting':
                if ($comp->my) {
                    $include = 'competition.setting.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'registration':
                $secretRegistration = request(3);
                if (!$comp->secretRegistration) {
                    $include = 'competition.registration.close.php';
                } elseif ($comp->secretRegistration == $secretRegistration) {
                    $include = 'competition.registration.php';
                } else {
                    $include = 'competition.registration.wrong.php';
                }
                break;
            case 'event':
            case 'result':
                $code = request(3);
                $round = request(4);
                $event_dict = $comp_data->event_dict->by_code[$code]->id ?? FALSE;
                if (!$round) {
                    $round = 1;
                }
                $event_round_this = $comp_data->rounds[$event_dict][$round]->round->id ?? null;
                $include = 'competition.index.php';
                break;
            case false:
                $event_round_this = false;
                $include = 'competition.index.php';
                break;
            default:
                $include = 'competition.section.wrong.php';
        }
        include 'competition.php';
    } else {
        include 'competition.notfound.php';
    }
} else {
    include 'competitions.php';
}