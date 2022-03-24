<div class="shadow" >
    <?php $countEvents = sizeof(db::rows("Select id from unofficial_events where competition=" . $comp->id)); ?>
    <h1>
        <?php if (!$comp->show) { ?>
            <i class="far fa-eye-slash"></i>
        <?php } ?>
        <a href="<?= PageIndex() . "competitions/$secret" ?>"><?= $comp->name ?></a>
    </h1>
    <h2><?= $comp->details ?></h2>
    <i class="far fa-calendar-alt"></i> 
    <?= dateRange($comp->date, $comp->date_to) ?>    
    <?php unofficial\getFavicon($comp->website) ?>
    <i class="fas fa-user-tie"></i>
    <?php if ($comp->competitor_wcaid) { ?>
        <a target='_blank' 
           href='https://www.worldcubeassociation.org/persons/<?= $comp->competitor_wcaid ?>'>
            <?= $comp->competitor_name ?></a>   
    <?php } else { ?>
        <?= $comp->competitor_name ?>
    <?php } ?>
    <?php if ($comp->my) { ?>
        <i class="fas fa-cog"></i>
        <a href="<?= PageIndex() . "competitions/$comp->secret/setting" ?>">Setting</a> 
    <?php } ?>

    <?php if ($comp->my or $comp->organizer) { ?>
        <i class="fas fa-users-cog"></i>
        <a href="<?= PageIndex() . "competitions/$comp->secret/registrations" ?>">Registrations</a> 
    <?php } ?>

    <?php if ($comp->secretRegistration and $comp->shareRegistration) { ?>
        <i class="fas fa-user-plus"></i>
        <a href="<?= PageIndex() . "competitions/$comp->secret/registration/$comp->secretRegistration" ?>">Self-registration</a>
    <?php } ?>
    <?php if ($comp->ranked) { ?>
        <?= $ranked_icon ?>
        <a href="<?= PageIndex() . "competitions/rankings" ?>">FunCubing Rankings</a>
        <?php if ($comp->rankedJudgeSenior) { ?>
            <nobr>
                <i class="fas fa-signature"></i> Senior Judge  
                <b><?= $comp->rankedJudgeSenior_name ?></b>
            </nobr>
        <?php } ?>
        <?php if ($comp->rankedJudgeJunior) { ?>
            <nobr>
                <i class="fas fa-signature"></i> Judge
                <b><?= $comp->rankedJudgeJunior_name ?></b>
            </nobr>
        <?php } ?>
    <?php } ?>
    <p style='padding: 5px'></p>
    <?php ($include ?? FALSE) ? include $include : false; ?>
</div>
