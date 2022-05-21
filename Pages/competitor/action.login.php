<?php

wcaoauth::set(
        config::get('WCA_OAUTH', 'client_id')
        , config::get('WCA_OAUTH', 'client_secret')
        , config::get('WCA_OAUTH', 'scope')
        , PageIndex() . config::get('WCA_OAUTH', 'url_refer')
        , db::connection()
);

wcaoauth::out();
$competitor = wcaoauth::authorize();
if ($competitor) {
    competitor\actual($competitor);
}

wcaoauth::location();
exit();
