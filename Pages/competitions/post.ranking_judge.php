<?php

$judges = filter_input(INPUT_POST, 'judges', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

foreach ($judges as $judge) {
  
    $nameRU = db::escape($judge['nameRU'] ?? false);
    $rankEN = db::escape($judge['rankEN'] ?? false);
    $rankRU = db::escape($judge['rankRU'] ?? false);
    $regionEN = db::escape($judge['regionEN'] ?? false);
    $regionRU = db::escape($judge['regionRU'] ?? false);
    $is_archive = db::escape($judge['is_archive'] ?? false) ? 1 : 0;
    $wcaid = db::escape($judge['wcaid'] ?? false);

    if ($wcaid) {
        db::exec("UPDATE unofficial_judges
        SET is_archive = $is_archive, rank = '$rankEN', rankRU = '$rankRU', region = '$regionEN', regionRU = '$regionRU'
        WHERE wcaid='$wcaid'");

        if ($nameRU) {
            db::exec("UPDATE dict_competitors
        SET nameRU = '$nameRU'
        WHERE wcaid='$wcaid'");
        } else {
            db::exec("UPDATE dict_competitors
        SET nameRU = null
        WHERE wcaid='$wcaid'");
        }
    }
}
?>