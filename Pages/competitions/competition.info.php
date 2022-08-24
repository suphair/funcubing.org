<h2>
    <i title="General info" class="fas fa-info-circle"></i>
    <?= t('General info', 'Информация'); ?>
</h2>
<br>
<table width="100%" style="padding:0px; margin:0px">
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
                            <?php unofficial\getFavicon($competition->website, false) ?>    </td>
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
                ?>
                <?php if ($competition->is_ranked) { ?>
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
                    <?php foreach ($competition->judges ?? [] as $judge) { ?>
                        <tr>
                            <td><?= $judge->role ?></td>  
                            <td><i class="fas fa-signature"></i> <?= $judge->name ?> 
                                <?= unofficial\build_contact($judge) ?></td>
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
                <tr>
                    <td>
                    </td>
                    <td>
                        <i class="fas fa-certificate"></i>
                        <a target="_blank" href="?action=certificates"><?= t('Certificates', 'Сертификаты') ?></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= t('Export', 'Экспорт') ?> (json)
                    </td>
                    <td>
                        <i class="fas fa-info-circle"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>"><?= t('Competition info', 'Информация') ?></a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <i class="fas fa-users"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>/registrations"><?= t('Registrations', 'Регистрации') ?></a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <i class="fas fa-newspaper"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>/events"><?= t('Events', 'Дисциплины') ?></a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <i class="fas fa-list-alt"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $competition->id ?>/results"><?= t('Results', 'Результаты') ?></a>
                    </td>
                </tr>
            </table>
        </td>
        <?php if ($competition->details ?? '') { ?>
            <td width="60%" valign="top" align="left">
                <?= markdown::convertToHtml($competition->details ?? ''); ?>
            </td>
        <?php } ?>
    </tr>
</table>