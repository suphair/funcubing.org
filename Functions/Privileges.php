<?php

Function CheckAdmin() {
    if (isset($_SESSION['Competitor'])) {
        return $_SESSION['Competitor']->wca_id == Suphair \ Config :: get ('Admin','wcaid');
    }
    return false;
}

Function GetCompetitorData() {
    if (isset($_SESSION['Competitor'])) {
        if (!isset($_SESSION['Competitor']->id)) {
            unset($_SESSION['Competitor']);
            return false;
        }
        return $_SESSION['Competitor'];
    }
    return false;
}
