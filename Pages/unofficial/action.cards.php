<?php

$blank = filter_input(INPUT_GET, 'blank') ?? FALSE;
$event_code = request(3);
$round = request(4);

$events = [];
$event_dict = $comp_data->event_dict->by_code[$event_code]->id ?? FALSE;
$event = $comp_data->rounds[$event_dict][$round]->round->id ?? FALSE;
if ($comp_data->event_rounds[$event]->id ?? FALSE) {
    $events[] = $comp_data->event_rounds[$event];
} elseif ($event_code) {
    die("event [$event_code] with round [$round] not found");
} else {
    $events = $comp_data->event_rounds;
}

$pdf = new FPDF('P', 'mm');

foreach ($events as $event) {

    $points = array();
    $points[] = array(5, 5);
    $points[] = array($pdf->GetPageWidth() / 2 + 5, 5);
    $points[] = array(5, $pdf->GetPageHeight() / 2 + 5);
    $points[] = array($pdf->GetPageWidth() / 2 + 5, $pdf->GetPageHeight() / 2 + 5);
    $sizeX = $pdf->GetPageWidth() / 2 - 10;
    $sizeY = $pdf->GetPageHeight() / 2 - 10;

    if ($blank !== FALSE) {
        $list = 1;
        $competitors = [];
    } else {
        $competitors = unofficial\getCompetitorsByEventround($event->id);
        foreach ($competitors as $c => $competitor) {
            if ($competitor->place) {
                unset($competitors[$c]);
            }
        }
        $competitors = array_values($competitors);
        $list = ceil(max([sizeof($competitors), 1]) / 4);
    }
    for ($l = 0; $l < $list; $l++) {
        $pdf->AddPage();
        $pdf->SetLineWidth(0.5);
        $pdf->Line(5, $pdf->GetPageHeight() / 2, $pdf->GetPageWidth() - 5, $pdf->GetPageHeight() / 2);
        $pdf->Line($pdf->GetPageWidth() / 2, 5, $pdf->GetPageWidth() / 2, $pdf->GetPageHeight() - 5);
        foreach (range(0, 3) as $i) {
            $point = $points[$i];
            $competitor = $competitors[$i + $l * 4] ?? FALSE;

            $pdf->SetLineWidth(0.2);
            $pdf->SetFont('msserif', '', 14);
            $lat = iconv('utf-8', 'windows-1251', $comp->name);
            $pdf->Text($point[0] + 10, $point[1] + 10, $lat);

            $pdf->SetFont('msserif', '', 12);
            $lat = iconv('utf-8', 'windows-1251', $comp_data->events[$event->event_dict]->name);
            $pdf->Text($point[0] + 10, $point[1] + 5, $lat . ' / ' . 'round ' . $event->round);


            $pdf->SetFont('Arial', '', 10);

            $Ry = 20;
            $pdf->Rect($point[0] + 10, $point[1] + $Ry - 6, 85, 13);

            $pdf->SetFont('msserif', '', 16);
            if ($competitor) {
                $lat = iconv('utf-8', 'windows-1251', $comp_data->competitors[$competitor->id]->name);
                $pdf->Text($point[0] + 15, $point[1] + $Ry + 2, $lat);
            }

            $Ry += 12;
            if ($event->comment) {
                $pdf->SetFont('msserif', '', 10);
                $lat = iconv('utf-8', 'windows-1251', $event->comment);
                $pdf->Text($point[0] + 10, $point[1] + $Ry + 1, $lat);
                $Ry += 6;
            }

            $pdf->SetFont('Arial', '', 10);
            $pdf->Text($point[0] + 40, $point[1] + $Ry + 1, 'Result');
            $pdf->Text($point[0] + 67, $point[1] + $Ry + 1, 'Judge');
            $pdf->Text($point[0] + 83, $point[1] + $Ry + 1, 'Comp');
            $pdf->Text($point[0] + 15, $point[1] + $Ry + 1, 'Scr');

            $format = $formats_dict[$comp_data->events[$event->event_dict]->format_dict];
            foreach (range(1, $format->attempts) as $k) {
                $pdf->SetFont('Arial', '', 14);
                $pdf->Text($point[0], $point[1] + $Ry + 10 + ($k - 1) * 16, $k);
                $pdf->Rect($point[0] + 26, $point[1] + $Ry + 2 + ($k - 1) * 16, 37, 13);
                $pdf->Rect($point[0] + 64, $point[1] + $Ry + 2 + ($k - 1) * 16, 15, 13);
                $pdf->Rect($point[0] + 80, $point[1] + $Ry + 2 + ($k - 1) * 16, 15, 13);
                $pdf->Rect($point[0] + 10, $point[1] + $Ry + 2 + ($k - 1) * 16, 15, 13);

                if ($event->cutoff and (
                        ($k == 2 and $format->attempts == 5)or
                        ($k == 1 and $format->attempts == 3))) {
                    $pdf->SetFont('msserif', '', 8);
                    $lat = "$k attempt" . ($k > 1 ? 's' : '' ) . " to get < $event->cutoff";
                    $pdf->Text($point[0] + 26, $point[1] + $Ry + $k * 16 + 1.4, $lat);
                    $pdf->Line($point[0], $point[1] + $Ry + 0.8 + $k * 16, $point[0] + 25, $point[1] + $Ry + 0.8 + $k * 16);
                    $pdf->Line($point[0] + 63, $point[1] + $Ry + 0.8 + $k * 16, $point[0] + 99, $point[1] + $Ry + 0.8 + $k * 16);
                }
            }
            if ($event->time_limit) {
                $pdf->SetFont('msserif', '', 8);
                $lat = 'Time limit ' . $event->time_limit . ($event->cumulative ? ' in total' : '');
                $pdf->Text($point[0] + 26, $point[1] + $Ry + $k * 16 + 1.4, $lat);
            }
            $pdf->SetFont('Arial', '', 14);
            $pdf->Text($point[0] - 1, $point[1] + 40 + 5 * 16 + 10, "Ex");
            $pdf->Rect($point[0] + 26, $point[1] + 32 + 5 * 16 + 10, 37, 13);
            $pdf->Rect($point[0] + 64, $point[1] + 32 + 5 * 16 + 10, 15, 13);
            $pdf->Rect($point[0] + 80, $point[1] + 32 + 5 * 16 + 10, 15, 13);
            $pdf->Rect($point[0] + 10, $point[1] + 32 + 5 * 16 + 10, 15, 13);
        }
    }
}
if ($event_code) {
    $pdf->Output("{$comp->name}_cards_{$event_code}_{$event->round}.pdf", 'I');
} else {
    $pdf->Output("{$comp->name}_cards.pdf", 'I');
}
$pdf->Close();

