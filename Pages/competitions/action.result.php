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

$pdf = new PDF_Dash('P', 'mm');

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
    $yCompetitor = 6;

    $max_page = 44;

    $pdf->SetFont('courier');

    $pages = ceil(sizeof($competitors) / $max_page);

    $xStart = 5;
    $xEnd = $pdf->GetPageWidth() - 5;

    for ($p = 0; $p < $pages; $p++) {
        $start = $p * $max_page;
        $end = min(array(($p + 1) * $max_page, sizeof($competitors)));
        $on_page = ($end - $start + 1);
        $pdf->AddPage();
        $pdf->SetFont('msserif', '', 10);

        $n = 0;
        for ($c = $start; $c < $end; $c++) {
            $competitor = $competitors[$c];
            $n++;

            if ($c % 2 == 0) {
                #   $pdf->SetFillColor(240, 240, 240);
                #   $pdf->Rect(5, 38 + ($n - 1) * 8, $pdf->GetPageWidth() - 10, 8, "F");
            }
            #$pdf->Line(18, 23 + $n * 8, $pdf->GetPageWidth() - 5, 23 + $n * 8);
            #$pdf->SetFont('Arial', '', 12);
            $place = $competitor->place;
            if ($competitor->podium) {
                #$pdf->SetFont('msserif', 'U', 10);
                $pdf->Text(12, 20 + $n * $yCompetitor, '*');
            }
            if ($competitor->next_round) {
                #$pdf->SetFont('msserif', 'U', 10);
                $pdf->Text(12, 20 + $n * $yCompetitor, '>');
            }
            $pdf->Text(7, 20 + $n * $yCompetitor, $place);
            $pdf->SetFont('msserif', '', 10);
            $dX = 1;
            if ($event->format == 'average') {
                #$pdf->SetFont('msserif', 'B', 10);

                $pdf->Text($xEnd - $dX * $xAttempt, 20 + $n * $yCompetitor, attempt($competitor->best));
                $pdf->Text($xEnd - $dX * $xAttempt, 20 - 1 + $n * $yCompetitor, '.');
                $dX++;
                #$pdf->SetFont('msserif', '', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 20 + $n * $yCompetitor, attempt($competitor->average));
                $dX++;
            } elseif ($event->format == 'mean') {
                #$pdf->SetFont('msserif', 'B', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 20 + $n * $yCompetitor, attempt($competitor->best));
                $pdf->Text($xEnd - $dX * $xAttempt, 20 - 1 + $n * $yCompetitor, '.');
                $dX++;
                #$pdf->SetFont('msserif', '', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 20 + $n * $yCompetitor, attempt(str_replace("-cutoff", "", $competitor->mean)));
                $dX++;
            } else {
                #$pdf->SetFont('msserif', 'B', 10);
                $pdf->Text($xEnd - $dX * $xAttempt, 20 + $n * $yCompetitor, attempt($competitor->best));
                $dX++;
            }
            #$pdf->SetFont('msserif', '', 10);
            #$pdf->SetFont('Arial', '', 10);
            for ($i = $event->attempts; $i > 0; $i--) {

                $pdf->Text($xEnd - $dX * $xAttempt, 20 + $n * $yCompetitor, attempt($competitor->{"attempt$i"}));
                if ($i > 1)
                    $pdf->Text($xEnd - $dX * $xAttempt, 20 - 1 + $n * $yCompetitor, '.');
                $dX++;
            }


            $pdf->SetLineWidth(0.4);
            $pdf->SetDash(0.1, 5);
            if ($n > 0) {
                $pdf->Line(30,
                        21.5 + ($n - 0.4) * $yCompetitor,
                        $xEnd - ($dX - 1) * $xAttempt,
                        21.5 + ($n - 0.4) * $yCompetitor);
            }

            #            $pdf->SetFont('msserif', '', 12);
            $lat = iconv('utf-8', 'windows-1251', $competitor->name);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetDash(0);
            $width = $pdf->GetStringWidth($lat);
            $pdf->Rect(18, 20 + ($n - 0.6) * $yCompetitor, $width + 5, 8, 'F');
            $pdf->Text(18, 20 + $n * $yCompetitor, $lat);
            $pdf->SetDash(0.1, 5);
        }


#        $pdf->SetFont('msserif', '', 12);
        $even_name = iconv('utf-8', 'windows-1251', $event->name);
        $comp_name = iconv('utf-8', 'windows-1251', $comp->name);
        $round = $rounds_dict[$event->final ? 0 : $event->round]->fullName;
        $pdf->Text(5, 13, "$even_name, $round. $comp_name");

        #       $pdf->SetFont('Arial', '', 20);
        //$pdf->SetLineWidth(0.3);
        #$pdf->Line(5, 38, $pdf->GetPageWidth() - 5, 38);
        //$pdf->SetLineWidth(0.1);
        #      $pdf->SetFont('Arial', '', 10);
        $pdf->Text(6, 20, 'Place');
        # $pdf->Line(15, 30, 15, 32 + 8 * $on_page);
        $pdf->Text(18, 20, 'Competitor');

        $dX = 1;

        if ($event->format == 'mean') {
            #         $pdf->SetFont('Arial', '', 10);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 20, 'Best');
            $dX++;
            #        $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 20, 'Mean');
            $dX++;
        } elseif ($event->format == 'average') {
            #       $pdf->SetFont('Arial', '', 10);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 20, 'Best');
            $dX++;
            #      $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 20, 'Avg');
            $dX++;
        } else {
            #     $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt + 5, 20, 'Best');
            $dX++;
        }
        $pdf->SetFont('Arial', '', 10);
        for ($i = $event->attempts; $i > 0; $i--) {
            $pdf->Text($xEnd - $dX * $xAttempt, 20, sprintf('%0 9s', $i));
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

function attempt($attempt) {
    $attempt = str_replace(['DNS', '(', ')'], "", $attempt);
    $attempt = str_replace(['DNF'], "X", $attempt);
    return sprintf('%0 10s', $attempt);
}
