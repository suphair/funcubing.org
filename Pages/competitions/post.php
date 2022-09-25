<?php

$me = api\get_me();
$secret = db::escape(request(1));
$grand = ($me->is_admin ?? FALSE or $me->is_federation ?? FALSE);
if ($secret) {
    $competition = api\get_competition($secret);
    $comp = unofficial\getCompetition($secret);
    if ($competition) {
        if (filter_input(INPUT_GET, 'registration_add') !== NULL) {
            include 'post.registration.add.php';
        }
        if (filter_input(INPUT_GET, 'registration_delete') !== NULL) {
            include 'post.registration.delete.php';
        }
        if (filter_input(INPUT_GET, 'ranking_competitors') !== NULL) {
            include 'post.ranking_competitors.php';
        }
    } else {
        if (filter_input(INPUT_GET, 'ranking_competitor') !== NULL and $grand) {
            include 'post.ranking_competitor.php';
        }
        if (filter_input(INPUT_GET, 'ranking_delegate') !== NULL and $grand) {
            include 'post.ranking_delegate.php';
        }
        if (filter_input(INPUT_GET, 'ranking_delegate_add') !== NULL and $grand) {
            include 'post.ranking_delegate_add.php';
        }
        if (filter_input(INPUT_GET, 'ranking_rename_delete') !== NULL and $grand) {
            include 'post.ranking_rename_delete.php';
        }
        if (filter_input(INPUT_GET, 'ranking_rename_add') !== NULL and $grand) {
            include 'post.ranking_rename_add.php';
        }
    }
}

if ($me->wca_id ?? FALSE) {
    if (filter_input(INPUT_GET, 'create') !== NULL) {
        include 'post.competition.create.php';
    }

    if (filter_input(INPUT_GET, 'text') !== NULL and $grand ?? false) {
        include 'post.text.php';
    }

    if ($competition) {
        $grand = $competition->grand;
        if (filter_input(INPUT_GET, 'rankings_settings') !== NULL and $grand->federation ?? false) {
            include 'post.competition.rankings_settings.php';
        }

        if (filter_input(INPUT_GET, 'organizer_rename') !== NULL and $grand->federation ?? false) {
            include 'post.organizer_rename.php';
        }

        if ($grand->setting ?? false) {

            if (filter_input(INPUT_GET, 'setting') !== NULL) {
                include 'post.competition.setting.php';
            }

            if (filter_input(INPUT_GET, 'organizer_add') !== NULL) {
                include 'post.competition.organizer.add.php';
            }

            if (filter_input(INPUT_GET, 'organizer_remove') !== NULL) {
                include 'post.competition.organizer.remove.php';
            }

            if (filter_input(INPUT_GET, 'rounds') !== NULL) {
                $events_dict = unofficial\getEventsDict();
                $formats_dict = unofficial\getFormatsDict();
                $rounds_dict = unofficial\getRoundsDict();
                $results_dict = unofficial\getResultsDict();
                include 'post.competition.rounds.php';
            }

            if (filter_input(INPUT_GET, 'delete') !== NULL) {
                include 'post.competition.delete.php';
            }

            if (filter_input(INPUT_GET, 'comments') !== NULL) {
                $events_dict = unofficial\getEventsDict();
                include 'post.competition.comments.php';
            }

            if (filter_input(INPUT_GET, 'sheet_create') !== NULL) {
                include 'post.competition.sheet.create.php';
            }
            if (filter_input(INPUT_GET, 'sheet_edit') !== NULL) {
                include 'post.competition.sheet.edit.php';
            }
            if (filter_input(INPUT_GET, 'sheet_delete') !== NULL) {
                include 'post.competition.sheet.delete.php';
            }
        }
        if ($grand->edit ?? false) {

            if (filter_input(INPUT_GET, 'competitors_add') !== NULL) {
                $comp_data = unofficial\getCompetitionData($competition->local_id);
                include 'post.competition.competitors.add.php';
            }

            if (filter_input(INPUT_GET, 'competitors_select') !== NULL) {
                include 'post.competition.competitors.select.php';
            }

            if (filter_input(INPUT_GET, 'competitors_edit') !== NULL) {
                include 'post.competition.competitors.edit.php';
            }

            if (filter_input(INPUT_GET, 'competitors_delete') !== NULL) {
                include 'post.competition.competitors.delete.php';
            }

            if (filter_input(INPUT_GET, 'result_delete') !== NULL) {
                include 'post.result.delete.php';
            }

            if (filter_input(INPUT_GET, 'results_add') !== NULL) {
                include 'post.results.add.php';
            }

            if (filter_input(INPUT_GET, 'close_round') !== NULL) {
                include 'post.results.close_round.php';
            }

            if (filter_input(INPUT_GET, 'resuts_registrations_add_first') !== NULL) {
                include 'post.resuts.registrations.add.first.php';
            }

            if (filter_input(INPUT_GET, 'resuts_registration_add') !== NULL) {
                include 'post.resuts.registration.add.php';
            }

            if (filter_input(INPUT_GET, 'resuts_registrations_add_next') !== NULL) {
                $comp_data = unofficial\getCompetitionData($competition->local_id);
                include 'post.resuts.registrations.add.next.php';
            }
        }
    }
}


$return_refer = filter_input(INPUT_POST, 'return_refer');

if ($return_refer == 'true') {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} elseif ($return_refer) {
    header("Location: " . $return_refer);
    exit();
} 
