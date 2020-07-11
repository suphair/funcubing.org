<?php

if ($competitor_id ?? FALSE and ! $competitor) {
    die("competitor [$competitor_id] not found");
}

if (!isset($comp)) {
    $comp = unofficial\getCompetition($competitor->competition_secret);
    $competitors[$competitor->id] = unofficial\getResutsByCompetitor($competitor->id);
} else {
    foreach ($comp_data->competitors as $competitor) {
        $competitors[$competitor->id] = unofficial\getResutsByCompetitor($competitor->id);
    }
}


$xPlace = 10;
$xAttempt = 17;
$xCompetitor = 50;

$pdf = new FPDF('P', 'mm');
$max_page = 30;
foreach ($competitors as $results) {
    $pdf->SetFont('courier');
    $pages = ceil(sizeof($results) / $max_page);
    $xStart = 5;
    $xEnd = $pdf->w - 5;
    for ($p = 0; $p < $pages; $p++) {
        $dis_prev = 0;
        $start = $p * $max_page;
        $end = min(array(($p + 1) * $max_page, sizeof($results)));
        $on_page = ($end - $start + 1);
        $pdf->AddPage();

        $pdf->SetLineWidth(1);
        //$pdf->Line($xStart, 35, $xEnd, 35);
        //$pdf->Line($xStart + $xPlace, 30, $xEnd - $xAttempt * $column_attempt_count, 30);

        $n = 0;
        $n_ext = 0;
        for ($c = $start; $c < $end; $c++) {
            $result = $results[$c];
            if ($dis_prev != $result->event_dict and $c > $start) {
                $n += 0.5;
                $n_ext += 0.5;
            }
            $dis_prev = $result->event_dict;

            $n++;


            $pdf->SetFillColor(240, 240, 240);
            $pdf->Rect(5, 38 + ($n - 1) * 8, $pdf->w - 10, 8, "F");

            $pdf->SetLineWidth(0.3);
            if ($n > 0) {
                $pdf->Line(5, 38 + ($n - 1) * 8, $pdf->w - 5, 38 + ($n - 1) * 8);
            }
            $pdf->Line(5, 38 + $n * 8, $pdf->w - 5, 38 + $n * 8);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Text(7, 35 + $n * 8, $result->place . ($result->podium ? '*' : ''));

            $pdf->SetFont('Arial', '', 12);

            $pdf->Text(28, 35 + $n * 8, $result->event_name);
            if ($result->final) {
                $pdf->Text(16, 35 + $n * 8, 'final');
            } else {
                $pdf->Text(16, 35 + $n * 8, $result->round_name);
            }

            $dX = 1;

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', $result->best));
            $dX++;
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace('-cutoff', '', $result->average)));
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', $result->mean));
            $dX++;


            $pdf->SetFont('Arial', '', 10);
            for ($i = 5; $i > 0; $i--) {
                if ($result->average != "-cutoff" or $i <= 3) {
                    $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', $result->{"attempt$i"}));
                }
                $dX++;
            }
        }


        //$pdf->Image("Image/UC_B.png",5,5,20,20,'png');




        $pdf->SetFont('msserif', '', 18);
        $lat = iconv('utf-8', 'windows-1251', $comp->name . ', ' . dateRange($comp->date));
        $pdf->Text(5, 23, $lat);

        $lat = iconv('utf-8', 'windows-1251', $result->competitor_name);
        $pdf->Text(5, 13, $lat);

        $pdf->SetFont('Arial', '', 20);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(5, 38, $pdf->w - 5, 38);

        $pdf->SetLineWidth(0.1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(6, 35, 'Place');
        $pdf->Line(15, 30, 15, 32 + 8 * ($on_page + $n_ext));
        $pdf->Text(16, 35, 'Round');
        $pdf->Line(27, 30, 27, 32 + 8 * ($on_page + $n_ext));
        $pdf->Text(28, 35, 'Event');


        $dX = 1;

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * ($on_page + $n_ext));
        $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Best');
        $dX++;
        $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * ($on_page + $n_ext));
        $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Avg');
        $dX++;

        $pdf->SetFont('Arial', '', 10);
        for ($i = 5; $i > 0; $i--) {
            $pdf->Text($xEnd - $dX * $xAttempt, 35, sprintf('%0 9s', $i));
            $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * ($on_page + $n_ext));
            $dX++;
        }
    }
}

if (sizeof($competitors) == 1) {
    $pdf->Output('competitor_certificate_' . $competitor->id . ".pdf", 'I');
}

if (sizeof($competitors) > 1) {
    $pdf->Output('competition_certificates_' . $comp->secret . ".pdf", 'D');
}

$pdf->Close();
