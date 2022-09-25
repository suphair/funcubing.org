<?php

$order = db::escape(filter_input(INPUT_POST, 'order'), FILTER_VALIDATE_INT);
$competition_id = $comp->id;

if ($order) {
    $row = db::row("SELECT * "
                    . " FROM unofficial_competition_sheets "
                    . " WHERE competition_id = $competition_id "
                    . " AND `order` = $order "
                    . " AND is_archive = 0 "
                    . " order by version ");

    if ($row) {
        $user = wcaoauth::me()->wca_id;
        db::exec("UPDATE unofficial_competition_sheets SET is_archive = 1 WHERE id= $row->id");
        db::exec("INSERT INTO unofficial_competition_sheets(competition_id,sheet,title,content,user,`order`,version, is_archive)"
                . " VALUES"
                . " ($competition_id,'-','-','-','$user',$order, $row->version + 1,1)");
    }
}