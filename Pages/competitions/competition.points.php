<?php if ($competition->points) { ?>
    <?php $points = $points_dict[$competition->points] ?>
    <h2>
        <div style='display: inline-block;width:50%;padding:0px;margin:0px;'>
            <i class="<?= $points->icon ?>"></i>
            <?= t('Overall standings', 'Общий зачёт') ?> - <?= $points->name ?>
        </div>
    </h2>
    <?php include "competition.points.$competition->points.php"; ?>
<?php } else { ?>
    Not found
<?php } ?>
