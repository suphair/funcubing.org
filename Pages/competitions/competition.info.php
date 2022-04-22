<h2>
    <i title="General info" class="fas fa-info-circle"></i>
    <?= t('General info', 'Информация'); ?>
</h2>
<br>
<table width="100%" style="padding:0px; margin:0px">
    <tr>
        <?php if ($comp->logo) { ?>
            <td width="0%" valign="top">
                <img src="<?= $comp->logo ?>" width="200px" style="padding-right: 20px;"/>
            </td>
        <?php } ?>
        <td width="30%" valign="top">
            <table class="table_info">
                <?php if ($comp->my) { ?>
                    <tr>
                        <td><i class="fas fa-cog"></i></td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/$comp->secret/setting" ?>"><?= t('Setting', 'Настройки') ?></a>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($comp->my or $comp->organizer) { ?>
                    <tr>
                        <td><i class="fas fa-users-cog"></i></td>
                        <td>
                            <a href="<?= PageIndex() . "competitions/$comp->secret/registrations" ?>"><?= t('Registrations', 'Регистрации') ?></a> 
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($comp->secretRegistration and $comp->shareRegistration) { ?>
                    <tr>
                        <td><i class="fas fa-user-plus"></i></td>
                        <td>    
                            <a href="<?= PageIndex() . "competitions/$comp->secret/registration/$comp->secretRegistration" ?>">Self-registration</a>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <?php if (!$comp->show) { ?>
                        <td></td>
                        <td><i class="far fa-eye-slash"></i>
                            Private</td>
                    <?php } ?>
                </tr>
                <tr>
                    <td><?= t('Date', 'Дата') ?></td>
                    <td><i class="far fa-calendar-alt"></i>
                        <?= dateRange($comp->date, $comp->date_to) ?>    </td>
                </tr>
                <?php if ($comp->website) { ?>
                    <tr>
                        <td><?= t('Website', 'Сайт') ?></td>
                        <td>
                            <?php unofficial\getFavicon($comp->website) ?>    </td>
                    </tr>
                <?php } ?>
                <?php
                $o = 0;
                foreach (array_merge([$comp], $comp_data->organizers) as $organizer) {
                    if ($organizer->competitor_wcaid != $comp->competitor_wcaid or!$o) {
                        ?>
                        <tr>
                            <td><?php if (!$o++) { ?><?= t('Organizer', 'Организатор') ?><?php } ?></td>
                            <td>
                                <i class="fas fa-user-tie"></i>
                                <a target='_blank' 
                                   href='https://www.worldcubeassociation.org/persons/<?= $organizer->competitor_wcaid ?>'>
                                       <?= $organizer->competitor_name ?>
                                </a>   
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <?php
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
                <?php if ($comp->ranked) { ?>
                    <tr><td colspan="2"><hr></td></tr>
                    <tr>
                        <td><?= $ranked_icon ?></td>
                        <td><a href="<?= PageIndex() . "competitions/rankings" ?>"><?= t('Rankings', 'Рейтинг') ?></a></td>
                    </tr>

                    <?php
                    $judges = [];
                    if ($comp->rankedJudgeSenior_name) {
                        $judges[] = $comp->rankedJudgeSenior_name;
                    }
                    if ($comp->rankedJudgeJunior_name) {
                        $judges[] = $comp->rankedJudgeJunior_name;
                    }
                    $j = 0;
                    foreach ($judges as $judge) {
                        if ($judge) {
                            ?>
                            <tr>
                                <td><?php if (!$j++) { ?> <?= t('Judge', 'Судья') ?><?php } ?></td>  
                                <td><i class="fas fa-signature"></i> <?= $judge ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
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
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $comp->secret ?>"><?= t('Competition info', 'Информация') ?></a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <i class="fas fa-users"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $comp->secret ?>/registrations"><?= t('Registrations', 'Регистраци') ?></a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <i class="fas fa-newspaper"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $comp->secret ?>/events"><?= t('Events', 'Дисциплины') ?></a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <i class="fas fa-list-alt"></i>
                        <a target="_blank" href="<?= PageIndex() ?>api/competitions/<?= $comp->secret ?>/results"><?= t('Results', 'Результаты') ?></a>
                    </td>
                </tr>
            </table>
        </td>
        <td width="60%" valign="top" align="left">
            <?= markdown::convertToHtml($comp->details ?? ''); ?>
        </td>
    </tr>
</table>