<?php

$delegates = filter_input(INPUT_POST, 'delegates', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

foreach ($delegates as $delegate) {

    $nameRU = db::escape($delegate['nameRU'] ?? false);
    $rankEN = db::escape($delegate['rankEN'] ?? false);
    $rankRU = db::escape($delegate['rankRU'] ?? false);
    $regionEN = db::escape($delegate['regionEN'] ?? false);
    $regionRU = db::escape($delegate['regionRU'] ?? false);
    $is_archive = db::escape($delegate['is_archive'] ?? false) ? 1 : 0;
    $wcaid = db::escape($delegate['wcaid'] ?? false);
    $vk = db::escape($delegate['vk'] ?? false);
    $telegram = db::escape($delegate['telegram'] ?? false);
    $phone = db::escape($delegate['phone'] ?? false);
    $email = db::escape($delegate['email'] ?? false);


    $telegram = str_replace(['https://t.me/'], '', $telegram);
    $vk = str_replace(['https://vk.com/'], '', $vk);

    if ($wcaid) {
        db::exec("UPDATE unofficial_delegates
        SET 
        is_archive = $is_archive,rank = '$rankEN', rankRU = '$rankRU', region = '$regionEN', regionRU = '$regionRU',
        vk = '$vk', telegram = '$telegram', phone = '$phone', email = '$email'
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