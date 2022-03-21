<?php

$session = db::escape(session_id());
$competitor = db::escape(filter_input(INPUT_POST, 'competitor', FILTER_VALIDATE_INT));
if ($competitor and $session) {
    db::exec("DELETE IGNORE unofficial_competitors_round FROM"
            . " unofficial_competitors_round "
            . " JOIN unofficial_competitors on unofficial_competitors.id = unofficial_competitors_round.competitor"
            . " WHERE unofficial_competitors.id = $competitor AND unofficial_competitors.session = '$session' ");

    db::exec("DELETE IGNORE FROM unofficial_competitors WHERE id = $competitor AND session = '$session' ");
}