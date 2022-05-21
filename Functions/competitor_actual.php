<?php

function competitor_actual() {
    foreach (\db::rows("select * from unofficial_fc_wca") as $row) {
        $wcaid = $row->wcaid;
        $fcid = $row->FCID;
        $name = false;
        if ($wcaid) {
            $name = \db2::row("select name from Persons where id='$row->wcaid' order by subid desc")->name ?? false;
            $name = trim(explode('(', $name)[0]);
        }

        if (!$name) {
            $nameRU = \db::row("select * from unofficial_competitors where FCID = '$fcid'")->name ?? false;
            $name = transliterate($nameRU);
        }

        if ($name) {
            \db::exec("update unofficial_fc_wca set wca_name='$name' where FCID='$fcid'");
        }
    }
}
