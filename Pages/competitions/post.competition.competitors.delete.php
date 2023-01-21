<?php

db::exec("DELETE IGNORE unofficial_competitors_round "
        . " FROM unofficial_competitors_round "
        . " JOIN unofficial_competitors "
        . " ON unofficial_competitors.id = unofficial_competitors_round.competitor"
        . " AND  unofficial_competitors.competition = $comp->id");


db::exec("DELETE IGNORE FROM unofficial_competitors "
        . " WHERE  unofficial_competitors.competition = $comp->id ");

/*PATCH*/
db::exec("DELETE from `unofficial_fc_wca`  "
        . " where `FCID` not in (select `unofficial_competitors`.`FCID` from `unofficial_competitors` where FCID is not null)");
