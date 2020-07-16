<?php
$partners = unofficial\getPartners($organizer_id);

$organizers = unofficial\getOrganizers();

$rows = db::rows("select 
unofficial_competitors.id,
coalesce(unofficial_competitors_result.average, unofficial_competitors_result.mean) average,
unofficial_competitors_result.attempt1,
unofficial_competitors_result.attempt2,
unofficial_competitors_result.attempt3,
unofficial_competitors_result.attempt4,
unofficial_competitors_result.attempt5,
`unofficial_competitors`.name competitor_name,
`unofficial_events_dict`.id event_id,
unofficial_competitions.name competition_name,
unofficial_competitions.date competition_date,
unofficial_competitions.secret,
unofficial_events_rounds.round round,
unofficial_competitions.competitor organizer,
order_average
from `unofficial_competitors_result`
join `unofficial_competitors_round` on `unofficial_competitors_round`.id = `unofficial_competitors_result`.competitor_round
join `unofficial_competitors` on `unofficial_competitors`.id = `unofficial_competitors_round`.competitor
join `unofficial_events_rounds` on `unofficial_events_rounds`.id = `unofficial_competitors_round`.round
join `unofficial_events` on `unofficial_events`.id = `unofficial_events_rounds`.event
join `unofficial_competitions` on `unofficial_competitions`.id = `unofficial_events`.competition
join `unofficial_events_dict` on `unofficial_events_dict`.id = `unofficial_events`.event_dict
left outer join unofficial_partners on unofficial_partners.partner = unofficial_competitions.competitor
where `unofficial_events_dict`.special = 0 and order_average != 1999998 and `unofficial_competitions`.show = 1
and ( unofficial_competitions.competitor = $organizer_id or unofficial_partners.competitor = $organizer_id)
order by unofficial_events_dict.order, order_average, `unofficial_competitors`.name ");

$records_average = [];
foreach ($rows as $row) {
    $records_average[$row->event_id] ??= [];
    $records_average[$row->event_id][$row->competitor_name] ??= $row;
}

$rows = db::rows("select 
unofficial_competitors.id,
unofficial_competitors_result.best single,
`unofficial_competitors`.name competitor_name,
`unofficial_events_dict`.id event_id,
unofficial_competitions.name competition_name,
unofficial_competitions.date competition_date,
unofficial_competitions.secret,
unofficial_events_rounds.round round,
unofficial_competitions.competitor organizer,
order_average
from `unofficial_competitors_result`
join `unofficial_competitors_round` on `unofficial_competitors_round`.id = `unofficial_competitors_result`.competitor_round
join `unofficial_competitors` on `unofficial_competitors`.id = `unofficial_competitors_round`.competitor
join `unofficial_events_rounds` on `unofficial_events_rounds`.id = `unofficial_competitors_round`.round
join `unofficial_events` on `unofficial_events`.id = `unofficial_events_rounds`.event
join `unofficial_competitions` on `unofficial_competitions`.id = `unofficial_events`.competition
join `unofficial_events_dict` on `unofficial_events_dict`.id = `unofficial_events`.event_dict
left outer join unofficial_partners on unofficial_partners.partner = unofficial_competitions.competitor
where `unofficial_events_dict`.special = 0 and order_best != 999999 and `unofficial_competitions`.show = 1
and ( unofficial_competitions.competitor = $organizer_id or unofficial_partners.competitor = $organizer_id)
order by unofficial_events_dict.order,order_best, `unofficial_competitors`.name ");

$records_single = [];
foreach ($rows as $row) {
    $records_single[$row->event_id] ??= [];
    $records_single[$row->event_id][$row->competitor_name] ??= $row;
}
?>

<div class="shadow" >
    <h1>
        <i class="fas fa-signal fa-rotate-90"></i>
        <a href="<?= PageIndex() ?>unofficial/rankings">
            Rankings
        </a>
        / <?= $organizer->name ?>
    </h1>
    <?php if (sizeof($partners)) { ?>
        <br><h3>
            Included rankings of competitors of competitions of other organizers:
        </h3>  
        <?php foreach ($partners as $wid => $partner) { ?>
            <nobr>
                <b><?= $organizers[$wid]->short_name ?></b>
                <a href="<?= PageIndex() ?>unofficial/rankings/<?= $wid ?>"><?= $partner->name ?></a>,
            </nobr>
        <?php } ?>
    <?php } ?>
    <?php if ($organizer_id == (wcaoauth::me()->id ?? null)) { ?>
        <nobr>
            <a href="#" data-organizers-add-link>
                <i class="fas fa-user-plus"></i>
                Add organizers in your rankings
            </a>
        </nobr>
    <?php } ?>
    <?php if ($organizer_id == (wcaoauth::me()->id ?? null)) { ?>
        <div class="shadow2" data-organizers-add-block hidden>
            <h2>
                <i class="fas fa-users"></i>
                Add organizers in your rankings
            </h2>
            <form method='POST' action='?rankings_organizers_add'>
                <select ID="countries-chosen-select" Name="partners[]" class="chosen-select" multiple>
                    <?php
                    foreach ($organizers as $organizer_row) {
                        if ($organizer_row->wid != $organizer_id) {
                            ?>
                            <option <?= ($partners[$organizer_row->wid] ?? FALSE) ? 'selected' : '' ?> value='<?= $organizer_row->wid ?>'>
                                <?= $organizer_row->name ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <button>
                    <i class="far fa-save"></i>
                    Save
                </button>
            </form>
        </div>       
    <?php } ?>
    <div class="shadow2">
        <h2>
            Average
            <?php foreach ($records_average as $event => $competitors) { ?>
                <a href='#' data-event-code-select='avg_<?= $event ?>' <?= $event == array_key_first($records_average) ? 'class="select"' : '' ?>><i class='<?= $events_dict[$event]->image ?>'></i></a>
            <?php } ?>
            Single  
            <?php foreach ($records_single as $event => $competitors) { ?>
                <a href='#' data-event-code-select='single_<?= $event ?>'><i class='<?= $events_dict[$event]->image ?>'></i></a>
            <?php } ?>
        </h2>

        <?php foreach ($records_average as $event => $competitors) { ?>
            <div data-event-code ='avg_<?= $event ?>' <?= $event != array_key_first($records_average) ? 'hidden' : '' ?>>
                <h1>
                    <i class='<?= $events_dict[$event]->image ?>'></i>
                    <?= $events_dict[$event]->name ?> / Average
                </h1>
                <table class='table_new'>
                    <thead>
                        <tr>
                            <td>

                            </td>
                            <td>
                                Name
                            </td>
                            <td class='attempt'>
                                Average
                            </td>
                            <td align='center' colspan='5'>
                                Solves
                            </td>
                            <td>
                                Competition
                            </td>
                            <?php if (sizeof($partners)) { ?>
                                <td>
                                    <i class="fas fa-user-tie"></i>
                                </td>
                            <?php } ?>
                        </tr>   
                    </thead>    
                    <tbody>
                        <?php foreach ($competitors as $competitor => $result) { ?>
                            <tr>
                                <td data-event-place="<?= $result->average ?>">
                                </td>
                                <td>
                                    <a href='<?= PageIndex() ?>unofficial/competitor/<?= $result->id ?>'>
                                        <?= $competitor ?>
                                    </a>
                                </td>
                                <td class='attempt'>
                                    <b><?= $result->average ?></b>
                                </td>
                                <?php foreach (range(1, 5) as $i) { ?>
                                    <td class='attempt'>
                                        <?= $result->{"attempt$i"} ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <a href='<?= PageIndex() ?>unofficial/<?= $result->secret ?>/event/<?= $events_dict[$event]->code ?>/<?= $result->round ?>'>
                                        <?= $result->competition_name ?>
                                    </a>
                                </td>
                                <?php if (sizeof($partners)) { ?>
                                    <td style='<?= $result->organizer == $organizer_id ? 'font-weight:bold' : '' ?>'>
                                        <span title='<?= $organizers[$result->organizer]->name ?>'>
                                            <?= $organizers[$result->organizer]->short_name ?>
                                        </span>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        <?php foreach ($records_single as $event => $competitors) { ?>
            <div data-event-code ='single_<?= $event ?>' hidden>
                <h1>
                    <i class='<?= $events_dict[$event]->image ?>'></i>
                    <?= $events_dict[$event]->name ?> / Single
                </h1>
                <table class='table_new'>
                    <thead>
                        <tr>
                            <td>
                            </td>
                            <td>
                                Name
                            </td>
                            <td class='attempt'>
                                Single
                            </td>
                            <td>
                                Competition
                            </td>
                            <?php if (sizeof($partners)) { ?>
                                <td>
                                    <i class="fas fa-user-tie"></i>
                                </td>
                            <?php } ?>
                        </tr>   
                    </thead>    
                    <tbody>
                        <?php foreach ($competitors as $competitor => $result) { ?>
                            <tr>
                                <td data-event-place="<?= $result->single ?>">
                                </td>
                                <td>
                                    <a href='<?= PageIndex() ?>unofficial/competitor/<?= $result->id ?>'>
                                        <?= $competitor ?>
                                    </a>
                                </td>
                                <td class='attempt'>
                                    <b><?= $result->single ?></b>
                                </td>
                                <td>
                                    <a href='<?= PageIndex() ?>unofficial/<?= $result->secret ?>/event/<?= $events_dict[$event]->code ?>/<?= $result->round ?>'>
                                        <?= $result->competition_name ?>
                                    </a>
                                </td>
                                <?php if (sizeof($partners)) { ?>
                                    <td style='<?= $result->organizer == $organizer_id ? 'font-weight:bold' : '' ?>'>
                                        <span title='<?= $organizers[$result->organizer]->name ?>'>
                                            <?= $organizers[$result->organizer]->short_name ?>
                                        </span>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
    <script src="<?= PageLocal() ?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>
    <script>
<?php include 'rankings.organizer.js' ?>
    </script>