<?php

namespace competitor;

function actual($competitor) {

    $name = trim(explode("(", $competitor->name ?? FALSE)[0]);
    $wcaid = $competitor->wca_id ?? FALSE;
    $wid = $competitor->id ?? 0;
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
        \db::exec("UPDATE dict_competitors SET name = '$name', wid = $wid,wcaid = '$wcaid', country = '$country' WHERE wcaid = '{$row_wcaid->wcaid}'");
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
}
