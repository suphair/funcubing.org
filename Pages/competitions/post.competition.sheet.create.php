<?php

$sheet = db::escape(filter_input(INPUT_POST, 'sheet'));
$title = db::escape(filter_input(INPUT_POST, 'title'));
$content = db::escape(filter_input(INPUT_POST, 'content'));
$competition_id = $comp->id;

if ($sheet AND $title AND!db::row("SELECT 1 "
                . " FROM unofficial_competition_sheets "
                . " WHERE competition_id = $competition_id "
                . " AND is_archive = 0"
                . " AND (sheet = '$sheet' OR title='$title') ")) {
    $row = db::row("SELECT max(`order`) AS `order` "
                    . " FROM unofficial_competition_sheets "
                    . " WHERE competition_id = $competition_id ");
    $order = $row ? ($row->order + 1) : 1;
    $user = wcaoauth::me()->wca_id;
    db::exec("INSERT INTO unofficial_competition_sheets(competition_id,sheet,title,content,user,`order`)"
            . " VALUES"
            . " ($competition_id,'$sheet','$title','$content','$user',$order)");
}


