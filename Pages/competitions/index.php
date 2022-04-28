<link href="<?= PageIndex() ?>Styles/competitions.css?3" rel="stylesheet">
<?php
$me = wcaoauth::me() ?? FALSE;
$secret = db::escape(request(1));
$ranked_icon = '<img width="16px" align="top" src="' . PageIndex() . 'Pages/competitions/FC.png" title="' . t('Speedcubing Federation', 'Федерация Спидкубинга') . '"></img>';
$wca_icon = '<img width="16px" align="top" src="' . PageIndex() . 'Pages/competitions/WCA.png"></img>';
if ($secret == 'competitor') {
    $competitor_id = request(2);
    $competitor = unofficial\getCompetitor($competitor_id);
    if ($competitor) {
        include 'competitor.php';
    } else {
        include 'competitor.notfound.php';
    }
} elseif ($secret == 'rankings') {
    $rounds_dict = unofficial\getRoundsDict();
    include 'rankings.php';
} elseif ($secret) {
    $comp = unofficial\getCompetition($secret, $me);
    if ($comp->id ?? FALSE) {
        change_title($comp->name);
        $secret = $comp->secret;
        $comp_data = unofficial\getCompetitionData($comp->id);
        $events_dict = unofficial\getEventsDict();
        $formats_dict = unofficial\getFormatsDict();
        $rounds_dict = unofficial\getRoundsDict();
        $results_dict = unofficial\getResultsDict();
        $events_list = false;

        $section = db::escape(request(2));

        switch ($section) {
            case 'wcaid':
                if ($comp->ranked and ($comp->my or $comp->organizer or unofficial\federation() )) {
                    $include = 'competition.wcaid.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'registrations':
                if (($comp->my or $comp->organizer) and!$comp->approved) {
                    $include = 'competition.registrations.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'setting':
                if ($comp->my and!$comp->approved) {
                    $include = 'competition.setting.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'ranking':
                if (unofficial\federation()) {
                    $include = 'competition.ranking.php';
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
            case 'event_competitors':
                $event_round_this = true;
                $include = 'competition.index.php';
                break;
            case false:
                $section = 'info';
            case 'competitors':
            case 'records':
            case 'events':
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
?><br>