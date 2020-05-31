<?php

function getPersonRecords($wcaid) {
    $data = GetValue('persons_' . $wcaid, true);
    if (!$data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/persons/" . $wcaid);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $personal_records = false;
        if ($status == 200) {
            SaveValue('persons_' . $wcaid, $data);
            $personal_records = json_decode($data)->personal_records;
        }
    } else {
        $personal_records = json_decode($data)->personal_records;
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
