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
$comp_api = api\competitions($comp->secret)[$comp->id] ?? false;
if (!$comp_api) {
    exit();
}

$organisers = [];
$delegates = [];

foreach ($comp_api->delegates ?? [] as $delegate) {
    $delegate_name = str_replace(' ', '&nbsp;', trim(explode("(", $delegate->name)[0]));
    if (!in_array($delegate_name, $delegates)) {
        $delegates[] = $delegate_name;
    }
}

foreach ($comp_api->organizers as $organizer) {
    $organizer_name = str_replace(' ', '&nbsp;', trim(explode("(", $organizer->name)[0]));
    if (!in_array($organizer_name, $delegates) and!in_array($organizer_name, $organisers)) {
        $organisers[] = $organizer_name;
    }
}

$mpdf = new \Mpdf\Mpdf();

$stylesheet = '
               body {
                    background-image:url(' . PageIndex() . 'Pages/competitions/certificate.png); 
                    background-image-resize:4;
                    font-family: "Dejavu Sans"; 
               }
               td {
                    border: 2px solid white;
                    padding:3px;
                    font-size:16px;
               }
               table{
                    border-collapse:collapse;
               }
               table thead td{
                    background-color:rgb(48,48,48);
                    color: white;
               }
               table tbody td{
                    background-color:lightgray;
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
        if ($result->best and!in_array($result->best, ['DNF'])) {
            $result_row = ['value' => $result->best, 'format' => t('best', 'лучшая'), 'position' => $result->place];
        }
        if ($result->mean and!in_array($result->mean, ['DNF'])) {
            $result_row = ['value' => $result->mean, 'format' => t('mean', 'среднее'), 'position' => $result->place];
        }
        if ($result->average and!in_array($result->average, ['DNF'])) {
            $result_row = ['value' => $result->average, 'format' => t('average', 'среднее'), 'position' => $result->place];
        }
        if ($result_row and!isset($results_event[$result->event_name])) {
            $results_event[$result->event_name] = (object) $result_row;
        }
    }
    if (sizeof($results_event)) {
        $html = '<div style="padding:20px 30px 0px 30px"><span style="font-size:18px;">';
        if (sizeof($delegates)) {
            if ($RU) {
                $html .= '<b>' . implode(" и ", $delegates) . '</b>'
                        . ', от имени <span class="nobr"><b>Делегатов России</b></span>';
            } else {
                $html .= '<b>' . implode(" and ", $delegates) . '</b>'
                        . ', on behalf of the <span class="nobr"><b>Delegates of Russia</b></span>';
            }
        }
        if (sizeof($organisers) and sizeof($delegates)) {
            if ($RU) {
                $html .= ', и ';
            } else {
                $html .= ', and ';
            }
        }
        if (sizeof($organisers)) {
            if ($RU) {
                $html .= '<b>' . implode(" и ", $organisers) . '</b>'
                        . ', от имени команды организаторов';
            } else {
                $html .= '<b>' . implode(" and ", $organisers) . '</b>'
                        . ', on behalf of the organization team';
            }
        }

        if ($RU) {
            if ((sizeof($organisers) + sizeof($delegates)) > 1) {
                $html .= ', подтвержают что';
            } else {
                $html .= ', подтвержает что';
            }
        } else {
            $html .= ', certify that';
        }
        if ($RU) {
            $text = 'принимал(a) участие в <b>' . str_replace(' ', '&nbsp;', $comp->name) . '</b> и получил(а)
следующие результаты:';
        } else {
            $text = 'has participated in the <b>' . str_replace(' ', '&nbsp;', $comp->name) . '</b>, obtaining the
following results:';
        }

        $mpdf->WriteHTML($html . '</span></div><div style="text-align:center;padding:10px;"><span style="font-size:32px;font-weight:bold;">' . $competitor_name . '</span></div>'
                . '<div style="padding:10px 30px 0px 30px"><span style="font-size:18px;">'
                . $text . '</div>'
                . '<table style="width:100%; margin:10px 30px 0px 30px;">'
                . '<thead><tr><td width="300px">' . t('Event', 'Дисциплина') . '</td><td colspan=2 align="center">' . t('Result', 'Результат') . '</td><td width="30px">' . t('Position', 'Место') . '</td></tr></thead><tbody>');
        foreach ($results_event as $event_name => $result_event) {
            $mpdf->WriteHTML('<tr><td>' . $event_name . '</td><td align="center">' . $result_event->format . '</td><td align="right">' . $result_event->value . '</td><td align="center">' . $result_event->position . '</td></tr>');
        }

        $mpdf->WriteHTML('</tbody></table>');
        $mpdf->WriteHTML('<div style="padding:20px; text-align:center"><span style="font-size:18px;">' . ($comp_api->city ? "$comp_api->city, " : "") . dateRange($comp->date, $comp->date_to, true) . '</span></div>');

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
