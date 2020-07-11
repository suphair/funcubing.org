<?php
    if (isset($_POST['countries'])) {
        $countries_post = $_POST['countries'];
    } else {
        $countries_post = [];
    }

    $countries = db::rows("SELECT iso2 FROM dict_countries ORDER BY name");
    foreach ($countries as $country) {
        $countries[$country->iso2] = TRUE;
    }

    foreach ($countries_post as $c => $country) {
        if (!isset($countries[db::escape($country)])) {
            unset($countries_post[$c]);
        }
    }
    if (!$countries_post) {
        $countries_post = [$me->country_iso2];
    }

    db::exec("UPDATE announcements "
            . " SET countries = '" . json_encode($countries_post) . "' "
            . " WHERE user = {$me->id}");
