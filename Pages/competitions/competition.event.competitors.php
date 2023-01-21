<?php
$round = db::escape(request(4));

$record_attempts = [];
foreach ($records[$event->event_dict] ?? [] as $record) {
    $record_attempts[$record->type][] = $record->round_id;
}
?>
<table width='100%'>
    <tr>
        <td width='50%' style='vertical-align: top'>
            <?php if ($event->round == 1) { ?>
                <?php
                $competitors_first = $comp_data->competitors;
                foreach ($competitors_first as $competitor_id => $competitor_first) {
                    if ($competitors[$competitor_id] ?? FALSE) {
                        unset($competitors_first[$competitor_id]);
                    }
                }
                if (!$competition->is_ranked) {
                    ?>
                    <form action='?resuts_registration_add' method='POST'>        
                        <?= t('Create new competitor', 'Создать нового участника') ?>
                        <input name='name'>
                        <button>
                            <i class="fas fa-plus-square"></i>
                            <?= t('Create', 'Создать') ?>
                        </button>
                    </form>
                    <?php
                }
                if (sizeof($competitors_first)) {
                    ?>

                    <form method="POST" action="?resuts_registrations_add_first">
                        <table class="table_new">
                            <thead>
                                <tr>
                                    <td><?= t('Competitor', 'Имя') ?></td>
                                    <td><?= t('Add', 'Выбрать') ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($competitors_first as $competitor_first) { ?>
                                    <tr>
                                        <td><?= $competitor_first->name ?> </td>
                                        <td align="center">
                                            <?php ?>
                                            <input name="competitors[<?= $competitor_first->id ?>]"  type="Checkbox">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button>
                            <i class="fas fa-user-plus"></i>
                            <?= t('Add competitors', 'Добавить участников') ?>
                        </button>
                    </form>
                <?php } ?>

            <?php } else { ?>
                <?php
                $competitors_prev = unofficial\getCompetitorsByEventdictRound($comp->id, $event_dict, $round - 1);
                foreach ($competitors_prev as $competitor_id => $competitor_first) {
                    if (!$competitors_prev[$competitor_id]->place) {
                        unset($competitors_prev[$competitor_id]);
                    } else {
                        if ($competitors[$competitor_id] ?? FALSE) {
                            $competitors_prev[$competitor_id]->this_register = TRUE;
                            $competitors_prev[$competitor_id]->this_place = $competitors[$competitor_id]->place;
                        } else {
                            $competitors_prev[$competitor_id]->this_register = FALSE;
                            $competitors_prev[$competitor_id]->this_place = FALSE;
                        }
                    }
                }

                if (sizeof($competitors_prev)) {
                    ?>
                    <form method="POST" action="?resuts_registrations_add_next">
                        <table class="table_new">
                            <thead>
                                <tr>
                                    <td>
                                        <?= t('Place in the preview round', 'Место в пред. раунде') ?>
                                    </td>
                                    <td>
                                        <?= t('Competitor', 'Имя') ?>
                                    </td>
                                    <td>
                                        <?= t('Add/Remove', 'Добавить/Отменить') ?>
                                    </td>
                                    <td>
                                        <?= t('Bulk select', 'Массовый выбор') ?>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($competitors_prev as $competitor) { ?>
                                    <tr>
                                        <td align="right">
                                            <?= $competitor->place ?> 
                                        </td>
                                        <td>
                                            <label for='competitors[<?= $competitor->id ?>]'>
                                                <?= $competitor->name ?>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <?php if ($competitor->this_place) { ?>
                                                <i class="fas fa-user-lock"></i>
                                            <?php } else { ?>
                                                <input hidden name="competitors[<?= $competitor->id ?>]" value='off'>
                                                <input 
                                                <?= $competitor->this_register ? 'checked' : '' ?>
                                                    data-competitor-place = '<?= $competitor->place ?>' id ='competitors[<?= $competitor->id ?>]' name="competitors[<?= $competitor->id ?>]"  type="Checkbox">
                                                <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($competitor->place >= 1) { ?>
                                                <a href='#' data-competitor-place-select = '<?= $competitor->place ?>'>
                                                    <?= t("up to $competitor->place places", "до $competitor->place места") ?>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>    
                        <button>
                            <i class="fas fa-user-plus"></i>
                            <?= t('Add competitors', 'Добавить участников') ?>
                        </button>
                    </form>  
                <?php } ?>
            <?php } ?>
        </td>
        <td width='50%' style='vertical-align: top'>
            <table class="table_new">
                <thead>
                    <tr>
                        <td>#</td>
                        <td><?= t('Competitor', 'Имя') ?></td>
                        <td><?= t('Complete', 'Завершено') ?></td>
                        <td><?= t('Remove', 'Отменить') ?></td>
                    <tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $bulk_delete = false;
                    usort($competitors, function($a, $b) {
                        return ($a->name > $b->name);
                    });
                    foreach ($competitors as $competitor) {
                        ?>
                        <tr>
                            <td >
                                <?= $i++; ?> 
                            </td>
                            <td>
                                <?= $competitor->name ?>
                            </td>
                            <td class='table_new_center'><?= $competitor->attempts ? '<i class="fas fa-check"></i>' : '' ?></td>
                            <td class='table_new_center'>
                                <?php
                                if (!$competitor->attempts) {
                                    $bulk_delete = true;
                                    ?>
                                    <form method="POST" action="?result_delete"
                                          onsubmit="return confirm('Remove competitor {<?= $competitor->name ?>} from this event ?');">
                                        <input hidden name="competitor_round" value="<?= $competitor->competitor_round ?>">
                                        <button " style="margin:0px;padding:1px 2px;" class="delete">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<script>
<?php include 'competition.event.competitors.js' ?>
</script>
