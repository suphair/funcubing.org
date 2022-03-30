<h2>
    <i class="fas fa-trophy"></i> 
    Updated FunCubing records</h2>
<table class="table_new">
    <thead>
        <tr>
            <td></td>
            <td>Event</td>
            <td>Type</td>
            <td class='attempt'>Result</td>
            <td>Competitor</td>
            <td colspan='5' align='center'>Solves</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events_dict as $event_id => $event) { ?>
            <?php
            if ($records[$event_id] ?? false) {
                foreach ($records[$event_id] as $record) {
                    ?>
                    <tr>
                        <td>
                            <i class="<?= $event->image ?>"></i>
                        </td>
                        <td>
                            <?= $event->name ?>
                        </td>
                        <td>
                            <?= ['best' => 'Single', 'average' => 'Average'][$record->type] ?>
                        </td>
                        <td class='record'>
                            <?= $record->result ?>
                        </td>
                        <td>
                            <?php
                            $link = $record->FCID ? "rankings/competitor/$record->FCID" : false;
                            if ($link) {
                                ?>
                                <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $record->competitor_name ?></a>
                            <?php } else { ?>
                                <?= $record->competitor_name ?>
                            <?php } ?>
                        </td>
                        <?php foreach (range(1, 5) as $i) { ?>
                            <td class='attempt'>
                                <?= $record->{"attempt$i"} ?? false ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>