<div class="shadow" >
    <?php $countEvents = sizeof(db::rows("Select id from unofficial_events where competition=" . $comp->id)); ?>
    <h1>
        <?php if (!$comp->show) { ?>
            <i class="far fa-eye-slash"></i>
        <?php } ?>
        <span class='flag-icon flag-icon-<?= strtolower($comp->competitor_country) ?>'></span>
        <a href="<?= PageIndex() . "unofficial/$secret" ?>"><?= $comp->name ?></a>

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
        <a href="<?= PageIndex() . "unofficial/$comp->secret/setting" ?>">Setting</a> 
    <?php } ?>

    <?php if ($comp->my or $comp->organizer) { ?>
        <i class="fas fa-users-cog"></i>
        <a href="<?= PageIndex() . "unofficial/$comp->secret/registrations" ?>">Registrations</a> 
    <?php } ?>

    <?php if ($comp->secretRegistration and $comp->shareRegistration) { ?>
        <i class="fas fa-user-plus"></i>
        <a href="<?= PageIndex() . "unofficial/$comp->secret/registration/$comp->secretRegistration" ?>">Self-registration</a>
    <?php } ?>
    <h3>
        <?php foreach ($comp_data->events as $event) { ?>
            <?php if ($events_dict[$event->event_dict]->special) { ?>
                <p>
                    <i class="<?= $events_dict[$event->event_dict]->image ?>"></i>
                    <?= $event->name ?>
                </p>
            <?php } ?>
        <?php } ?>
    </h3>
    <br>
    <?php ($include ?? FALSE) ? include $include : false; ?>
</div>
