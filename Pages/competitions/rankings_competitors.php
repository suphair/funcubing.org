<?php $competitors = unofficial\getRankedCompetitors(); ?>
<h2>
    <i title='Competitors' class="fas fa-users"></i>
    <?= t('Competitors', 'Участники') ?> (<?= count($competitors) ?>)
</h2>
<table class="table thead_stable">
    <thead>
        <tr>
            <th><?= t('Name', 'Имя') ?></th>
            <th><?= t('Competitions', 'Соревнования') ?></th>
            <th>WCA ID <i class="fas fa-external-link-alt"></i></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($competitors as $competitor) { ?>
            <tr>
                <td>
        <nobr>
            <a href="<?= PageIndex() . "competitions/rankings/competitor/$competitor->FCID" ?>">
                <?php if ($competitor->name != $competitor->name_other) { ?>
                    <?= $competitor->name ?> (<?= $competitor->name_other ?>)
                <?php } else { ?>    
                    <?php if (t(true, false)) { ?>
                        <?= transliterate($competitor->name) ?> <?= $competitor->name ?>)
                    <?php } else { ?>
                        <?= $competitor->name ?> (<?= transliterate($competitor->name) ?>)
                    <?php } ?>
                <?php } ?>
            </a>
        </nobr>
    </td>
    <td align="center">
        <?php
        $competitions = [];
        foreach (explode(',', $competitor->competitions_secret) as $c => $secret) {
            if (!in_array($secret, explode(",", config::get('MISC', 'competition_exclude_secret')))) {
                $competitions[] = "<a href='" . PageIndex() . "/competitions/$secret'>" . (explode(',', $competitor->competitions_name)[$c] ?? '???') . "</a>";
            }
        }
        ?>
    <nobr><?= implode(',</nobr> <nobr>', $competitions) ?></nobr>
    </td>
    <td>
        <?php if ($competitor->wcaid) { ?>
            <a target='_blank' href='https://www.worldcubeassociation.org/persons/<?= $competitor->wcaid ?>'>
                <?= $competitor->wcaid ?>
            </a>
        <?php } elseif ($competitor->nonwca) { ?>
            <?= t('none', 'нет') ?>
        <?php } ?>
    </td>
    </tr>
<?php } ?>
</tbody>
</table>
