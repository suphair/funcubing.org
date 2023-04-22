<?php $organizers = unofficial\getOrganizers() ?>
<div class="shadow" >
    <h1>
        <?= $ranked_icon ?>
        List of rankings
    </h1>   
    <table class="table_new">
        <thead>
            <tr>
                <td>
                    Rankings of competitors of competitions of this organizer
                </td>
                <td>
                    Included rankings of competitors of competitions of other organizers
                </td>
            </tr>
        </thead>    
        <tbody>
            <?php foreach ($organizers as $organizer) { ?>
                <tr>
                    <td>
                        <a href="<?= PageIndex() ?>competitions/rankings/<?= $organizer->wid ?>">
                            <?= $organizer->name ?>
                        </a>
                    </td>
                    <td style="white-space: normal">
                        <?php $partners = unofficial\getPartners($organizer->wid); ?>
                        <?php foreach ($partners as $partner) { ?>
                <nobr>
                    <?= $partner->name ?>,
                </nobr>
            <?php } ?>
            </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>