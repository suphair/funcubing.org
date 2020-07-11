<?php

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

foreach ($events as $event_round) {

    $competitors = unofficial\getCompetitorsByEventround($event_round->id);
    $event = unofficial\getEventByEventround($event_round->id);
    foreach ($competitors as $c => $competitor) {
        if (!$competitor->place) {
            unset($competitors[$c]);
        }
    }
    $competitors = array_values($competitors);

    $xPlace = 10;
    $xAttempt = 17;
    $xCompetitor = 50;

    $max_page = 30;

    $pdf->SetFont('courier');

    $pages = ceil(sizeof($competitors) / $max_page);

    $xStart = 5;
    $xEnd = $pdf->GetPageWidth() - 5;

    for ($p = 0; $p < $pages; $p++) {
        $start = $p * $max_page;
        $end = min(array(($p + 1) * $max_page, sizeof($competitors)));
        $on_page = ($end - $start + 1);
        $pdf->AddPage();

        $pdf->SetLineWidth(1);

        $n = 0;
        for ($c = $start; $c < $end; $c++) {
            $competitor = $competitors[$c];
            $n++;

            if ($c % 2 == 0) {
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Rect(5, 38 + ($n - 1) * 8, $pdf->GetPageWidth() - 10, 8, "F");
            }
            $pdf->SetLineWidth(0.3);
            if ($n > 0) {
                $pdf->Line(5, 38 + ($n - 1) * 8, $pdf->GetPageWidth() - 5, 38 + ($n - 1) * 8);
            }
            $pdf->Line(5, 38 + $n * 8, $pdf->GetPageWidth() - 5, 38 + $n * 8);

            $pdf->SetFont('Arial', 'B', 12);
            $place = $competitor->place;
            if ($competitor->podium) {
                $place .= '*';
            }
            if ($competitor->next_round) {
                $place .= '+';
            }
            $pdf->Text(7, 35 + $n * 8, $place);


            $pdf->SetFont('msserif', '', 12);
            $lat = iconv('utf-8', 'windows-1251', $competitor->name);
            $pdf->Text(18, 35 + $n * 8, $lat);



            $dX = 1;

            if ($event->format == 'average') {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $competitor->best)));
                $dX++;
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $competitor->average)));
                $dX++;
            } elseif ($event->format == 'mean') {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $competitor->best)));
                $dX++;
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $competitor->mean)));
                $dX++;
            } else {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $competitor->best)));
                $dX++;
            }



            $pdf->SetFont('Arial', '', 10);
            for ($i = $event->attempts; $i > 0; $i--) {
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNS", "", $competitor->{"attempt$i"})));
                $dX++;
            }
        }

        $pdf->SetFont('msserif', '', 18);
        $lat = iconv('utf-8', 'windows-1251', $comp->name . ', ' . date('j F Y', strtotime($comp->date)));
        $pdf->Text(5, 23, $lat);

        $lat = iconv('utf-8', 'windows-1251', $event->name);


        if ($event->final and $event->rounds > 1) {
            $pdf->Text(5, 13, $lat . ', final');
        }
        if (!$event->final) {
            $pdf->Text(5, 13, $lat . ', round ' . $event->round);
        }
        if ($event->final and $event->rounds == 1) {
            $pdf->Text(5, 13, $lat);
        }

        $pdf->SetFont('Arial', '', 20);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(5, 38, $pdf->GetPageWidth() - 5, 38);

        $pdf->SetLineWidth(0.1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(6, 35, 'Place');
        $pdf->Line(15, 30, 15, 32 + 8 * $on_page);
        $pdf->Text(18, 35, 'Competitor');

        $dX = 1;

        if ($event->format == 'mean') {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Best');
            $dX++;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Mean');
            $dX++;
        } elseif ($event->format == 'average') {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Best');
            $dX++;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Avg');
            $dX++;
        } else {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Best');
            $dX++;
        }
        $pdf->SetFont('Arial', '', 10);
        for ($i = $event->attempts; $i > 0; $i--) {
            $pdf->Text($xEnd - $dX * $xAttempt, 35, sprintf('%0 9s', $i));
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
            $dX++;
        }
    }
}
if ($event_code) {
    $pdf->Output("{$comp->name}_results_{$event_code}_{$event->round}.pdf", 'I');
} else {
    $pdf->Output("{$comp->name}_results.pdf", 'I');
}
$pdf->Close();

