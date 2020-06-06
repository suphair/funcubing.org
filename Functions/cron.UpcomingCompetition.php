<?php

function UpcomingCompetition() {

    $_details = [];
    $_details['message'] = 0;
    $_details['subscribe'] = 0;


    $resultsApi = Suphair \ Wca \ Api ::getCompetitionsUpcoming('cron.UpcomingCompetition');

    $upcomingCountry = [];
    foreach ($resultsApi as $result) {
        if (!isset($upcomingCountry[$result['country_iso2']])) {
            $upcomingCountry[$result['country_iso2']] = [];
        }
        $upcomingCountry[$result['country_iso2']][] = $result;
    }

    DataBaseClass::Query("Select * from MailUpcomingCompetitions where Status=1");
    $MailUpcomingCompetitions = DataBaseClass::getRows();
    $_details['subscribe'] = sizeof($MailUpcomingCompetitions);
    foreach ($MailUpcomingCompetitions as $mailUpcomingCompetition) {
        echo "<hr>";
        echo $mailUpcomingCompetition['Email'];
        $results = [];
        foreach (explode(',', $mailUpcomingCompetition['Country']) as $country) {
            if (!isset($upcomingCountry[$country])) {
                $upcomingCountry[$country] = [];
            }
            $results = array_merge($results, $upcomingCountry[$country]);
        }

        usort($results, function($a, $b) {
            return strcmp($a['announced_at'], $b['announced_at']);
        });

        $message = '';
        $announced_at = date("Y-m-d H:i:s", strtotime($mailUpcomingCompetition['announced_at']));
        echo $announced_at;
        if (is_array($results) and sizeof($results)) {
            ob_start();
            foreach ($results as $result)
                if (strtotime($result['announced_at']) > strtotime($announced_at)) {
                    ?>
                    <p> 
                        <a href="<?= $result['url'] ?>"><?= $result['name'] ?></a> &#9642; 
                        <?= date_range($result['start_date'], $result['end_date']) ?> &#9642;
                        <?= CountryName($result['country_iso2']) ?>, <?= $result['city'] ?>
                        <br> events:
                        <?php
                        $events = [];
                        foreach ($result['event_ids'] as $event_id) {
                            $events[] = $event_id;
                        }
                        ?>
                        <?= implode(", ", $events); ?>;
                        <br> delegates:
                        <?php
                        $delegates = [];
                        foreach ($result['delegates'] as $delegate) {
                            $delegates[] = short_Name($delegate['name']);
                        }
                        ?>
                        <?= implode(", ", $delegates); ?>;
                        <br> organizers:
                        <?php
                        $organizers = [];
                        foreach ($result['organizers'] as $organizer) {
                            $organizers[] = short_Name($organizer['name']);
                        }
                        ?>
                        <?= implode(", ", $organizers); ?>;
                        <br>
                        announced_at <?= $result['announced_at'] ?>
                    </p>
                    <?php
                }
            $message = ob_get_contents();
            ob_end_clean();
        }
        if ($message) {
            $_details['message'] ++;

            $subject = "FunCubing: New competitions announce";
            $message .= "<hr> Your email: " . $mailUpcomingCompetition['Email'] . "; Tracked countries: " . CountryNames($mailUpcomingCompetition['Country']);
            $message .= "<br><a href='http://" . Pageindex() . "MailUpcomingCompetition'>Subscription management</a>";

            if (strpos($_SERVER['PHP_SELF'], '/' . GetIni('LOCAL', 'PageBase') . '/') !== false) {
                echo "<br><b>$subject</b><br>";
                echo $message;
            }

            if (SendMail($mailUpcomingCompetition['Email'], $subject, $message) === true) {
                DataBaseClass::Query("Update MailUpcomingCompetitions set announced_at=NOW() where ID=" . $mailUpcomingCompetition['ID']);
            }
        }
    }

    return json_encode($_details);
}
