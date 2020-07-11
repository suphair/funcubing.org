<?php
if ((wcaoauth::me()->wca_id ?? FALSE) == config::get('Admin', 'wcaid')
        or filter_input(INPUT_SERVER, 'HTTP_USER_AGENT') == 'Wget/1.17.1 (linux-gnu)'
        or config::isLocalhost()) {
    $cron = new cron(db::connection());
    $cron->run();
    db::close();
    die('Cron is complete');
} else {
    die('Access denied');
}

