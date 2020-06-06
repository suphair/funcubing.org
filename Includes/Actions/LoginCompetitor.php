<?php

Suphair \ Wca \ Oauth::set(
        GetIni('WCA_OAUTH', 'client_id')
        , GetIni('WCA_OAUTH', 'client_secret')
        , GetIni('WCA_OAUTH', 'scope')
        , PageIndex() . GetIni('WCA_OAUTH', 'url_refer')
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