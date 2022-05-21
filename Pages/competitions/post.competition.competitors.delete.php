<?php


db::exec("DELETE IGNORE unofficial_competitors_round "
        . " FROM unofficial_competitors_round "
        . " JOIN unofficial_competitors "
        . " ON unofficial_competitors.id = unofficial_competitors_round.competitor"
        . " AND  unofficial_competitors.competition = $comp->id");


db::exec("DELETE IGNORE FROM unofficial_competitors "
        . " WHERE  unofficial_competitors.competition = $comp->id ");
