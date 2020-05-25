<?php

$print = false;
if (isset($_GET['Secret'])) {
    $Secret = DataBaseClass::Escape($_GET['Secret']);



    DataBaseClass::FromTable("Meeting");
    DataBaseClass::Where("Secret='$Secret'");
    DataBaseClass::Join_current("MeetingCompetitor");
    if (isset($_GET['Competitor']) and is_numeric($_GET['Competitor'])) {
        DataBaseClass::Where("MC.ID=" . $_GET['Competitor']);
    }

    $meeting = DataBaseClass::QueryGenerate(false);

    if (is_array($meeting)) {
        $print = true;
    }
}
if (!$print) {
    echo 'Not found';
    exit();
}
DataBaseClass::Join("Meeting", "MeetingDiscipline");
DataBaseClass::Join_current("MeetingFormat");
DataBaseClass::Join("MeetingDiscipline", "MeetingDisciplineList");
DataBaseClass::Where("MDL.ID<100");
DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
DataBaseClass::Where("MCD.MeetingCompetitor=MC.ID");
DataBaseClass::Where("MCD.Place is not null");
DataBaseClass::OrderClear("MeetingCompetitor", "Name");
DataBaseClass::Order("MeetingDisciplineList", "ID");
DataBaseClass::Order("MeetingDiscipline", "Round");

$results = DataBaseClass::QueryGenerate();
$competitors_resuts = array();
$competitors = array();
foreach ($results as $result) {
    $competitors_resuts[$result['MeetingCompetitor_ID']][] = $result;
    $competitors[$result['MeetingCompetitor_ID']] = $result;
}

DataBaseClass::FromTable("MeetingDiscipline", "Meeting=" . $meeting['Meeting_ID']);
DataBaseClass::Join_current("MeetingDisciplineList");
$rounds = array();

foreach (DataBaseClass::QueryGenerate() as $row) {
    if (!isset($rounds[$row['MeetingDisciplineList_ID']])) {
        $rounds[$row['MeetingDisciplineList_ID']] = 0;
    }
    $rounds[$row['MeetingDisciplineList_ID']] ++;
}


$xPlace = 10;
$xAttempt = 17;
$xCompetitor = 50;

$pdf = new FPDF('P', 'mm');
$max_page = 30;

$pdf->SetFont('courier');

foreach ($competitors as $comp_id => $competitor) {

    $results = $competitors_resuts[$comp_id];
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
            if ($dis_prev != $result['MeetingDisciplineList_ID'] and $c > $start) {
                $n += 0.5;
                $n_ext += 0.5;
            }
            $dis_prev = $result['MeetingDisciplineList_ID'];

            $n++;


            $pdf->SetFillColor(240, 240, 240);
            $pdf->Rect(5, 38 + ($n - 1) * 8, $pdf->w - 10, 8, "F");

            $pdf->SetLineWidth(0.3);
            if ($n > 0) {
                $pdf->Line(5, 38 + ($n - 1) * 8, $pdf->w - 5, 38 + ($n - 1) * 8);
            }
            $pdf->Line(5, 38 + $n * 8, $pdf->w - 5, 38 + $n * 8);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Text(7, 35 + $n * 8, $result['MeetingCompetitorDiscipline_Place']);

            $pdf->SetFont('Arial', '', 12);

            //$pdf->Image("Image/MeetingImage/".$result['MeetingDisciplineList_Name'].".png",16, 31+$n*8,5,5,'png');

            if ($result['MeetingDiscipline_Round'] == $rounds[$result['MeetingDisciplineList_ID']]) {
                if ($rounds[$result['MeetingDisciplineList_ID']] > 1) {
                    $pdf->Text(22, 35 + $n * 8, $result['MeetingDisciplineList_Name'] . ' Final');
                } else {
                    $pdf->Text(22, 35 + $n * 8, $result['MeetingDisciplineList_Name']);
                }
            } else {
                if ($rounds[$result['MeetingDisciplineList_ID']] > 1) {
                    $pdf->Text(22, 35 + $n * 8, $result['MeetingDisciplineList_Name'] . ' Round ' . $result['MeetingDiscipline_Round']);
                } else {
                    $pdf->Text(22, 35 + $n * 8, $result['MeetingDisciplineList_Name']);
                }
            }



            $dX = 1;

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text(
                    $xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf(
                            '%0 10s', str_replace(
                                    "DNF", "", $result['MeetingCompetitorDiscipline_Best']
                            )
                    )
            );
            $dX++;
            $pdf->Text(
                    $xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf(
                            '%0 10s', str_replace(
                                    ["DNF", "-cutoff"], "", $result['MeetingCompetitorDiscipline_Average']
                            )
                    )
            );
            $pdf->Text(
                    $xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf(
                            '%0 10s', str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Mean']
                            )
                    )
            );
            $dX++;


            $pdf->SetFont('Arial', '', 10);
            for ($i = 5; $i > 0; $i--) {
                $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNS", "", $result['MeetingCompetitorDiscipline_Attempt' . $i])));
                $dX++;
            }
        }


        //$pdf->Image("Image/UC_B.png",5,5,20,20,'png');




        $pdf->SetFont('msserif', '', 18);
        $lat = iconv('utf-8', 'windows-1251', $result['Meeting_Name'] . ', ' . date('j F Y', strtotime($result['Meeting_Date'])));
        $pdf->Text(5, 23, $lat);

        $lat = iconv('utf-8', 'windows-1251', $competitor['MeetingCompetitor_Name']);
        $pdf->Text(5, 13, $lat);

        $pdf->SetFont('Arial', '', 20);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(5, 38, $pdf->w - 5, 38);

        $pdf->SetLineWidth(0.1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(6, 35, 'Place');
        $pdf->Line(15, 30, 15, 32 + 8 * ($on_page + $n_ext));
        $pdf->Text(22, 35, 'Discipline');

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


//            $pdf->SetFont('Arial','',10);
//            $pdf->Text(75, 286,GetIni('TEXT','print_meeting'));
    }
}
if (sizeof($competitors) > 1) {
    $pdf->Output('Results_' . $result['Meeting_Name'] . '.pdf', 'D');
} elseif (sizeof($competitors) > 0) {
    $pdf->Output('Results_' . $result['Meeting_Name'] . '_' . $competitor['MeetingCompetitor_Name'] . ".pdf", 'I');
}
$pdf->Close();
exit();
