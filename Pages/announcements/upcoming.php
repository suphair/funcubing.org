<?php
$competitions = json_decode(db::row("SELECT value FROM wca_api_cash WHERE `key` LIKE '%competitions:start=%;sort=start_date;page=1;%' ORDER BY timestamp DESC LIMIT 1")->value ?? '[]');
$upcomings = [];
foreach ($competitions as $competition) {
    if (in_array($competition->country_iso2, $countries) and!$competition->cancelled_at) {
        $upcomings[] = $competition;
    }
}
if (sizeof($upcomings)) {
    usort($upcomings, function($a, $b) {
        return $a->start_date > $b->start_date;
    })
    ?>
    <div class='shadow2'>
        <h2>Upcoming competitions for your tracked countries</h2>
        <table class='table_new'>
            <thead>
                <tr>
                    <td>
                        Date
                    </td>
                    <td>
                        Name
                    </td>
                    <td>
                        Country
                    </td>
                <tr>
            </thead>
            <tbody>
                <?php foreach ($upcomings as $upcoming) { ?>
                    <tr>
                        <td>
                            <b><?= dateRange($upcoming->start_date, $upcoming->end_date) ?></b>
                        </td>
                        <td>
                            <span class='flag-icon flag-icon-<?= strtolower($upcoming->country_iso2) ?>'></span>
                            <?= $upcoming->name ?>
                        <td>
                            <b><?= countyName($upcoming->country_iso2) ?></b>,
                            <?= $upcoming->city ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>