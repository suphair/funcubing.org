<?php

AddLog('Master', 'Cron', 'Start');

$crons = DataBaseClass::getRowsObject("
    SELECT
        id,
        name,
        intervalMinutes
    FROM CronConfig
    WHERE nextRun < now()
        OR nextRun is null
    ORDER BY nextRun
   ");


foreach ($crons as $cron) {
    $cronFile = __DIR__ . "/{$cron->name}.php";

    $logId = AddLogCronStart($cron->id, $cron->name);

    DataBaseClass::Query("
        UPDATE CronConfig 
        SET 
            lastRun = now(),
            nextRun = 
            CASE 
                WHEN fixTime    
                    THEN str_to_date(
                        CONCAT(
                            DATE_ADD(CURDATE(),INTERVAL 1 DAY),
                            ' ',
                            fixTime
                        ),
                        '%Y-%m-%d %H:%i:%s'
                    )
                WHEN intervalMinutes 
                    THEN DATE_ADD(now(),INTERVAL intervalMinutes MINUTE)
            END    
        WHERE id = {$cron->id}
    ");

    $_details = [];
    if (file_exists($cronFile)) {
        include $cronFile;
    } else {
        $_details['error'] = 'not found';
    }
    if (!sizeof($_details)) {
        $_details[] = 'done';
    }
    AddLogCronEnd($logId, json_encode($_details));
}

DataBaseClass::close();
exit();
