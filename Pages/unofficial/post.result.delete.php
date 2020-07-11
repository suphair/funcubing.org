<?php
$competitor_round= db::escape(filter_input(INPUT_POST, 'competitor_round',FILTER_VALIDATE_INT));

if($competitor_round){
    db::exec("DELETE IGNORE unofficial_competitors_round "
            . " FROM unofficial_competitors_round"
            . " JOIN unofficial_events_rounds on unofficial_events_rounds.id = unofficial_competitors_round.round "
            . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
            . " WHERE unofficial_competitors_round.id = $competitor_round AND unofficial_events.competition = $comp->id ");
}
