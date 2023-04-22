<?php

namespace api;

function sheets($competition_id = false) {

    $sheets = \db::rows("SELECT 
                            cs.content,
                            cs.title,
                            cs.sheet,
                            c.details
                        FROM unofficial_competitions c
                        LEFT OUTER JOIN unofficial_competition_sheets cs on c.id = cs.competition_id
                        WHERE lower('$competition_id') in (lower(c.secret), lower(c.rankedID), '')
                        AND is_archive = 0");
    $sheets_key = [];
    if ($sheets[0]->details) {
        $sheets_key[] = (object) [
                    'code' => 'info',
                    'title' => 'Информация',
                    'content' => $sheets[0]->details
        ];
    }
    foreach ($sheets as $sheet) {
        $sheets_key[] = (object) [
                    'code' => $sheet->sheet,
                    'title' => $sheet->title,
                    'content' => $sheet->content
        ];
    }
    return $sheets_key;
}
