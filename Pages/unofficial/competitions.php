<div class="shadow2" >
    <?php if (!$me) { ?>    
        <h3>
            <i class="error far fa-hand-paper"></i> 
            To create unofficial competition you need to sign in with WCA.
        </h3>
    <?php } else { ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script>
            $(function () {
                $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
            });
        </script>

        <form method="POST" action="?create">
            <b>Create unofficial competition</b> 
            <input required placeholder="RamenskoeMeeting #1" type="text" name="name" value="" />
            <input style="width:140px" placeholder="Select date" required type="text" id="datepicker" name="date">
            <button>
                <i class="fas fa-plus-circle"></i> 
                Create
            </button>
        </form>
        <i class="fas fa-info-circle"></i> Competitions is created privately.
        You can make them public later in the settings.
        Or leave them hidden for your testing or fun.
    <?php } ?>
</div>

<div class="shadow" >
    <?php $mine = ($me and filter_input(INPUT_GET, 'show') == 'mine'); ?>
    <h2>
        <?php if ($mine) { ?>
            My Unofficial Competitions
        <?php } else { ?>
            Public Unofficial Competitions
        <?php } ?>
    </h2>
    <?php if ($mine) { ?>
        <p>
            <i class="far fa-eye"></i>
            <a href="?show=all">
                Show all
            </a>
        </p>
    <?php } elseif ($me) { ?>
        <p>
            <i class="fas fa-crown"></i>
            <a href="?show=mine">

                Show only mine
            </a>
        </p>
    <?php } ?>
    <?php $competitions = unofficial\getCompetitions($me, $mine); ?>
    <table class='table_new'>
        <thead>
            <tr>
                <td/>
                <td/>
                <td/>
                <td>
                    Organizer
                </td>
                <td>
                    Competition
                </td>
                <td>
                    Date
                </td>
                <td>
                    Web site
                </td>
            </tr>    
        </thead>
        <tbody>
            <?php foreach ($competitions as $competition) { ?>
                <tr>   
                    <td>
                        <?php if (!$competition->show) { ?>
                            <i class="far fa-eye-slash"></i>
                        <?php } ?>
                    </td>
                    <td align="center" >
                        <?php if ($competition->my) { ?>
                            <i class="far fa-crown"></i>
                        <?php } elseif ($competition->organizer) { ?>
                            <i class="fas fa-user-tie"></i>
                        <?php } ?>
                    </td>

                    <td>
                        <span class='flag-icon flag-icon-<?= strtolower($competition->competitor_country) ?>'></span>
                    </td>
                    <td>
                        <?= $competition->competitor_name ?>
                    </td>   
                    <td>                    
                        <a href="<?= PageIndex()?>unofficial/<?= $competition->secret ?>"><?= $competition->name ?> </a>
                    </td>
                    <td>
                        <?= dateRange($competition->date) ?>
                    </td>
                    <td>
                        <?php if ($competition->website) { ?>
                            <?php preg_match('/https?:\/\/(?:www\.|)([\w.-]+).*/', $competition->website, $matches); ?>
                            <?php if (isset($matches[1])) { ?>
                                <img src="<?= unofficial\getFavicon($matches[1]) ?>">
                                <a target="_blank" href="<?= $competition->website ?>">
                                    <?= $matches[1] ?>
                                </a> 
                            <?php } ?>        
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>  
</div>