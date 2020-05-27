<div class="shadow" >
    <h2>Subscription to announcements of WCA competitions</h2>
    <p style="padding: 10px 0px;">When WCA competitions for tracked countries are announced on the WCA website, you will receive an email</p>

    <?php
    $competitor = GetCompetitorData();
    if ($competitor) {
        DataBaseClass::Query("Select * from MailUpcomingCompetitions where Competitor={$competitor->id}");
        if (!DataBaseClass::getRow()) {
            DataBaseClass::Query("Insert into MailUpcomingCompetitions (Email,Competitor,Country) values('{$competitor->email}',{$competitor->id},'$competitor->country_iso2')");
        }
        DataBaseClass::Query("Update MailUpcomingCompetitions set Email='{$competitor->email}' where Competitor={$competitor->id}");
        DataBaseClass::Query("Select * from MailUpcomingCompetitions where Competitor={$competitor->id}");
        $mailUpcomingCompetitions = DataBaseClass::getRow();
        $mail = $mailUpcomingCompetitions['Email'];
        $countries = explode(",", $mailUpcomingCompetitions['Country']);
        ?>   
        <table class='table_info'>
            <tr>
                <td>
                    Status
                </td>
                <td>
                    <?php if ($mailUpcomingCompetitions['Status']) { ?>
                        <span style='color:var(--green)'>
                            <i class="fas fa-envelope-open-text"></i>
                            Subscription is done
                        </span>
                    <?php } else { ?>
                        <span style='color:var(--red)'>
                            <i class="far fa-hand-paper"></i>
                            Subscription disabled
                        </span>
                    <?php } ?>        
                </td>
            </tr>
            <tr>
                <td>
                    WCA ID
                </td>
                <td>
                    <a href='https://www.worldcubeassociation.org/persons/<?= $competitor->wca_id ?>' target='_blank'>
                        <?= $competitor->wca_id ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    Name
                </td>
                <td>
                    <?= Short_Name($competitor->name) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Email
                </td>
                <td>
                    <?= $mail ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php
                    $mailTest = GetMessage('mailTest');
                    if ($mailTest == 1) {
                        ?>
                        <p style='color:var(--green)'>
                            The email was sent successfully at <?= date('H:i:s'); ?> (UTC+3)
                        </p>
                    <?php } else { ?>
                        <?php if ($mailTest) { ?>
                            <p style='color:var(--red)'>
                                <?= $mailTest ?> at <?= date('H:i:s'); ?> (UTC+3)
                            </p>
                        <?php } ?>
                        <form style="margin:0px;padding:0px;" method="POST" action="<?= PageIndex() . "Actions/MailUpcomingCompetitionsTest" ?>">
                            <input style="margin:0px;" type="submit" value="Send a test email">
                        </form> 
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>
                    Check time
                </td>
                <td>
                    <?= $mailUpcomingCompetitions['announced_at'] ?>
                </td>
            </tr>
            <tr>
                <td>
                    Countries</td>
                <td></td>
            </tr>
            <?php foreach ($countries as $country) { ?>
                <?php
                DataBaseClass::Query("Select * from Country where ISO2='$country'");
                $countryData = DataBaseClass::getRow();
                ?>

                <tr>
                    <td>

                        <span class='flag-icon flag-icon-<?= strtolower($country) ?>'></span>
                    </td>
                    <td>
                        <a href='https://www.worldcubeassociation.org/competitions?region=<?= str_replace(" ", "+", $countryData['Code']) ?>' target='_blank'>
                            <?= $countryData['Name'] ?>
                        </a>
                    </td>
                </tr>               
            <?php } ?>
            <tr>
                <td>
                </td>
                <td>
                    <?php if ($mailUpcomingCompetitions['Status']) { ?>
                        <form 
                            onsubmit="return confirm('Confirm unsubscribe')"
                            method="POST" action="<?= PageIndex() . "Actions/MailUpcomingCompetitionsUnsubscribe" ?>">
                            <input type="submit" class="delete" value="Unsubscribe">
                        </form> 
                    <?php } else { ?>
                        <form method="POST" action="<?= PageIndex() . "Actions/MailUpcomingCompetitionsSubscribe" ?>">
                            <input type="submit" value="Subscribe">
                        </form> 
                    <?php } ?>
                </td>
            </tr>
        </table>

        <form method="POST" action="<?= PageIndex() . "Actions/MailUpcomingCompetitionsSave" ?>">
            <select style="width: 600px" ID="countries-chosen-select" Name="countries[]" data-placeholder="Choose  countries" class="chosen-select" multiple>
                <?php
                DataBaseClass::Query("Select * from Country where ISO2<>'' and Name<>'' order by Name");
                foreach (DataBaseClass::getRows() as $country) {
                    ?>
                    <option name="countries[]"  <?= in_array($country['ISO2'], $countries) ? 'selected' : '' ?> value='<?= $country['ISO2'] ?>'>
                        <?= $country['ISO2'] ?> - <?= $country['Name'] ?>
                    </option>
                <?php } ?>
            </select>
            <input type="submit" value="Save countries">
        </form>    

        <script src="<?= PageLocal() ?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>

    <?php } else { ?>
        <h3 class="error" style="padding:20px 0px;">
            <i class="fas fa-hand-paper"></i>
            To subscribe you need to sign in with WCA.
        </h3> 
    <?php } ?>

    <?php
    DataBaseClass::Query("Select count(*) count from `MailUpcomingCompetitions` where Status=1");
    $data = DataBaseClass::getRow()
    ?>
</div>
<p>
    <i class="fas fa-info-circle"></i>  
    Total active subscriptions: <?= $data['count'] ?>
</p>
