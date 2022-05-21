<?php

$code = db::escape(filter_input(INPUT_POST, 'code'));
$textEN = db::escape(filter_input(INPUT_POST, 'textEN'));
$textRU = db::escape(filter_input(INPUT_POST, 'textRU'));

db::exec("update unofficial_text set is_archive = 1 where code in('{$code}RU','{$code}EN') ");
db::exec("insert into unofficial_text (code,text) values ('{$code}EN','$textEN')");
db::exec("insert into unofficial_text (code,text) values ('{$code}RU','$textRU')");
