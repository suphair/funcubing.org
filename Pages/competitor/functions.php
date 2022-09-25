<?php

namespace competitor;

function actual($competitor) {



    $name = trim(explode("(", $competitor->name ?? FALSE)[0]);
    $wcaid = $competitor->wca_id ?? FALSE;
    $wid = $competitor->id ?? 0;

    $wid = is_numeric($wid) ? $wid : 0;

    $country = $competitor->country_iso2 ?? FALSE;
    if (!$wcaid and!$wid) {
        return;
    }
    $row_wcaid = $wcaid ? \db::row("SELECT * FROM dict_competitors WHERE wcaid = '$wcaid' ") : FALSE;
    $row_wid = $wid ? \db::row("SELECT * FROM dict_competitors WHERE wid = '$wid'") : FALSE;


    if (!$row_wcaid and!$row_wid) {
        \db::exec("INSERT INTO dict_competitors (wcaid, wid, country, name) VALUES ('$wcaid', $wid, '$country', '$name')");
    }

    if ($row_wcaid and!$row_wid) {
        if ($wid) {
            \db::exec("UPDATE dict_competitors SET name = '$name', wid = $wid, wcaid = '$wcaid', country = '$country' WHERE wcaid = '{$row_wcaid->wcaid}'");
        } else {
            \db::exec("UPDATE dict_competitors SET name = '$name', wcaid = '$wcaid', country = '$country' WHERE wcaid = '{$row_wcaid->wcaid}'");
        }
    }

    if (!$row_wcaid and $row_wid) {
        \db::exec("UPDATE dict_competitors SET name = '$name', wid = $wid,wcaid = '$wcaid', country = '$country' WHERE wid = '{$row_wid->wid}'");
    }

    if ($row_wcaid and $row_wid) {
        if ($row_wcaid->wid != $row_wid->wid or $row_wcaid->wcaid != $row_wid->wcaid) {
            \db::exec("DELETE FROM dict_competitors WHERE wcaid = '{$row_wcaid->wcaid}'");
        }
        \db::exec("UPDATE dict_competitors SET name = '$name', wid = $wid, wcaid = '$wcaid', country = '$country' WHERE wid = '{$row_wid->wid}'");
    }

    $nameRU = trim(str_replace([$name, '(', ')'], '', $competitor->name));
    if ($country == 'RU' and $nameRU) {
        if ($row_wid) {
            \db::exec("UPDATE dict_competitors SET nameRU = '$nameRU' WHERE wid = '{$row_wid->wid}'");
        }
        if ($row_wcaid) {
            \db::exec("UPDATE dict_competitors SET nameRU = '$nameRU' WHERE wcaid = '{$row_wcaid->wcaid}'");
        }
    }
}

function login($competitor) {
    $name = trim(explode("(", $competitor->name)[0]);
    $wca_id = $competitor->wca_id;
    $login_id = $competitor->id + 0;
    $country = $competitor->country_iso2;
    $name_ru = $country == 'RU' ? trim(str_replace(")", "", explode("(", $competitor->name)[1] ?? false)) : null;
    $email = $competitor->email;

    $where = "(login_id = $login_id " . ($wca_id ? "or wca_id = '$wca_id'" : '') . ") and new_id is null";
    $exists = \db::rows("select * from competitors where $where");

    $fc_id = null;
    if (sizeof($exists)) {
        $max = \db::row("SELECT max(name_ru) name_ru, max(fc_id) fc_id FROM competitors where $where");
        if (!$name_ru) {
            $name_ru = $max->name_ru;
        }
        $fc_id = $max->fc_id;
    }

    $insert = false;
    if (sizeof($exists) != 1) {
        $insert = true;
    } else {
        $update_id = $exists[0]->id;
    }
    if (sizeof($exists) == 1) {
        if ($exists[0]->login_id != $login_id
                or $exists[0]->wca_id != $wca_id
                or $exists[0]->name_ru != $name_ru
                or $exists[0]->name != $name
                or $exists[0]->country != $country
                or $exists[0]->email != $email) {
            $insert = true;
        }
    }

    if ($insert) {
        \db::exec_null("INSERT INTO competitors 
            (name, name_ru, login_id, wca_id, fc_id, country, email) 
            VALUES 
            ('$name', '$name_ru', $login_id, '$wca_id', '$fc_id', '$country', '$email') ");
        $insert_id = \db::id();
        \db::exec("UPDATE competitors set new_id = $insert_id where $where and id!= $insert_id");
    } else {

        \db::exec_null("UPDATE competitors set
            name = '$name', name_ru = '$name_ru', login_id = $login_id, wca_id = '$wca_id', country = '$country', email = '$email'
            where id = $update_id ");
    }
}
