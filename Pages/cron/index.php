<?php
if ((wcaoauth::me()->wca_id ?? FALSE) == config::get('Admin', 'wcaid')
        or filter_input(INPUT_SERVER, 'HTTP_USER_AGENT') == 'Wget/1.19.5 (linux-gnu)'
        or config::isLocalhost()) {
    $cron = new cron(db::connection());
    $cron->run();
    db::close();
    die(json_encode(['message' => 'Cron is complete']));
} else {
    die(json_encode(['error' => 'Access denied']));
}

