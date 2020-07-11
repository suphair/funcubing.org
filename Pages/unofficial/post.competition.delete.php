<?php

db::exec("DELETE FROM `unofficial_competitions` WHERE ID = $comp->id");

if (db::affected()) {
    header('Location: ' . PageIndex() . "/unofficial");
    exit();
}
