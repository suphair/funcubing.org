<?php

$print = false;
$Competitor = GetCompetitorData();
if ($Competitor and isset($_GET['Secret']) and isset($_GET['Discipline']) and is_numeric($_GET['Discipline'])) {
    $Secret = DataBaseClass::Escape($_GET['Secret']);
    $Discipline = $_GET['Discipline'];


    DataBaseClass::FromTable("Meeting");
    DataBaseClass::Where("Secret='$Secret'");
    DataBaseClass::Join_current("MeetingDiscipline");
    DataBaseClass::Where("MD.ID=$Discipline");
    DataBaseClass::Join_current("MeetingFormat");
    DataBaseClass::Join("MeetingDiscipline", "MeetingDisciplineList");
    $meeting = DataBaseClass::QueryGenerate(false);

    if (is_array($meeting)) {
        $print = true;
    }
}
if (!$print) {
    echo 'Not found';
    exit();
}

DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
DataBaseClass::Where("MCD.Place is not null");
DataBaseClass::Join_current("MeetingCompetitor");
DataBaseClass::OrderClear("MeetingCompetitorDiscipline", "Place");

$results = DataBaseClass::QueryGenerate();


$xPlace = 10;
$xAttempt = 17;
$xCompetitor = 50;

$pdf = new FPDF('P', 'mm');
$max_page = 30;

$pdf->SetFont('courier');

$pages = ceil(sizeof($results) / $max_page);

$xStart = 5;
$xEnd = $pdf->w - 5;

for ($p = 0; $p < $pages; $p++) {
    $start = $p * $max_page;
    $end = min(array(($p + 1) * $max_page, sizeof($results)));
    $on_page = ($end - $start + 1);
    $pdf->AddPage();

    $pdf->SetLineWidth(1);
    //$pdf->Line($xStart, 35, $xEnd, 35);
    //$pdf->Line($xStart + $xPlace, 30, $xEnd - $xAttempt * $column_attempt_count, 30);

    $n = 0;
    for ($c = $start; $c < $end; $c++) {
        $result = $results[$c];
        $n++;

        if ($c % 2 == 0) {
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Rect(5, 38 + ($n - 1) * 8, $pdf->w - 10, 8, "F");
        }
        $pdf->SetLineWidth(0.3);
        if ($n > 0) {
            $pdf->Line(5, 38 + ($n - 1) * 8, $pdf->w - 5, 38 + ($n - 1) * 8);
        }
        $pdf->Line(5, 38 + $n * 8, $pdf->w - 5, 38 + $n * 8);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(7, 35 + $n * 8, $result['MeetingCompetitorDiscipline_Place']);

        $pdf->SetFont('msserif', '', 12);
        $lat = iconv('utf-8', 'windows-1251', $result['MeetingCompetitor_Name']);
        $pdf->Text(18, 35 + $n * 8, $lat);



        $dX = 1;

        if ($meeting['MeetingFormat_Format'] == 'Average') {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Best'])));
            $dX++;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Average'])));
            $dX++;
        } elseif ($meeting['MeetingFormat_Format'] == 'Mean') {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Best'])));
            $dX++;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Mean'])));
            $dX++;
        }else{
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNF", "", $result['MeetingCompetitorDiscipline_Best'])));
            $dX++;
        }



        $pdf->SetFont('Arial', '', 10);
        for ($i = $meeting['MeetingFormat_Attempts']; $i > 0; $i--) {
            $pdf->Text($xEnd - $dX * $xAttempt, 35 + $n * 8, sprintf('%0 10s', str_replace("DNS", "", $result['MeetingCompetitorDiscipline_Attempt' . $i])));
            $dX++;
        }
    }

    //if(file_exists("Image/Competition/".$data['Competition_WCA'].'.jpg')){
    //    $pdf->Image("Image/Competition/".$data['Competition_WCA'].'.jpg',5,5,25,25,'jpg');
    //}
    //$pdf->Image("Image/MeetingImage/".$meeting['MeetingDisciplineList_Name'].'.png',5,5,20,20,'png');
    //$pdf->Image("Image/UC_B.png",$pdf->w-25,5,20,20,'png');




    $pdf->SetFont('msserif', '', 18);
    $lat = iconv('utf-8', 'windows-1251', $result['Meeting_Name'] . ', ' . date('j F Y', strtotime($result['Meeting_Date'])));
    $pdf->Text(5, 23, $lat);

    if ($meeting['MeetingDiscipline_Name']) {
        $lat = iconv('utf-8', 'windows-1251', $meeting['MeetingDiscipline_Name']);
    } else {
        $lat = iconv('utf-8', 'windows-1251', $meeting['MeetingDisciplineList_Name']);
    }

            
    $pdf->Text(5, 13, $lat . ' / round ' . $meeting['MeetingDiscipline_Round']);
    $pdf->SetFont('Arial', '', 20);
    $pdf->SetLineWidth(0.3);
    $pdf->Line(5, 38, $pdf->w - 5, 38);

    $pdf->SetLineWidth(0.1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Text(6, 35, 'Place');
    $pdf->Line(15, 30, 15, 32 + 8 * $on_page);
    $pdf->Text(18, 35, 'Competitor');

    $dX = 1;

    if ($meeting['MeetingFormat_Format'] == 'Mean') {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
        $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Best');
        $dX++;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
        $pdf->Text($xEnd - $dX * $xAttempt + 5, 35, 'Mean');
        $dX++;
    } elseif ($meeting['MeetingFormat_Format'] == 'Average') {
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
    for ($i = $meeting['MeetingFormat_Attempts']; $i > 0; $i--) {
        $pdf->Text($xEnd - $dX * $xAttempt, 35, sprintf('%0 9s', $i));
        $pdf->Line($xEnd - $dX * $xAttempt, 30, $xEnd - $dX * $xAttempt, 32 + 8 * $on_page);
        $dX++;
    }


    //$pdf->SetFont('Arial','',10);
    //$pdf->Text(75, 286,GetIni('TEXT','print_meeting'));
}
$pdf->Output($meeting['Meeting_Name'] . '_Results_' . $meeting['MeetingDisciplineList_Name'] . '_' . $meeting['MeetingDiscipline_Round'] . ".pdf", 'I');
$pdf->Close();
exit();
