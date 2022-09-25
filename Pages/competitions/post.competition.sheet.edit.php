<?php

$order = db::escape(filter_input(INPUT_POST, 'order'), FILTER_VALIDATE_INT);
$sheet = db::escape(filter_input(INPUT_POST, 'sheet'));
$title = db::escape(filter_input(INPUT_POST, 'title'));
$content = db::escape(filter_input(INPUT_POST, 'content'));
$competition_id = $comp->id;
if ($order) {
    $row = db::row("SELECT * "
                    . " FROM unofficial_competition_sheets "
                    . " WHERE competition_id = $competition_id "
                    . " AND `order` = $order "
                    . " AND is_archive = 0 "
                    . " order by version ");

    if ($sheet AND $title AND $row) {
        $user = wcaoauth::me()->wca_id;
        db::exec("UPDATE unofficial_competition_sheets SET is_archive = 1 WHERE id= $row->id");
        db::exec("INSERT INTO unofficial_competition_sheets(competition_id,sheet,title,content,user,`order`,version)"
                . " VALUES"
                . " ($competition_id,'$sheet','$title','$content','$user',$order, $row->version + 1)");
    }
}


