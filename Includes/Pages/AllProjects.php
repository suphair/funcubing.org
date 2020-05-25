
<table>
    <tr>
        <td>
            <img src='<?= PageIndex() ?>Logo/Logo_Color_UC.png' width='50'> 
        </td>
        <td>
            <h3><a href='<?= PageIndex() ?>?Meetings'>Unofficial competitions</a></h3>
            For WCA events at unofficial competitions. Any speedcuber can register a competition.
        </td>
    </tr>
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <td>
            <img src='<?= PageIndex() ?>Logo/Logo_Color_GC.png' width='50'> 
        </td>
        <td>
            <h3><a href='<?= PageIndex() ?>?CompetitionGoals'>Competition Goals</a></h3>
            To set personal goals for official disciplines in official competitions.
        </td>
    </tr>
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <td>
            <img src='<?= PageIndex() ?>Logo/Logo_Color_SA.png' width='50'> 
        </td>
        <td>
            <h3><a href='<?= PageIndex() ?>Achievements'>Speedcuber's Achievements</a></h3>
            Just for fun and nothing more. Only personal records are used.
        </td>
    </tr>
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <td>
            <img src='<?= PageIndex() ?>Logo/Logo_Color_MB.png' width='50'> 
        </td>
        <td>
            <h3><a href='<?= PageIndex() ?>MosaicBuilding'>Mosaic Building</a></h3>
            Upload the image. Get the PDF. Create picture.
        </td>
    </tr>  
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <td>
            <img src='<?= PageIndex() ?>Logo/Logo_Color_FC.png' width='50'> 
        </td>
        <td>
            <h3><a href='<?= PageIndex() ?>FriendsCompetitions'>Friends' Competitions</a></h3>
            Shows the competitions your friends have registered for.
        </td>
    </tr>  
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <?php
        DataBaseClass::Query("Select count(*) count from `MailUpcomingCompetitions` where Status=1");
        $data = DataBaseClass::getRow()
        ?>
        <td align='center'><?= svg_green(20, 'Competitions Announcements') ?> 
        </td>
        <td> 
            <h3><a href='<?= PageIndex() ?>MailUpcomingCompetition'>Competitions' Announcements</a>
                <span class="badge"><?= $data['count'] ?></span></h3>
            Subscription to announcements of WCA competitions.
        </td>
    </tr>
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <td align='center'><?= svg_green(20, 'WCA API') ?> 
        </td>
        <td>
            <h3><a href='<?= PageIndex() ?>WCA_API'>WCA API</a></h3>
            Examples of use WCA API
        </td>
    </tr>
    <tr class='no_border'><td>&nbsp;</td></tr>
    <tr>
        <td align='center'><?= svg_green(20, 'Translit') ?> 
        <td>
            <h3><a href='<?= PageIndex() ?>Translit'>Transliteration of Russian names</a></h3>
            Transliteration and use cases in WCA
        </td>
    </tr>

</table>

