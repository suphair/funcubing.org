<?php

function getPersonRecords($wcaid) {

    $data = Suphair \ Wca \ Api::
            getPerson(
                    $wcaid, 'getPersonRecords', [], false);

    if (isset($data->personal_records)) {
        $personal_records = $data->personal_records;
    } else {
        $personal_records = [];
    }

    $records = [];
    foreach ($personal_records as $event => $record) {
        if (isset($record->single->best)) {
            $records[$event]['single'] = $record->single->best;
        }
        if (isset($record->average->best)) {
            $records[$event]['average'] = $record->average->best;
        }
    }

    return $records;
}
