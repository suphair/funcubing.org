<h2>
    <i class="fas fa-users"></i>
    <?= t('Competitors', 'Участники') ?>
</h2>
<?php if ($comp->my or $comp->organizer) { ?>
    <i class="fas fa-user-cog"></i> <a href="<?= PageIndex() . "competitions/$comp->secret/registrations" ?>"><?= t('Registrations', 'Регистрации') ?></a>
    <i class="fas fa-users"></i> <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $comp->secret ?>/registrations"><?= t('Export registrations', 'Экспорт регистраций') ?></a>
    <i class="fas fa-list-alt"></i> <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $comp->secret ?>/results"><?= t('Export results', 'Экспорт результатов') ?></a>
    <i class="fas fa-certificate"></i> <a target="_blank" href="?action=certificates"><?= t('Competitors', 'Сертификаты') ?></a>
    <?php if (unofficial\federation()) { ?>
        <?= $wca_icon ?>
        <a href="<?= PageIndex() . "competitions/$comp->secret/wcaid" ?>"><?= t('Binding to WCA', 'Привязка к WCA') ?></a>
    <?php } ?>
    <br>
<?php } ?>
<table class="table_new">
    <thead>
        <tr>
            <td>
                <?= t('Competitor', 'Имя') ?>
            </td>
            <?php
            $round_next_exists = false;
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                if ($event_round->round > 1) {
                    $round_next_exists = true;
                }
            }
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                ?>
                <td class="table_new_center <?= ($event_round->round == $rounds) ? 'border_right' : '' ?>" style='vertical-align: bottom'>
                    <font size='1'>
                    <?php if ($event_round->round == $rounds) { ?>
                        <i class="<?= $rounds_dict[3]->image ?>"></i>
                    <?php } else { ?>
                        <i class="<?= $rounds_dict[$event_round->round]->image ?>"></i>
                    <?php } ?>
                    </font>
                    <br>
                    <a  href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_round->event_dict]->code}/$event_round->round" ?> ">
                        <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                    </a>
                </td>
            <?php } ?>
        </tr>
        <tr <?= !$round_next_exists ? 'hidden' : '' ?>>
            <td>
                <i class="fas fa-caret-square-right"></i> <?= t('Advance next round', 'Проходят дальше') ?>
            </td>
            <?php
            foreach ($comp_data->event_rounds as $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                ?>
                <td align='center' style="padding:0px" class="<?= ($event_round->round == $rounds) ? 'border_right' : '' ?>">
                    <?php if ($event_round->round != $rounds) { ?>
                        <?= $event_round->next_round_value ?><?= $event_round->next_round_procent ? '%' : '' ?>
                    <?php } ?> 
                </td>
            <?php } ?>
        </tr> 
    </thead>
    <tbody>
        <?php
        $c = 0;
        foreach ($comp_data->competitors as $competitor_id => $competitor) {
            if (sizeof($comp_data->competitors[$competitor_id]->events) > 0) {
                if ($c++ == 20) {
                    $c = 0;
                    ?>
                    <tr>
                        <td/>
                        <?php foreach ($comp_data->event_rounds as $event_round_id => $event_round) { ?>
                            <td align='center' class="<?= ($event_round->round == $event_round->rounds) ? 'border_right' : '' ?>">
                                <i class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                            </td>
                        <?php } ?>
                    </tr>       
                <?php } ?>

                <tr>
                    <td>
                        <?php if ($competitor->non_resident) { ?>
                            <i class='fas fa-globe'></i>
                            <?php
                        }
                        if ($comp->ranked and!$competitor->non_resident) {
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
                    <?php
                    foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                        $event = unofficial\getEventByEventround($event_round_id);
                        $result = unofficial\getCompetitorsByEventround($event_round_id, $event)[$competitor_id] ?? FALSE;
                        $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                        ?>
                        <td align="center" class="<?= ($event_round->round == $rounds) ? 'border_right' : '' ?>" >
                            <?php if ($result) { ?>
                                <?php if ($result->place ?? FALSE) { ?>
                                    <font align="center" class="<?= $result->podium ? 'podium table_new_bold' : '' ?>">
                                    <?= $result->place ?><?= $result->next_round ? '&bull;' : '' ?><?= $result->podium ? '*' : '' ?>
                                    </font>
                                <?php } else { ?>
                                    <i style='color:var(--light_gray)' class="<?= $events_dict[$event_round->event_dict]->image ?>"></i>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td>
                <?= t('Total', 'Всего') ?> <?= sizeof($comp_data->competitors) ?>
            </td>
            <?php
            foreach ($comp_data->event_rounds as $event_round_id => $event_round) {
                $rounds = $comp_data->events[$event_round->event_dict]->event_rounds;
                ?>
                <td align='center' style="vertical-align:bottom;" class="<?= ($event_round->round == $rounds) ? 'border_right' : '' ?>">
                    <?php $competitors_count = sizeof($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors ?? []) ?>
                    <?php
                    $results_count = 0;
                    foreach ($comp_data->competitors as $competitor_id => $competitor) {
                        if ($comp_data->rounds[$event_round->event_dict][$event_round->round]->competitors[$competitor_id]->place ?? FALSE) {
                            $results_count++;
                        }
                    }
                    ?>
                    <?php if ($results_count) { ?>
                        <?php if ($results_count != $competitors_count) { ?>
                            <?= $results_count ?>
                        <?php } else { ?>
                            <?php if ($event_round->round == $rounds) { ?>
                                <i style='color:var(--green)' class="fas fa-flag-checkered"></i>
                            <?php } else { ?>
                                <i style='color:var(--green)' class="fas fa-arrow-alt-circle-right"></i>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <i style='color:var(--gray)' class="fas fa-hourglass-start"></i>
                    <?php } ?>
                    <br>
                    <?= $competitors_count ?: '&nbsp'; ?>
                </td>
            <?php } ?>
        </tr>
    </tfoot>
</table>
<?php if (!sizeof($comp_data->competitors)) { ?>
    <p>No competitors</p>
<?php } ?>