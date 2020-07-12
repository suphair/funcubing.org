<table class='table_info'>
    <tr>
        <td>
            Status
        </td>
        <td>
            <?php if ($announcements->status) { ?>
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
            <a href='https://www.worldcubeassociation.org/persons/<?= $me->wca_id ?>' target='_blank'>
                <?= $me->wca_id ?>
            </a>
        </td>
    </tr>
    <tr>
        <td>
            Name
        </td>
        <td>
            <?= $me->name ?>
        </td>
    </tr>
    <tr>
        <td>
            Email
        </td>
        <td>
            <?= $announcements->email ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php
            $mailTest = postGet('test');
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
                <form style="margin:0px;padding:0px;" method="POST" action="?Test">
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
            <?= $announcements->timestamp ?>
        </td>
    </tr>
    <tr>
        <td>
            Countries</td>
        <td></td>
    </tr>
    <?php foreach ($countries as $country) { ?>
        <?php $countryData = db::row("SELECT name, code FROM dict_countries where iso2='$country'"); ?>
        <?php if (!$countryData) continue; ?>
        <tr>
            <td>
                <span class='flag-icon flag-icon-<?= strtolower($country) ?>'></span>
            </td>
            <td>
                <a href='https://www.worldcubeassociation.org/competitions?region=<?= str_replace(" ", "+", $countryData->code) ?>' target='_blank'>
                    <?= $countryData->name ?>
                </a>
            </td>
        </tr>               
    <?php } ?>
    <tr>
        <td>
        </td>
        <td>
            <?php if ($announcements->status) { ?>
                <form 
                    onsubmit="return confirm('Confirm unsubscribe')"
                    method="POST" 
                    action="?Unsubscribe">
                    <input type="submit" class="delete" value="Unsubscribe">
                </form> 
            <?php } else { ?>
                <form 
                    method="POST"
                    action="?Subscribe">
                    <input type="submit" value="Subscribe">
                </form> 
            <?php } ?>
        </td>
    </tr>
</table>

<form method="POST" action="?Save">
    <select style="width: 600px" ID="countries-chosen-select" Name="countries[]" data-placeholder="Choose  countries" class="chosen-select" multiple>
        <?php
        $countriesList = db::rows("SELECT * FROM dict_countries ORDER BY name");
        foreach ($countriesList as $country) {
            ?>
            <option name="countries[]"  <?= in_array($country->iso2, $countries) ? 'selected' : '' ?> value='<?= $country->iso2 ?>'>
                <?= $country->iso2 ?> - <?= $country->name ?>
            </option>
        <?php } ?>
    </select>
    <input type="submit" value="Save countries">
</form>    

<script src="<?= PageLocal() ?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>
