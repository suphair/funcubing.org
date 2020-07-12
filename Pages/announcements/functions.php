<?php

namespace announcements;

function cron() {
    $_details['message'] = 0;
    $_details['subscribe'] = 0;

    $competitions = \wcaapi::getCompetitionsUpcoming(__FILE__ . ': ' . __FUNCTION__, false);
    $countries = [];
    
    foreach ($competitions as $competition) {
        if (!isset($countries[$competition->country_iso2])) {
            $countries[$competition->country_iso2] = [];
        }
        $countries[$competition->country_iso2][] = $competition;
    }

    $announcements = \db::rows("SELECT * FROM announcements WHERE Status = 1");
    $_details['subscribe'] = sizeof($announcements);
    foreach ($announcements as $announcement) {
        echo "<hr>";
        echo $announcement->email;
        $results = [];
        foreach (json_decode($announcement->countries) as $country) {
            if (!isset($countries[$country])) {
                $countries[$country] = [];
            }
            $results = array_merge($results, $countries[$country]);
        }

        usort($results, function($a, $b) {
            return strcmp($a->announced_at, $b->announced_at);
        });

        $message = '';
        $announced_at = date("Y-m-d H:i:s", strtotime($announcement->timestamp));
        echo $announced_at;
        if (is_array($results) and sizeof($results)) {
            ob_start();
            foreach ($results as $result)
                if (strtotime($result->announced_at) > strtotime($announced_at)) {
                    ?>
                    <p> 
                        <a href="<?= $result->url ?>"><?= $result->name ?></a> &#9642; 
                        <?= dateRange($result->start_date, $result->end_date) ?> &#9642;
                        <?= countyName($result->country_iso2) ?>, <?= $result->city ?>
                        <br> events:
                        <?php
                        $events = [];
                        foreach ($result->event_ids as $event_id) {
                            $events[] = $event_id;
                        }
                        ?>
                        <?= implode(", ", $events); ?>;
                        <br> delegates:
                        <?php
                        $delegates = [];
                        foreach ($result->delegates as $delegate) {
                            $delegates[] = explode(" (",$delegate->name)[0];
                        }
                        ?>
                        <?= implode(", ", $delegates); ?>;
                        <br> organizers:
                        <?php
                        $organizers = [];
                        foreach ($result->organizers as $organizer) {
                            $organizers[] = explode(" (",$organizer->name)[0];
                        }
                        ?>
                        <?= implode(", ", $organizers); ?>;
                        <br>
                        announced_at <?= $result->announced_at ?>
                    </p>
                    <?php
                }
            $message = ob_get_contents();
            ob_end_clean();
        }
        if ($message) {
            $_details['message'] ++;

            $subject = "FunCubing: New competitions announce";
            $message .= "<hr> Your email: " . $announcement->email . "; Tracked countries: " . $announcement->countries;
            $message .= "<br><a href='http://" . Pageindex() . "announcements'>Subscription management</a>";

            if (\config::isLocalhost()) {
                echo "<br><b>$subject</b><br>";
                echo $message;
            }

            if (sendMail($announcement->email, $subject, $message) === true) {
                \db::exec("UPDATE announcements"
                        . " SET timestamp = NOW() "
                        . " WHERE ID = " . $announcement->id);
            }
        }
    }
    return json_encode($_details);
}
