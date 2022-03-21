<h2>
    <i class="<?= $event_select->image ?>"></i> <?= $event_select->name ?>
</h2>
<h2>
    <?php if (isset($ratings[$event_select->id]['best'])) { ?>
        <a 
            class='<?= $type == 'best' ? 'select' : '' ?>' 
            href='<?= PageIndex() ?>competitions/rankings/<?= $event_select->code ?>/best'>
            Single</a>
    <?php } ?>
    <?php if (isset($ratings[$event_select->id]['average'])) { ?>
        <a 
            class='<?= $type == 'average' ? 'select' : '' ?>' 
            href='<?= PageIndex() ?>competitions/rankings/<?= $event_select->code ?>/average'>
            Average</a>
    <?php } ?>
</h2>
<table class='table_new'>
    <thead>
        <tr>
            <td>#</td>
            <td>Name</td>
            <td>Result</td>
            <td>Competition</td>
            <?php if ($type == 'average') { ?>
                <td colspan='5' align='center'>Solves</td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ratings[$event_select->id][$type] ?? [] as $rating) { ?>
            <tr>
                <td >
                    <?= $rating->order; ?>
                </td>
                <td>
                    <?php if ($rating->FCID) { ?>
                        <a href="<?= PageIndex() . "competitions/rankings/competitor/$rating->FCID" ?>">
                            <?= $rating->competitor_name ?>
                        </a>
                    <?php } else { ?>
                        <?= $rating->competitor_name ?>
                    <?php } ?>
                </td>
                <td align='right' class="<?= $rating->order == 1 ? 'record' : '' ?>">
                    <b><?= $rating->result ?></b>
                </td>
                <td>
                    <a href="<?= PageIndex() ?>competitions/<?= $rating->competition_secret ?>">
                        <?= $rating->competition_name ?>
                    </a>
                </td>
                <?php if ($type == 'average') { ?>
                    <?php foreach (range(1, 5) as $i) { ?>
                        <td align='right'>
                            <?= $rating->{"attempt$i"} ?? false ?>
                        </td>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>