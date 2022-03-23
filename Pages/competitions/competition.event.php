<table class="table_new">
    <thead>
        <tr>
            <td>Place</td>
            <td>Competitor</td>
            <?php foreach (range(1, $event->attempts) as $i) { ?>
                <td class="attempt"><?= $i ?></td>
            <?php } ?>
            <?php foreach ($formats as $format) { ?>
                <td class="attempt"><?= ucfirst($format) ?></td>
            <?php } ?>
        <tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td align="center" class="<?= $competitor->podium ? 'podium' : '' ?> <?= $competitor->next_round ? 'next_round' : '' ?>">
                    <?= $competitor->place ?> 
                </td>
                <td >
                    <?php
                    if ($comp->ranked) {
                        $link = $competitor->FCID ? "rankings/competitor/$competitor->FCID" : false;
                    } else {
                        $link = "competitor/$competitor->id";
                    }
                    if ($link) {
                        ?>
                        <a href="<?= PageIndex() . "competitions/$link" ?>"><?= $competitor->name ?></a>
                    <?php } else { ?>
                        <?= $competitor->name ?>
                    <?php } ?>
                </td>
                <?php foreach (range(1, $event->attempts) as $i) { ?>
                    <td class="<?= $i == $event->attempts ? 'border-right-solid' : '' ?> attempt">
                        <?= str_replace("dns", "", $competitor->{"attempt$i"}) ?>
                    </td>
                <?php } ?>

                <?php foreach ($formats as $format) { ?>
                    <td  class="attempt">
                        <b>
                            <?= str_replace(["dns", "-cutoff"], ["dnf", ""], $competitor->$format) ?>
                        </b>
                    </td>
                <?php } ?>    
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if (!sizeof($competitors)) { ?>
    <p>No competitors</p>
<?php } ?>   
