<?php

$secret = db::escape(request(1));
if ($secret) {
    $comp = unofficial\getCompetition($secret);
    if ($comp->id ?? FALSE) {
        if (filter_input(INPUT_GET, 'registration_add') !== NULL) {
            include 'post.registration.add.php';
        }
        if (filter_input(INPUT_GET, 'registration_delete') !== NULL) {
            include 'post.registration.delete.php';
        }
    }
}

$me = wcaoauth::me();
if ($me->wca_id ?? FALSE) {
    if (filter_input(INPUT_GET, 'create') !== NULL) {
        include 'post.competition.create.php';
    }

    $secret = db::escape(request(1));
    if ($secret) {
        $comp = unofficial\getCompetition($secret, $me);
        if ($comp->my ?? FALSE) {

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
        }
        if ($comp->my ?? FALSE or $comp->organizer ?? FALSE) {

            if (filter_input(INPUT_GET, 'competitors_add') !== NULL) {
                $comp_data = unofficial\getCompetitionData($comp->id);
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

            if (filter_input(INPUT_GET, 'results_delete') !== NULL) {
                include 'post.results.delete.php';
            }

            if (filter_input(INPUT_GET, 'results_add') !== NULL) {
                include 'post.results.add.php';
            }

            if (filter_input(INPUT_GET, 'resuts_registrations_add_first') !== NULL) {
                include 'post.resuts.registrations.add.first.php';
            }

            if (filter_input(INPUT_GET, 'resuts_registration_add') !== NULL) {
                include 'post.resuts.registration.add.php';
            }

            if (filter_input(INPUT_GET, 'resuts_registrations_add_next') !== NULL) {
                $comp_data = unofficial\getCompetitionData($comp->id);
                include 'post.resuts.registrations.add.next.php';
            }
        }
    }

    if (request(1) == 'rankings') {
        $organizer_id = request(2);
        $organizer = unofficial\getOrganizer($organizer_id);
        if ($organizer->wid ?? FALSE) {
            if (filter_input(INPUT_GET, 'rankings_organizers_add') !== NULL) {
                include 'post.rankings.organizers.add.php';
            }
        }
    }
}
