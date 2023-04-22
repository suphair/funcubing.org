<?php

header('Content-Type: application/json; charset=utf-8');
$json_scrambles = \db::row("select json from scrambles where competition = $competition->local_id") ?? null;
if ($json_scrambles) {
    ?>
    <?=

    json_encode(json_decode($json_scrambles->json),
            JSON_PRETTY_PRINT +
            JSON_UNESCAPED_SLASHES +
            JSON_UNESCAPED_UNICODE);
    ?>
<?php } ?>