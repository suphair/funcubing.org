<?php

function GoalsReload() {
    $_details = [];
    $_details['competitions']['all'] = 0;
    $_details['competitions']['update'] = 0;
    $_details['competitions']['delete'] = 0;
    $_details['results']['all'] = 0;
    $_details['results']['load'] = 0;

    DataBaseClass::Query("Select WCA from GoalCompetition ");
    $competitions = DataBaseClass::getRows();
    $_details['competitions']['all'] = sizeof($competitions);

    DataBaseClass::Query("Select WCA from GoalCompetition WHERE TimeUpdate < DATE_ADD(current_date(),INTERVAL - 4 HOUR) ");
    $competitions = DataBaseClass::getRows();
    foreach ($competitions as $competition) {
        $competition_wca = $competition['WCA'];
        echo $competition_wca . '<br>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/competitions/$competition_wca");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $competitorData = json_decode($data);
        if ($status == 200 and ! isset($competitorData->error)) {
            GoalUpdateCompetition($competitorData);
            $_details['competitions']['update'] ++;
        }

        if (isset($competitorData->error) and $competitorData->error == "Competition with id $competition_wca not found") {
            DataBaseClass::Query("DELETE FROM GoalCompetition where WCA='{$competition_wca}'");
            $_details['competitions']['delete'] ++;
        }
    }
    echo '<hr>';
    DataBaseClass::Query("Select distinct GC.WCA from GoalCompetition GC join Goal G on GC.WCA=G.Competition where GC.DateStart<current_date() and GC.DateEnd>DATE_ADD(current_date(),INTERVAL -4 Week)");
    $competitions = DataBaseClass::getRows();
    $_details['results']['all'] = sizeof($competitions);
    foreach ($competitions as $competition) {
        $competition_wca = $competition['WCA'];
        echo $competition_wca . '<br>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/competitions/$competition_wca/results");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $results = json_decode($data, true);
        if ($status == 200 and sizeof($results)) {
            $_details['results']['update'] ++;
            DataBaseClass::Query("Update GoalCompetition set Result=1 where WCA='$competition_wca'");

            $result = [];
            $result_best = ['best' => [], 'average' => []];
            foreach ($results as $result) {
                foreach (['best', 'average'] as $format) {
                    if (!isset($result_best[$format][$result['wca_id']][$result['event_id']])) {
                        $result_best[$format][$result['wca_id']][$result['event_id']] = $result[$format];
                    } elseif ($result_best[$format][$result['wca_id']][$result['event_id']] > $result[$format]
                            and $result[$format] != -1
                            and $result[$format] != -2
                    ) {
                        $result_best[$format][$result['wca_id']][$result['event_id']] = $result[$format];
                    }
                }
            }
            $result_best['single'] = $result_best['best'];
            DataBaseClass::Query("Select G.*,C.WCAID from Goal G "
                    . " join Competitor C on C.WID=G.Competitor "
                    . "where G.Competition='$competition_wca'");
            $Competitors = [];
            foreach (DataBaseClass::getRows() as $row) {
                if (isset($result_best[$row['Format']][$row['WCAID']][$row['Discipline']])) {
                    $result_in = ResultEnter($result_best[$row['Format']][$row['WCAID']][$row['Discipline']], $row['Discipline'], $row['Format']);
                    $Complete = (GoalResultToInt($result_in) <= GoalResultToInt($row['Goal'])) + 0;
                    if (!GoalResultToInt($result_in))
                        $Complete = 0;
                    DataBaseClass::Query("Update Goal set Result='$result_in',Complete=$Complete where Discipline='" . $row['Discipline'] . "' and Competitor=" . $row['Competitor'] . " and Competition='$competition_wca' and Format='" . $row['Format'] . "'");
                }
                $Competitors[$row['Competitor']] = 1;
            }
            DataBaseClass::Query("Update Goal set Result='' where Competition='$competition_wca' and Result is null");
        }
    }

    return json_encode($_details);
}
