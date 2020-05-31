<?php

Function CheckAdmin() {
    if (isset($_SESSION['Competitor'])) {
        DataBaseClass::FromTable("Delegate", "WCA_ID='" . $_SESSION['Competitor']->wca_id . "' and Admin=1 and Status='Active'");
        $delegate = DataBaseClass::QueryGenerate(false);
        if (DataBaseClass::rowsCount()) {
            return true;
        }
    }
    return false;
}

Function GetCompetitorData() {
    if (isset($_SESSION['Competitor'])) {
        if (!isset($_SESSION['Competitor']->id)) {
            unset($_SESSION['Competitor']);
            return false;
        }

#        $_SESSION['Competitor']->wca_id = '2019MECK01';

        return $_SESSION['Competitor'];
    }
    return false;
}
