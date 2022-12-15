<?php
$sheet_get = filter_input(INPUT_GET, 'sheet');
$sheet_select = null;

foreach ($competition->sheets ?? [] as $sheet) {
    if ($sheet_get == $sheet->sheet) {
        $sheet_select = $sheet;
    }
}
?>
<h2>
    <i title="General info" class="fas fa-info-circle"></i>
    <?php if ($competition->sheets ?? false) { ?>
        <a href ="?" class="<?= !$sheet_select ? 'select' : '' ?>">
            <?= t('General info', 'Информация'); ?></a>
    <?php } else { ?>
        <?= t('General info', 'Информация'); ?>
    <?php } ?>
    <?php foreach ($competition->sheets ?? [] as $sheet) { ?>
        | <a href ="?sheet=<?= $sheet->sheet ?>" class="<?= ($sheet_select and $sheet_select->sheet == $sheet->sheet) ? 'select' : '' ?>"><?= $sheet->title ?></a>
    <?php } ?>
</h2>
<br>
<?php if ($sheet_select) { ?>
    <div class="sheet">
        <?= markdown::convertToHtml($sheet_select->content); ?>
    </div>
<?php } else { ?>
    <table width="100%" style="padding:0px; margin:0px" class="competition_info">
        <tr>
            <td valign="top" align="center">
                <?php if ($competition->logo) { ?>
                    <img src="<?= $competition->logo ?>" height="200px" style="padding-right: 20px;"/>

                <?php } ?>
                <?php if ($competition->details ?? '') { ?>
                    <br>
                <?php } else { ?>
                </td>
                <td>
                <?php } ?>
                <table class="table_info">
                    <?php if ($federation or $admin) { ?>
                        <tr>
                            <td><?= $ranked_icon ?></td>
                            <td>
                                <a href="<?= PageIndex() . "competitions/$competition->id/ranking" ?>"><?= t('Setting SF', 'Настройки ФС') ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($grand->setting) { ?>
                        <tr>
                            <td><i class="fas fa-cog"></i></td>
                            <td>
                                <a href="<?= PageIndex() . "competitions/$competition->id/setting" ?>"><?= t('Setting', 'Настройки') ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($grand->edit) { ?>
                        <tr>
                            <td><i class="fas fa-users-cog"></i></td>
                            <td>
                                <a href="<?= PageIndex() . "competitions/$competition->id/registrations" ?>"><?= t('Registrations', 'Регистрации') ?></a> 
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <?php if (!$competition->is_publish) { ?>
                            <td></td>
                            <td><i class="far fa-eye-slash"></i>
                                Private</td>
                        <?php } ?>
                    </tr>
                    <?php if ($competition->city) { ?>
                        <tr>
                            <td><?= t('City', 'Город') ?></td>
                            <td><i class="fas fa-map-marker-alt"></i>
                                <?= $competition->city ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><?= t('Date', 'Дата') ?></td>
                        <td><i class="far fa-calendar-alt"></i>
                            <?= dateRange($competition->start_date, $competition->end_date) ?>    </td>
                    </tr>
                    <?php if ($competition->website) { ?>
                        <tr>
                            <td><?= t('Website', 'Сайт') ?></td>
                            <td title="<?= $competition->website ?>">
                                <?php unofficial\getLink($competition->website) ?>    
                            </td>
                        </tr>
                        <?php
                    }
                    $o = 0;
                    foreach ($competition->organizers as $organizer) {
                        ?>
                        <tr>
                            <td><?php if (!$o++) { ?><?= t('Organizer', 'Организатор') ?><?php } ?></td>
                            <td>
                                <i class="fas fa-user-tie"></i>
                                <?php if ($organizer->wca_id) { ?>
                                    <a target='_blank' 
                                       href='https://www.worldcubeassociation.org/persons/<?= $organizer->wca_id ?>'>
                                           <?= $organizer->name ?>
                                    </a>   
                                <?php } else { ?>
                                    <?= $organizer->name ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $s = 0;
                    foreach ($comp_data->events as $event) {
                        if ($event->special) {
                            ?>
                            <tr>
                                <td><?php if (!$s++) { ?><?= t('Special event', 'Специальные') ?><?php } ?></td>  
                                <td style='white-space:nowrap;'><i class="<?= $event->image ?>"></i> <?= $event->name ?></td>
                            </tr>
                            <?php
                        }
                    }
                    if ($competition->is_ranked) {
                        ?>
                        <tr><td colspan="2"><hr></td></tr>
                        <?php if ($competition->is_approved) { ?>
                            <tr>
                                <td><?= $ranked_icon ?> <i class="message fas fa-check"></i></td>
                                <td>
                                    <a href="<?= PageIndex() . "competitions/rankings" ?>">
                                        <?= t('Confirmed by the Speedcubing&nbsp;Federation', 'Подтверждено Федерацией&nbsp;Спидкубинга') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td><?= $ranked_icon ?></td>
                                <td>
                                    <a href="<?= PageIndex() . "competitions/rankings" ?>">
                                        <?= t('Conducted by the Speedcubing&nbsp;Federation', 'Проводится Федерацией&nbsp;Спидкубинга') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($competition->delegates ?? [] as $delegate) { ?>
                            <tr>
                                <td><?= $delegate->role ?></td>  
                                <td><i class="fas fa-signature"></i> <?= $delegate->name ?> 
                                    <?= unofficial\build_contact($delegate) ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($grand->edit or $grand->federation) { ?>
                            <tr>
                                <td><?= $wca_icon ?></td>
                                <td>
                                    <a href="<?= PageIndex() . "competitions/$competition->id/wcaid" ?>"><?= t('Binding to WCA', 'Привязка к WCA') ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <tr><td colspan="2"><hr></td></tr>     
                    <?php if ($competition->points) {
                        ?>
                        <tr>
                            <td>
                                <i class="<?= $points_dict[$competition->points]->icon ?>"></i>
                            </td>
                            <td> 
                                <a href="<?= PageIndex(). "competitions/$competition->id/points" ?>"> <?= t('Overall standings', 'Общий зачёт') ?> - <?= $points_dict[$competition->points]->name ?></a></td>
                        </tr>
                    <?php } ?>
                    <?php
                    $res = true;
                    foreach ($comp_data->events as $event_a) {
                        ?>
                        <tr>
                            <td>
                                <?php if ($res) { ?>
                                    <?= t('Results', 'Результаты'); ?>
                                    <?php
                                    $res = false;
                                }
                                ?>
                            </td>
                            <td>
                                <i class="<?= $events_dict[$event_a->event_dict]->image ?>"></i>
                                <a
                                    title="<?= $comp_data->events[$event_a->event_dict]->name ?>"
                                    href="<?= PageIndex() . "competitions/$secret/event/{$events_dict[$event_a->event_dict]->code}/1" ?> "
                                    >
                                        <?= $comp_data->events[$event_a->event_dict]->name ?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($grand->view) { ?>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <i class="fas fa-certificate"></i>
                                <a target="_blank" href="?action=certificates"><?= t('Certificates', 'Сертификаты') ?></a> pdf
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= t('Export', 'Экспорт') ?>
                            </td>
                            <td>
                                <i class="fas fa-info-circle"></i>
                                <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>"><?= t('Competition info', 'Информация') ?></a>  json
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <i class="fas fa-users"></i>
                                <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>/registrations"><?= t('Registrations', 'Регистрации') ?></a> json
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <i class="fas fa-newspaper"></i>
                                <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>/events"><?= t('Events', 'Дисциплины') ?></a> json
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <i class="fas fa-list-alt"></i>
                                <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>/results"><?= t('Results', 'Результаты') ?></a> json
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
            <?php if ($competition->details ?? '') { ?>
                <td width="60%" valign="top" align="left">
                    <?= markdown::convertToHtml($competition->details ?? ''); ?>
                </td>
            <?php } ?>
        </tr>
    </table>
<?php } ?>