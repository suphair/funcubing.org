<?php

Suphair \ Wca \ Oauth::set(
        GetIni('WCA_AUTH', 'client_id')
        , GetIni('WCA_AUTH', 'client_secret')
        , GetIni('WCA_AUTH', 'scope')
        , PageIndex() . GetIni('WCA_AUTH', 'url_refer')
        , DataBaseClass::getConection()
);

$competitor = Suphair \ Wca \ Oauth::authorize();

unset($_SESSION['Competitor']);

if ($competitor) {
    $_SESSION['Competitor'] = $competitor;
    $name = Short_Name($competitor->name);
    $wcaid = $competitor->wca_id;
    $wid = $competitor->id;
    $country = $competitor->country_iso2;
    competitorActual($wcaid, $wid, $name, $country);
}
Suphair \ Wca \ Oauth::location();
?>  