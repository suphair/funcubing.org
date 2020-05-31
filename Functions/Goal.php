<?php

function GoalRecordFormat($result, $event, $type) {
    if (!$result)
        return '';

    if ($event == '333mbf') {
        $S1 = substr($result, 0, 2);
        $S2 = substr($result, 2, 5);
        $S3 = substr($result, 7, 2);
        $missed = $S3 + 0;
        $solved = 99 - $S1 + $missed;
        $mins = floor($S2 / 60 % 60);
        $secs = floor($S2 % 60);
        $timeFormat = sprintf('%d:%02d', $mins, $secs);
        return ($solved - $missed) . '/' . $solved . ' ' . $timeFormat;
    }

    if ($event == '333fm') {
        if ($type == 'average') {
            return sprintf("%01.2f", $result / 100);
        } else {
            return $result;
        }
    }
    $result_str = '';
    $minute = 0;
    $result_in = $result;
    if ($result > 6000) {
        $minute = floor($result / 6000);
        $result_str .= $minute . ":";
        $result -= $minute * 6000;
    }

    $second = floor($result / 100);
    if ($result < 1000 and $minute) {
        $result_str .= "0" . $second;
    } else {
        $result_str .= $second;
    }

    $result_str .= ".";

    $milisecond = $result - $second * 100;
    if ($milisecond < 10) {
        $result_str .= "0" . $milisecond;
    } else {
        $result_str .= $milisecond;
    }
    return $result_str;
}

function GoalProgress($record, $event, $type, $goal) {
    if (!$record  or !$goal) {
        return '';
    }
    $goalInt = GoalResultToInt($goal);
    $recordInt = GoalResultToInt($record);
    if ($goalInt >= $recordInt)
        return '';
    return (round(($recordInt - $goalInt) / $recordInt * 100, 1)) . '%';
}

function GoalResultToInt($result) {
    if ($result == 'DNF' or $result == 'DNS')
        return 0;

    $result = str_replace([":", "."], "_", "0" . $result);
    $a = explode("_", $result);

    if (sizeof($a) == 3) {
        return ($a[0] * 60 * 100) + $a[1] * 100 + $a[2];
    }

    if (sizeof($a) == 2) {
        return $a[0] * 100 + $a[1];
    }

    if (sizeof($a) == 1) {
        return $result;
    }
    return 0;
}

function UpcomingCompetitionSort($a, $b) {
    return strtotime($a['start_date']) > strtotime($b['start_date']);
}

