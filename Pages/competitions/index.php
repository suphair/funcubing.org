<link href="<?= PageIndex() ?>Styles/competitions.css" rel="stylesheet">
<?php
$me = wcaoauth::me() ?? FALSE;
$secret = db::escape(request(1));
$admin = \api\get_me()->is_admin ?? false;
$federation = \api\get_me()->is_federation ?? false;
$ranked_icon = '<img width="16px" align="top" src="' . PageIndex() . 'Pages/competitions/FC.png?1" title="' . t('Speedcubing Federation', 'Федерация Спидкубинга') . '"></img>';
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
} elseif ($secret == 'create') {
    include 'competition.create.php';
} elseif ($secret) {
    $competition = api\get_competition($secret);
    $comp = unofficial\getCompetition($secret, $me);
    if ($comp->id ?? FALSE) {
        change_title($competition->name);
        $secret = $competition->id;
        $comp_data = unofficial\getCompetitionData($competition->local_id);
        $events_dict = unofficial\getEventsDict();
        $formats_dict = unofficial\getFormatsDict();
        $rounds_dict = unofficial\getRoundsDict();
        $results_dict = unofficial\getResultsDict();
        $points_dict = unofficial\getPointsDict();
        $events_list = false;
        $grand = $competition->grand ?? (object) [
                    'edit' => false,
                    'view' => false,
                    'setting' => false,
                    'federation' => false,
                    'admin' => false
        ];

        $section = db::escape(request(2));
        switch ($section) {
            case 'wcaid':
                if ($competition->is_ranked and $grand->edit) {
                    $include = 'competition.wcaid.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'registrations':
                if ($grand->edit) {
                    $event_round_this = false;
                    $include = 'competition.index.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'setting':
            case 'setting_events':
            case 'setting_sheets':
                if ($grand->setting) {
                    $event_round_this = false;
                    $include = 'competition.index.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case 'ranking':
                if ($grand->federation) {
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
                $event_round_this = true;
                $include = 'competition.index.php';
                break;
            case 'result':
            case 'event_competitors':
                if ($grand->edit) {
                    $event_round_this = true;
                    $include = 'competition.index.php';
                } else {
                    $include = 'competition.accessdenied.php';
                }
                break;
            case false:
                $section = 'info';
            case 'competitors':
            case 'psychsheet':
            case 'wrongresults':
            case 'records':
            case 'events':
            case 'points':
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
?>
<br>
<script>
<?php include 'thead_stable.js' ?>
</script>