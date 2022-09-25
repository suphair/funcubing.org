<?php
include 'competitions.menu.php';
$competitions = api\get_competitions();

foreach ($competitions as $c => $competition) {
    foreach ($competition->organizers ?? [] as $organizer) {
        if ($organizer->main ?? false) {
            $competitions[$c]->main_organizer = $organizer;
        }
    }
    if ($show_hidden) {
        if ($competition->is_publish) {
            unset($competitions[$c]);
        }
    } else {
        if ($mine and!in_array('true', array_values((array) $competition->my_roles ?? []))) {
            unset($competitions[$c]);
        }
        if ($all and!in_array('true', array_values((array) $competition->my_roles ?? [])) and!$competition->is_publish) {
            unset($competitions[$c]);
        }
        if ($ranked and!($competition->is_ranked and
                ($competition->is_publish or
                $admin or
                $federation or
                in_array('true', array_values((array) $competition->my_roles ?? []))))) {
            unset($competitions[$c]);
        }
    }
}
?>
<table class='table thead_stable'>
    <thead>
        <tr>
            <th>
                [<?= count($competitions); ?>]
            </th>
            <th>
                <?= t('Date', 'Дата') ?>
            </th>
            <th>
                <?= t('Competition', 'Наименование') ?>
            </th>
            <th>
                <?= t('City', 'Город') ?>
            </th>
            <th>
                <?= t('Web site', 'Сайт') ?> <i class="fas fa-external-link-alt"></i>
            </th>
        </tr>    
    </thead>
    <tbody>
        <?php foreach ($competitions as $competition) { ?>
            <tr>   
                <td align="left">
                    <?php if ($competition->is_ranked) { ?>
                        <?= $ranked_icon ?>
                    <?php } ?>
                    <?php if ($competition->is_approved) { ?>
                        <i title="Подтверждено Федерацией Спидкубинга" class="message fas fa-check"></i>
                    <?php } ?>
                    <?php if ($competition->my_roles->delegate ?? false) { ?>
                        <i title="<?= t('Delegate', 'Делегат') ?>" class="fas fa-signature"></i>
                    <?php } ?>
                    <?php if ($competition->my_roles->organizer ?? false or $competition->my_roles->main_organizer ?? false) { ?>
                        <i title="<?= t('Organizer', 'Организатор') ?>" class="fas fa-user-tie"></i>
                    <?php } ?>
                    <?php if ($competition->my_roles->competitor ?? false) { ?>
                        <i title="<?= t('Competitor', 'Участник') ?>" class="fas fa-user"></i>
                    <?php } ?>
                    <?php if (strtotime($competition->start_date) > strtotime(date('Y-m-d'))) { ?>
                        <i class="fas fa-hourglass-start"></i>
                    <?php } ?>
                    <?php
                    if (strtotime($competition->start_date) <= strtotime(date('Y-m-d')) and
                            strtotime($competition->end_date) >= strtotime(date('Y-m-d'))) {
                        ?>
                        <i style='color:var(--green)' class="fas fa-running"></i>
                    <?php } ?>
                    <?php if (!$competition->is_publish) { ?>
                        <i title="<?= t('Hidden', 'Спрятано') ?>" class="far fa-eye-slash"></i>
                    <?php } ?>
                </td>
                <td>
                    <?= dateRange($competition->start_date, $competition->end_date) ?>

                </td>
                <td>                    
                    <a href="<?= PageIndex() ?>competitions/<?= $competition->id ?>"><?= t(transliterate($competition->name), $competition->name); ?> </a>
                </td>
                <td>
                    <?= str_replace(',', ',<br>', $competition->city) ?>
                </td>
                <td title="<?= $competition->website ?>">
                    <?php if (!$ranked) { ?>
            <nobr>
                <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $competition->main_organizer->wca_id ?>">
                    <?= $competition->main_organizer->name ?></a>
            </nobr>  
            &bull; 
        <?php } ?>
        <nobr>
            <?php unofficial\getLink($competition->website) ?>
        </nobr>  
    </td>
    </tr>
<?php } ?>
</tbody>
</table>  
<div class="details_footer">
    <span><i class="fas fa-signature"></i> - <?= t('Delegate', 'Делегат') ?></span>
    <span><i class="fas fa-user-tie"></i> - <?= t('Organizer', 'Организатор') ?></span>
    <span><i class="fas fa-user"></i> - <?= t('Competitor', 'Участник') ?></span>
    <span><?= $ranked_icon ?>  - <?= t('Speedcubing Federation', 'Федерация Спидкубинга') ?></span>
    <span><i class="message fas fa-check"></i>  - <?= t('Confirmed by the Speedcubing Federation', 'Подтверждено Федерацией Спидкубинга') ?></span>
    <span><i class="far fa-eye-slash"></i> - <?= t('Hidden', 'Спрятано') ?></span>
</div>
