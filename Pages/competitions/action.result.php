<?php

$event_code = request(3);
$round = request(4);
$event_key = "{$event_code}_{$round}";
$results = api\get_results($comp->secret, $event_key);
$event = api\get_event($comp->secret, $event_key);
if (!$event) {
    die("$event_key not found");
}
$result_type = $event->result_type;
$round_name = $event->round->name;
$attempts = $event->attempts;

$mpdf = new \Mpdf\Mpdf();

$stylesheet = '
               html,body {margin:0px;padding:0px;}
               * {
                font-family: Roboto, system-ui, sans-serif;
               td {
                    font-size:16px;
               }
               th {
                    font-size:12px;
                    font-weight:bold;
               }
               h3{
                    padding:0px;
                    margin:0px;
                    margin-bottom:10px;
               }
               table{
                    border-collapse:collapse;
                    width:100%;
               }
               table td{
                    padding:5px 10px 5px 10px;
                    white-space: nowrap;
                    border-bottom:1px solid lightgray;
               }
               table th{
                    padding-right:10px;
                    padding-left:10px;
                    white-space: nowrap;
                    border-bottom:1px solid lightgray;
               }
               table td.next_round{
                    background-color:lightgray;
                    border-bottom:1px solid white;
                    
               }';
$mpdf->SetTitle("$event_code - {$event->round->name}");
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->SetHTMLFooter('<div style="text-align: center">{PAGENO} ' . t('of', 'из') . ' {nbpg}</div>');
$comp_name = t(transliterate($comp->name), $comp->name);
$mpdf->WriteHTML("<h3>$comp_name &bull; $event->name &bull; $round_name</h3>");
$formats = array_unique(['average', 'best']);

$mpdf->WriteHTML('<table>');
$mpdf->WriteHTML('<thead><tr>');
$mpdf->WriteHTML('<th>#</th>');
$mpdf->WriteHTML('<th align="left" >' . t('Name', 'Имя') . '</th>');
for ($i = 1; $i <= $attempts; $i++) {
    $mpdf->WriteHTML("<th align='right'>$i</th>");
}
foreach ($formats as $f => $format) {
    if ($format == 'average') {
        $mpdf->WriteHTML('<th align="right">' . t(ucfirst($format), 'Среднее') . '</th>');
    } else {
        $mpdf->WriteHTML('<th align="right">' . t(ucfirst($format), 'Лучшая') . '</th>');
    }
}
$mpdf->WriteHTML('</tr></thead>');
$mpdf->WriteHTML('<tbody>');
foreach ($results as $result) {
    $mpdf->WriteHTML("<tr>");
    if ($result->next_round ?? false or
            ($event->round->this == $event->round->total and $result->pos <= 3)) {
        $mpdf->WriteHTML("<td class='next_round'>$result->pos</td>");
    } else {
        $mpdf->WriteHTML("<td>$result->pos</td>");
    }
    $mpdf->WriteHTML("<td>" . t(transliterate($result->name), $result->name) . "</td>");

    for ($i = 0; $i < $attempts; $i++) {
        $mpdf->WriteHTML("<td align='right'>" . attempt($result->attempts[$i], $result_type) . "</td>");
    }
    foreach ($formats as $f => $format) {
        if (!$f) {
            $mpdf->WriteHTML("<td align='right'><b>" . attempt($result->$format, $result_type) . "</b></td>");
        } else {
            $mpdf->WriteHTML("<td align='right'>" . attempt($result->$format, $result_type) . "</td>");
        }
    }
    $mpdf->WriteHTML("</tr>");
}
$mpdf->WriteHTML('</tbody>');
$mpdf->WriteHTML('</table>');
$mpdf->Output("Results_{$comp->secret}_$event_key.pdf", 'I');
exit();

function attempt($attempt, $result_type) {
    if ($attempt == -1) {
        return 'DNF';
    }
    if ($attempt == -2) {
        return 'DNS';
    }
    if ($attempt == 0) {
        return '';
    }
    if (in_array($result_type, ['amount_asc', 'amount_desc'])) {
        return $attempt;
    }
    $minute = floor($attempt / 100 / 60);
    $second = floor(($attempt - $minute * 60 * 100) / 100);
    $centisecond = $attempt - $minute * 60 * 100 - $second * 100;
    if ($minute) {
        return "$minute:" . sprintf("%02d", $second) . "." . sprintf("%02d", $centisecond);
    }
    if ($second) {
        return "$second." . sprintf("%02d", $centisecond);
    }
    return "0.$centisecond";
}
