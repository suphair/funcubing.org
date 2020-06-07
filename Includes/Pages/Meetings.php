<div class="shadow2" >
    <?php $Competitor = GetCompetitorData(); ?>
    <?php if (!$Competitor) { ?>    
        <?php $_SESSION['Refer'] = $_SERVER['REQUEST_URI']; ?>    
        <h3>
            <i class="error far fa-hand-paper"></i> 
            To create unofficial competition you need to sign in with WCA.
        </h3>
    <?php } else { ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script>
            $(function () {
                $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
            });
        </script>


        <form method="POST" action="<?= PageIndex() . "Actions/MeetingCreate" ?>">
            <b>Create unofficial competition</b> 
            <input required placeholder="RamenskoeMeeting #1" type="text" name="Name" value="" />

            <input style="width:140px" placeholder="Select date" required type="text" id="datepicker" name="Date">

            <button>
                <i class="fas fa-plus-circle"></i> 
                Create
            </button>
        </form>
        <i class="fas fa-info-circle"></i> Competitions is created privately.
        You can make them public later in the settings.
        Or leave them hidden for your testing or fun.
    <?php } ?>
</div>
<div class="shadow" >
    <?php $mine = ($Competitor and isset($_GET['Mine'])); ?>
    <h2>
        <?php if ($mine) { ?>
            My Unofficial Competitions
        <?php } else { ?>
            Public Unofficial Competitions
        <?php } ?>
    </h2>

    <?php if ($mine) { ?>
        <p>
            <i class="far fa-eye"></i>
            <a href="<?= PageIndex() ?>?Meetings">
                Show all
            </a>
        </p>
    <?php } elseif ($Competitor) { ?>
        <p>
            <i class="fas fa-crown"></i>
            <a href="<?= PageIndex() ?>?Meetings&Mine">

                Show only mine
            </a>
        </p>
    <?php } ?>


    <?php
    $sql = "SELECT
        Meeting.Website Meeting_Website,
        Meeting.ID Meeting_ID,
        Meeting.Show, 
        Meeting.Competitor Meeting_Competitor,
        Meeting.Secret Meeting_Secret,
        Meeting.Name Meeting_Name,
        Meeting.Details Meeting_Details,
        Meeting.Date Meeting_Date,
        Meeting.Show Meeting_Show,
        Meeting.Organizer Meeting_Organizer,
        Competitor.Name Competitor_Name,
        Competitor.Country Competitor_Country
    FROM Meeting
    JOIN Competitor on Competitor.WID = Meeting.Competitor 
    ";
    if ($mine) {
        $sql .= "
        WHERE Meeting.Competitor = {$Competitor->id}
            OR '{$Competitor->wca_id}' IN 
                (SELECT 
                    WCAID 
                FROM MeetingOrganizer
                WHERE MeetingOrganizer.Meeting = Meeting.ID)
        ";
    } elseif ($Competitor and ! CheckMeetingGrand()) {
        $sql .= "
        WHERE
            Meeting.Show
            OR
            Meeting.Competitor = {$Competitor->id}
            OR
            '{$Competitor->wca_id}' IN 
                (SELECT 
                    WCAID 
                FROM MeetingOrganizer
                WHERE MeetingOrganizer.Meeting = Meeting.ID)
        ";
    } elseif (!CheckMeetingGrand()) {
        $sql .= "WHERE
            Meeting.Show
        ";
    }
    $sql .= " ORDER BY Meeting.Date DESC";
    ?>

    <table class='table_new'>
        <thead>
            <tr>
                <?php if ($Competitor) { ?>
                    <td/>
                    <td/>
                <?php } ?>
                <td>
                    Organizer
                </td>
                <td/>
                <td>
                    Competition
                </td>
                <td>
                    Date
                </td>
                <td>
                    Web site
                </td>
            </tr>    
        </thead>
        <tbody>
            <?php
            DataBaseClass::Query($sql);
            foreach (DataBaseClass::getRows() as $meeting) {
                ?>
            <form method="POST" action="<?= PageIndex() . "Actions/MeetingShow" ?>">
                <tr>   
                    <?php if ($Competitor) { ?>
                        <td>
                            <?php if (!$meeting['Meeting_Show']) { ?>
                                <i class="far fa-eye-slash"></i>
                            <?php } ?>
                        </td>
                        <td align="center" >
                            <?php if ($meeting['Meeting_Competitor'] == $Competitor->id) { ?>
                                <i class="far fa-crown"></i>
                            <?php } elseif (in_array($Competitor->wca_id, explode(",", $meeting['Meeting_Organizer']))) { ?>
                                <i class="fas fa-user-tie"></i>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?= short_name($meeting['Competitor_Name']) ?>
                    </td>   
                    <td>
                        <span class='flag-icon flag-icon-<?= strtolower($meeting['Competitor_Country']) ?>'></span>
                    </td>
                    <td>
                        <a href="<?= PageIndex() ?>Meetings/<?= $meeting['Meeting_Secret'] ?>"><?= $meeting['Meeting_Name'] ?> </a>
                    </td>
                    <td>
                <nobr><?= date('d M Y', strtotime($meeting['Meeting_Date'])) ?></nobr>
                </td>
                <td align=right>
                    <?php if ($meeting['Meeting_Website']) { ?>
                        <a target="_blank" href="<?= $meeting['Meeting_Website'] ?>">
                            <?php preg_match('/https?:\/\/(?:www\.|)([\w.-]+).*/', $meeting['Meeting_Website'], $matches); ?>
                            <?php if (isset($matches[1])) { ?>
                                <?= $matches[1] ?>
                            <?php } else { ?>
                                Website
                            <?php } ?>        
                        </a>
                    <?php } ?>
                </td>
                </tr>
            </form>
        <?php } ?>
        </tbody>
    </table>  
</div>