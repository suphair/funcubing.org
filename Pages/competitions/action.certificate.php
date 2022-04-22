<?php

$competitors = [];
if (!isset($comp)) {
    $comp = unofficial\getCompetition($competitor->competition_secret);
    $competitors[$competitor->name] = unofficial\getResutsByCompetitor($competitor->id);
} else {
    foreach ($comp_data->competitors as $competitor) {
        $competitors[$competitor->name] = unofficial\getResutsByCompetitor($competitor->id);
    }
}
$RU = t(false, true);
$comp_api = api\competitions($comp->secret)[$comp->id];

$organisers = [];
$judges = [];
foreach ($comp_api->organizers as $organizer) {
    $organisers[] = trim(explode("(", $organizer->name)[0]);
}
foreach ($comp_api->judges ?? [] as $judge) {
    $judges[] = str_replace(' ', '&nbsp;', trim(explode("(", $judge->name)[0]));
}


$mpdf = new \Mpdf\Mpdf();
$stylesheet = '
               td {
                    border: 2px solid lightgray;
                    padding:3px;
                    font-size:18px;
               }
               table{
                    border-collapse:collapse;
               }
               thead td{
                    font-weight:bold;
               }';
$mpdf->WriteHTML($stylesheet, 1);
$competitors_count = count($competitors);
$r = 0;
foreach ($competitors as $competitor_name => $results) {
    $r++;
    $results_event = [];
    foreach ($results as $result) {
        $result_row = false;
        if ($result->best) {
            $result_row = ['value' => $result->best, 'format' => t('best', 'лучшая'), 'position' => $result->place];
        }
        if ($result->mean and!in_array($result->mean, ['DNF', 'dnf', '-cutoff'])) {
            $result_row = ['value' => $result->mean, 'format' => t('mean', 'среднее'), 'position' => $result->place];
        }
        if ($result->average and!in_array($result->average, ['DNF', 'dnf', '-cutoff'])) {
            $result_row = ['value' => $result->average, 'format' => t('average', 'среднее'), 'position' => $result->place];
        }
        if ($result_row and!isset($results_event[$result->event_name])) {
            $results_event[$result->event_name] = (object) $result_row;
        }
    }
    if (sizeof($results_event)) {
        $html = '<div style="padding:20px 20px 0px 20px"><span style="font-size:20px;">';
        if (sizeof($judges)) {
            if ($RU) {
                $html .= '<b>' . implode(" and ", $judges) . '</b>'
                        . ', от имени <span class="nobr"><b>Fun&nbsp;Cubing</b></span>, и ';
            } else {
                $html .= '<b>' . implode(" and ", $judges) . '</b>'
                        . ', on behalf of the <span class="nobr"><b>Fun&nbsp;Cubing</b></span>, and ';
            }
        }
        if (sizeof($organisers)) {
            if ($RU) {
                $html .= '<b>' . implode(" and ", $organisers) . '</b>'
                        . ', от имени команды организаторов, подтвержают что';
            } else {
                $html .= '<b>' . implode(" and ", $organisers) . '</b>'
                        . ', on behalf of the organization team, certify that';
            }
        }

        if ($RU) {
            $text = 'принимал участие в <b>' . str_replace(' ', '&nbsp;', $comp->name) . '</b>, получив
следующие результаты:';
        } else {
            $text = 'has participated in the <b>' . str_replace(' ', '&nbsp;', $comp->name) . '</b>, obtaining the
following results:';
        }

        $mpdf->WriteHTML($html . '</span></div><div style="text-align:center"><h1>' . $competitor_name . '</h1></div>'
                . '<div style="padding:10px 20px 0px 20px"><span style="font-size:20px;">'
                . $text . '</div>'
                . '<table style="width:100%; margin:10px 20px 0px 20px;">'
                . '<thead><tr><td width="300px">' . t('Event', 'Дисциплина') . '</td><td colspan=2 align="center">' . t('Result', 'Результат') . '</td><td width="30px">' . t('Position', 'Место') . '</td></tr></thead><tbody>');
        foreach ($results_event as $event_name => $result_event) {
            $mpdf->WriteHTML('<tr><td>' . $event_name . '</td><td align="center">' . $result_event->format . '</td><td align="right">' . $result_event->value . '</td><td align="center">' . $result_event->position . '</td></tr>');
        }

        $mpdf->WriteHTML('</tbody></table>');
        $mpdf->WriteHTML('<div style="padding:20px 20px 0px 20px; text-align:right"><span style="font-size:18px;">' . dateRange($comp->date, $comp->date_to) . '</span></div>');
        if ($competitors_count != $r) {
            $mpdf->AddPage();
        }
    }
}
if (sizeof($competitors) == 1) {
    $mpdf->Output($comp->name . '-' . array_keys($competitors)[0] . '-Certificate.pdf', 'I');
} else {
    $mpdf->Output($comp->name . '-Certificates.pdf', 'I');
}
exit();