function GoalImageCreate($competition, $competitorWID) {

    DataBaseClass::FromTable("GoalDiscipline");
    $Disciplines = [];
    foreach (DataBaseClass::QueryGenerate() as $row) {
        $Disciplines[$row['GoalDiscipline_Code']] = $row['GoalDiscipline_Name'];
    }


    #-----------------
    $font = "arial.ttf";
    $font_b = "arial.ttf";
    $font_i = "arial.ttf";

    DataBaseClass::Query("Select Name from Competitor where WID=" . $competitorWID);
    $h1 = 'Goals of ' . Short_Name(DataBaseClass::getRow()['Name']);
    $h2 = 'on the competition';
    DataBaseClass::FromTable("GoalCompetition", "WCA='$competition'");
    $h3 = DataBaseClass::QueryGenerate(false)['GoalCompetition_Name'];
    $params_weith = [];
    $params_weith[] = GetParam(14, $font_b, $h1)['weith'];
    $params_weith[] = GetParam(14, $font_b, $h2)['weith'];
    $params_weith[] = GetParam(14, $font_b, $h3)['weith'];

    $logo = imagecreatefrompng("Logo/Logo_Color_GC.png");

    DataBaseClass::Query("Select G.* from Goal G join GoalDiscipline GD on GD.Code=G.Discipline where G.Competitor=$competitorWID and G.Competition='$competition'"
            . "order by GD.ID, G.Format desc");
    $goals = DataBaseClass::getRows();
    $Y = 134;

    $discipline = '';
    foreach ($goals as $goal) {
        if ($goal['Discipline'] != $discipline) {
            $Y += 32;
            $discipline = $goal['Discipline'];
        }
        $Y += 24;
    }
    $Y += 10;
    $Y += 10;
    $max_X = max(420, max($params_weith) + 100 + 10);
    $im = imagecreatetruecolor($max_X, $Y);
    $background_color = imagecolorallocate($im, 255, 255, 255);
    $text_color = imagecolorallocate($im, 0, 0, 255);
    $green = imagecolorallocate($im, 0, 182, 67);
    $blue = imagecolorallocate($im, 17, 31, 135);
    $black = imagecolorallocate($im, 0, 0, 0);
    $orange = imagecolorallocate($im, 255, 128, 0);
    $red = imagecolorallocate($im, 162, 0, 0);
    imagefill($im, 2, 2, $background_color);
    imagerectangle($im, 1, 1, $max_X - 2, $Y - 2, $red);
    imagerectangle($im, 3, 3, $max_X - 4, $Y - 4, $red);
    imagecopyresampled($im, $logo, 10, 10, 0, 0, 70, 70, imageSX($logo), imageSY($logo));


    $weith = GetParam(14, $font_b, $h1)['weith'];
    imagefttext($im, 14, 0, 100 + ($max_X - 100 - 20 - $weith) / 2, 32, $blue, $font_b, $h1);
    $weith = GetParam(14, $font_b, $h2)['weith'];
    imagefttext($im, 14, 0, 100 + ($max_X - 100 - 20 - $weith) / 2, 55, $blue, $font_b, $h2);
    $weith = GetParam(14, $font_b, $h3)['weith'];
    imagefttext($im, 14, 0, 100 + ($max_X - 100 - 20 - $weith) / 2, 78, $blue, $font_b, $h3);

    imageline($im, 3, 90, $max_X - 4, 90, $red);
    imageline($im, 3, 88, $max_X - 4, 88, $red);

    $Y = 114;
    imagefttext($im, 12, 0, 80, $Y, $black, $font, 'PB on WCA');

    $weith = GetParam(12, $font, 'Goals')['weith'];
    imagefttext($im, 12, 0, 315 - $weith, $Y, $blue, $font, 'Goals');
    $weith = GetParam(12, $font_b, 'Results')['weith'];
    imagefttext($im, 12, 0, 400 - $weith, $Y, $black, $font, 'Results');

    $Y += 20;
    $discipline = '';
    $resultLoad = false;
    foreach ($goals as $goal) {
        if ($goal['Result'] !== null) {
            $resultLoad = true;
        }
    }
    foreach ($goals as $goal) {
        if ($goal['Discipline'] != $discipline) {
            $Y += 8;
            $ImgIcon = imagecreatefrompng("Image/Goal/" . $Disciplines[$goal['Discipline']] . ".png");
            imagecopyresampled($im, $ImgIcon, 10, $Y - 25, 0, 0, 30, 30, 500, 500);

            imagefttext($im, 12, 0, 45, $Y, $red, $font, $Disciplines[$goal['Discipline']]);
            $Y += 24;
            $discipline = $goal['Discipline'];
        }
        imagefttext($im, 12, 0, 10, $Y, $blue, $font_i, str_replace(['average', 'single'], ['Average', 'Single'], $goal['Format']));

        $weith = GetParam(12, $font, $goal['Record'] ?: '-')['weith'];
        if ($goal['Progress'] > 0 and $goal['Record']) {
            imagefttext($im, 12, 0, 155 - $weith, $Y, $black, $font, $goal['Record'] ?: '-');
        } else {
            imagefttext($im, 12, 0, 155 - $weith, $Y, $black, $font_i, $goal['Record'] ?: '-');
        }

        if ($goal['Progress'] > 0) {
            imagefttext($im, 12, 0, 173, $Y, $blue, $font, $goal['Progress']);
        }

        $weith = GetParam(12, $font_b, $goal['Goal'])['weith'];
        imagefttext($im, 12, 0, 315 - $weith, $Y, $blue, $font_b, $goal['Goal']);

        $record_int = GoalResultToInt($goal['Record']);
        $goal_int = GoalResultToInt($goal['Goal']);
        $result_int = GoalResultToInt($goal['Result']);

        if ($resultLoad or $result_int) {
            if ($result_int > 0) {
                $weith = GetParam(12, $font_b, $goal['Result'] . ' X')['weith'];
                if ($result_int <= $goal_int) {
                    imagefttext($im, 12, 0, 400 - $weith, $Y, $green, $font_b, $goal['Result'] . ' V');
                } else {
                    if ($record_int > $result_int) {
                        imagefttext($im, 12, 0, 400 - $weith, $Y, $orange, $font_b, $goal['Result'] . ' X');
                    } else {
                        imagefttext($im, 12, 0, 400 - $weith, $Y, $red, $font_b, $goal['Result'] . ' X');
                    }
                }
            } elseif (in_array($goal['Result'], ['DNF', 'DNS'])) {
                $weith = GetParam(12, $font_b, $goal['Result'] . ' X')['weith'];
                imagefttext($im, 12, 0, 400 - $weith, $Y, $red, $font_b, $goal['Result'] . ' X');
            } else {
                $weith = GetParam(12, $font_b, 'X')['weith'];
                imagefttext($im, 12, 0, 400 - $weith, $Y, $red, $font_b, 'X');
            }
        } else {
            imagefttext($im, 12, 0, 340, $Y, $red, $font, 'X ');
            imagefttext($im, 12, 0, 357, $Y, $black, $font, 'or');
            imagefttext($im, 12, 0, 380, $Y, $green, $font, 'V');
            imagefttext($im, 12, 0, 398, $Y, $black, $font, '?');
        }
        $Y += 24;
    }
    $Y += 10;
    imagecopyresampled($im, $logo, 7, $Y - 17, 0, 0, 20, 20, imageSX($logo), imageSY($logo));
    imagefttext($im, 10, 0, 30, $Y, $red, $font, 'FunCubing.org - Competition goals');
    imagefttext($im, 10, 0, $max_X - 80, $Y, $blue, $font, date("d.m.Y"));

    $fileName = "Images/Goal/" . $competition . "_" . $competitorWID . "_" . md5("GOALS" . $competition . $competitorWID) . ".png";
    if (sizeof($goals)) {
        imagepng($im, $fileName);
    } else {
        if (file_exists($fileName)) {
            unlink($fileName);
        }
    }
    imageDestroy($im);

    #------------------
}

