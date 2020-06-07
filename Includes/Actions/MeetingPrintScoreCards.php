<?php

$print = false;
$Competitor = GetCompetitorData();
if ($Competitor and isset($_GET['Secret']) and isset($_GET['Discipline']) and is_numeric($_GET['Discipline'])) {
    $Secret = DataBaseClass::Escape($_GET['Secret']);

    DataBaseClass::Query("Select * from `Meeting` where Secret='$Secret'");
    $meeting = DataBaseClass::getRow();

    if (is_array($meeting) and ! CheckMeetingGrand() and ! CheckMeetingOrganizer($meeting['ID'])) {
        DataBaseClass::Query("Select * from `Meeting` where Competitor=" . $Competitor->id . " and Secret='$Secret'");
        $meeting = DataBaseClass::getRow();
    }

    if (is_array($meeting)) {
        $print = true;
    }
}
if (!$print) {
    echo 'Not found';
    exit();
}


DataBaseClass::FromTable("Meeting", "ID=" . $meeting['ID']);
DataBaseClass::Join("Meeting", "MeetingDiscipline");
if ($_GET['Discipline']) {
    DataBaseClass::Where("MeetingDiscipline", "ID=" . $_GET['Discipline']);
}
if (!$_GET['Discipline'] and ! isset($_GET['blank'])) {
    DataBaseClass::Where("MeetingDiscipline", "Round=1");
}
DataBaseClass::Join("MeetingDiscipline", "MeetingDisciplineList");
DataBaseClass::Join("MeetingDiscipline", "MeetingFormat");


$disciplines = array();
$disciplineFormats = array();
$disciplineComment = array();
foreach (DataBaseClass::QueryGenerate() as $discipline) {
    $disciplines[$discipline['MeetingDiscipline_Round']][] = array($discipline['MeetingDisciplineList_Name'], $discipline['MeetingDiscipline_Name']);
    $disciplineFormats[$discipline['MeetingDisciplineList_Name']] = $discipline['MeetingFormat_Attempts'];
    $disciplineComment[$discipline['MeetingDisciplineList_Name']] = $discipline['MeetingDiscipline_Comment'];
}


DataBaseClass::Join("Meeting", "MeetingCompetitor");
DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
DataBaseClass::Where("MCD.MeetingCompetitor=MC.ID");
DataBaseClass::OrderClear("MeetingCompetitor", "Name");

$cards = array();
foreach (DataBaseClass::QueryGenerate() as $row) {
    $cards[$row['MeetingDiscipline_Round']][$row['MeetingDisciplineList_Name']][] = $row['MeetingCompetitor_Name'];
}

@$pdf = new FPDF('P', 'mm');

foreach ($disciplines as $round => $round_disciplines) {
    foreach ($round_disciplines as $discipline_names) {

        $discipline = $discipline_names[0];

        $points = array();
        $points[] = array(5, 5);
        $points[] = array($pdf->GetPageWidth() / 2 + 5, 5);
        $points[] = array(5, $pdf->GetPageHeight() / 2 + 5);
        $points[] = array($pdf->GetPageWidth() / 2 + 5, $pdf->GetPageHeight() / 2 + 5);
        $sizeX = $pdf->GetPageWidth() / 2 - 10;
        $sizeY = $pdf->GetPageHeight() / 2 - 10;

        if (!isset($cards[$round][$discipline]) or isset($_GET['blank'])) {
            $list = 1;
            $competitors = array('');
        } else {
            $competitors = $cards[$round][$discipline];
            $list = ceil((sizeof($cards[$round][$discipline])) / 4);
        }


        for ($l = 0; $l < $list; $l++) {
            $pdf->AddPage();
            $pdf->SetLineWidth(0.5);
            $pdf->Line(5, $pdf->GetPageHeight() / 2, $pdf->GetPageWidth() - 5, $pdf->GetPageHeight() / 2);
            $pdf->Line($pdf->GetPageWidth() / 2, 5, $pdf->GetPageWidth() / 2, $pdf->GetPageHeight() - 5);
            for ($i = 0; $i < 4; $i++) {
                $point = $points[$i];

                if (isset($competitors[$i + $l * 4])) {
                    $competitor = $competitors[$i + $l * 4];
                } else {
                    $competitor = '';
                }

                $pdf->SetLineWidth(0.2);
                $pdf->SetFont('msserif', '', 14);
                $lat = iconv('utf-8', 'windows-1251', $meeting['Name']);
                $pdf->Text($point[0] + 10, $point[1] + 10, $lat);

                $pdf->SetFont('msserif', '', 12);
                if ($discipline_names[1]) {
                    $lat = iconv('utf-8', 'windows-1251', $discipline_names[1]);
                    $pdf->Text($point[0] + 10, $point[1] + 5, $lat . ' / ' . 'round ' . $round);
                } else {
                    $pdf->Text($point[0] + 10, $point[1] + 5, $discipline . ' ' . 'Round ' . $round);
                }

                $pdf->SetFont('Arial', '', 10);

                $Ry = 20;
                $pdf->Rect($point[0] + 10, $point[1] + $Ry - 6, 85, 13);

                $pdf->SetFont('msserif', '', 16);
                $lat = iconv('utf-8', 'windows-1251', $competitor);
                $pdf->Text($point[0] + 15, $point[1] + $Ry + 2, $lat);
                $Ry += 12;

                if ($disciplineComment[$discipline]) {
                    $pdf->SetFont('msserif', '', 10);
                    $lat = iconv('utf-8', 'windows-1251', $disciplineComment[$discipline]);
                    $pdf->Text($point[0] + 10, $point[1] + $Ry + 1, $lat);
                    $Ry += 6;
                }

                $pdf->SetFont('Arial', '', 10);
                $pdf->Text($point[0] + 35, $point[1] + $Ry + 1, 'Result');
                $pdf->Text($point[0] + 67, $point[1] + $Ry + 1, 'Judge');
                $pdf->Text($point[0] + 83, $point[1] + $Ry + 1, 'Comp');


                for ($k = 1; $k <= $disciplineFormats[$discipline]; $k++) {
                    $pdf->SetFont('Arial', '', 14);
                    $pdf->Text($point[0], $point[1] + $Ry + 10 + ($k - 1) * 16, $k);
                    $pdf->Rect($point[0] + 10, $point[1] + $Ry + 2 + ($k - 1) * 16, 53, 13);
                    $pdf->Rect($point[0] + 64, $point[1] + $Ry + 2 + ($k - 1) * 16, 15, 13);
                    $pdf->Rect($point[0] + 80, $point[1] + $Ry + 2 + ($k - 1) * 16, 15, 13);
                }

                $pdf->SetFont('Arial', '', 14);
                $pdf->Text($point[0] - 1, $point[1] + 40 + 5 * 17 + 5, "Ex");
                $pdf->Rect($point[0] + 10, $point[1] + 32 + 5 * 17 + 5, 53, 13);
                $pdf->Rect($point[0] + 64, $point[1] + 32 + 5 * 17 + 5, 15, 13);
                $pdf->Rect($point[0] + 80, $point[1] + 32 + 5 * 17 + 5, 15, 13);
            }
        }
    }
}
$pdf->Output($meeting['Name'] . '_ScoreCards_' . $_GET['Discipline'] . ".pdf", 'I');
$pdf->Close();
exit();
