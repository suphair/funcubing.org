<?php

$partners = filter_input(INPUT_POST, 'partners', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

db::exec("DELETE FROM unofficial_partners WHERE competitor = $organizer_id");
foreach ($partners as $partner_id) {
    if (is_numeric($partner_id)) {
        db::exec("INSERT IGNORE INTO unofficial_partners (competitor, partner) VALUES ($organizer_id, $partner_id)");
    }
}