function ResultEnter($result, $event, $format) {
    if (!$result) {
        return '';
    }

    if ($result == -1) {
        return 'DNF';
    }

    if ($result == -2) {
        return 'DNS';
    }

    if ($event == '333fm' && $format == 'single') {
        return $result;
    }

    if ($event == '333fm' && $format == 'average') {
        return sprintf('%0.2f', $result / 100);
    }

    $minute = (int) floor($result / 60 / 100);
    $second = (int) floor(($result - $minute * 60 * 100) / 100);
    $milisecond = (int) ($result - $minute * 60 * 100 - $second * 100);
    //echo "$result $minute $second $milisecond <br>";

    if ($minute == 0 and $second == 0) {
        return sprintf('0.%02d', $milisecond);
    } else if ($minute == 0) {
        return sprintf('%d.%02d', $second, $milisecond);
    } else {
        return sprintf('%d:%02d.%02d', $minute, $second, $milisecond);
    }
}

function GoalEnter($result, $event, $format) {

    if (!$result or $result == -1) {
        return '';
    }

    if ($event == '333fm' && $format == 'single') {
        if (strlen($result) > 2) {
            $result = substr($result, 0, 2);
        }
        return $result;
    }

    if ($event == '333fm' && $format == 'average') {
        $result = '000' + $result;
        $result = substr($result, -4, 4);
        $result = substr($result, 0, 2) . '.' . substr($result, 2, 2);
        return $result;
    }

    $minute = 0;
    $second = 0;
    $milisecond = 0;

    if (strlen($result) === 1) {
        $result = '0.0' . $result;
    } else if (strlen($result) === 2) {
        $result = '0.' . $result;
    } else if (strlen($result) === 3) {
        $second = substr($result, 0, 1);
        $result = substr($result, 0, 1) . '.' . substr($result, 1, 2);
    } else if (strlen($result) === 4) {
        $second = substr($result, 0, 2);
        $result = $second . '.' . substr($result, 2, 2);
    } else if (strlen($result) === 5) {
        $second = substr($result, 1, 2);
        $minute = substr($result, 0, 1);
        $result = $minute . ':' . $second . '.' . substr($result, 3, 2);
    } else if (strlen($result) === 6) {
        $second = substr($result, 2, 2);
        $minute = substr($result, 0, 2);
        $milisecond = substr($result, 4, 2);
        if ($milisecond >= 50) {
            $second++;
        }
        if ($second == 60) {
            $second = 0;
            $minute++;
        }
        $result = substr('0' . $minute, -2, 2) . ':' . substr('0' + $second, -2, 2) . '.00';
    } else {
        $result = '';
    }
    return $result;
}

function CheckGoalGrand() {
    return ($Competitor = GetCompetitorData() and $Competitor->id == 6834);
}

function GoalUpdateCompetitions($wid) {
    $UpcomingCompetitions = arrayToObject(GetUpcomingCompetition($wid)['upcoming_competitions']);

    foreach ($UpcomingCompetitions as $competition) {


        GoalUpdateCompetition($competition);
    }
}

function GoalUpdateCompetition($competition) {

    $competition->name = DataBaseClass::Escape($competition->name);

    DataBaseClass::Query("Select * from GoalDiscipline order by ID");
    $events = [];
    foreach (DataBaseClass::getRows() as $event) {
        $events[] = $event['Code'];
    }
    $events2 = [];
    foreach($events as $event){
        if (in_array($event, objectToArray($competition->event_ids))) {
            $events2[] = $event;
        }
    }

    DataBaseClass::FromTable("GoalCompetition", "WCA='{$competition->id}'");
    if (!DataBaseClass::QueryGenerate(false)['GoalCompetition_ID']) {
        DataBaseClass::Query("insert into GoalCompetition "
                . "(WCA) values ('{$competition->id}')");
    }
    DataBaseClass::Query("Update GoalCompetition set "
            . " Name='{$competition->name}', "
            . " Country='{$competition->country_iso2}', "
            . " City='{$competition->city}', "
            . " DateStart='{$competition->start_date}', "
            . " DateEnd='{$competition->end_date}',"
            . " Events='" . json_encode($events2) . "',"
            . " TimeUpdate = now() "
            . " where WCA='{$competition->id}'");
}
