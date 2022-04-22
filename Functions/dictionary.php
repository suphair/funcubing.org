<?php

function countyName($str) {
    return db::row("Select name from dict_countries where iso2='$str'")->name ?? $str;
}
