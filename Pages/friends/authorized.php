<div class="shadow" >
    <?php
    $rows = db::exec("SELECT friend FROM friends WHERE user='{$me->wca_id}'");
    $friends = [];
    foreach ($rows as $row) {
        $friends[$row['friend']] = [];
    }
    $friends[$me->wca_id] = [];

    $competitions = [];
    foreach ($friends as $wcaid => &$friend) {
        $user = wcaapi::getUserCompetitionsUpcoming($wcaid, __FILE__, false);
        $friend['user'] = $user->user;
        $friend['competitions'] = [];
        foreach ($user->upcoming_competitions ?? [] as $competition) {
            $friend['competitions'][] = $competition;
            $competitions[$competition->id]['competition'] = $competition;
            $competitions[$competition->id]['users'][] = $user->user;
        }
    }
    unset($friend);

    usort($friends, function($a, $b) {
        return strcmp($a['user']->name, $b['user']->name);
    });

    usort($competitions, function($a, $b) {
        $a_start = $a['competition']->start_date;
        $b_start = $b['competition']->start_date;
        $a_end = $a['competition']->end_date;
        $b_end = $b['competition']->end_date;

        return $a_start != $b_start ?
                $a_start > $b_start :
                $a_end > $b_end;
    });
    ?>
    <h2>Your friends</h2>
    <table class="table_new">
        <tr>
            <td colspan="2" align='right'>
                <b>
                    Enter WCA ID your friend 
                </b>
                <p style="color:red">
                    <?= postGet('error') ?>
                </p>
                <p style="color:green">
                    <?= postGet('message') ?>
                </p>
            </td>
            <td>    
                <form ID="FriendAdd" method="POST" action="?Add">    
                    <input type='hidden' name="user" value="<?= $me->wca_id ?>">                
                    <input required autocomplete="false" type="input" placeholder="WCA ID" name="friend" value="<?= postGet('friend') ?>">  
                </form>
            </td>
            <td>
                <a onclick="$('#FriendAdd').submit(); return false;" href="#" style="color:var(--green)">
                    <i class="fas fa-user-plus"></i>
                    add
                </a>
            </td>
        </tr>
        <?php foreach ($friends as $user) { ?>
            <tr>      
                <td>
                    <span class='flag-icon flag-icon-<?= strtolower($user['user']->country_iso2) ?>'></span>
                    <a href='<?= $user['user']->url; ?>' target='_blank'>
                        <?= $user['user']->name ?>
                    </a>
                    <?php if ($user['user']->delegate_status) { ?>
                        <i class="fas fa-user-tie"></i>
                    <?php } ?>
                <td>
                    <?php foreach ($user['competitions'] as $competition) { ?>
                        <p>
                            <span class='flag-icon flag-icon-<?= strtolower($competition->country_iso2) ?>'></span>
                            <b>
                                <?= dateRange($competition->start_date, $competition->end_date); ?>
                            </b>
                            <a href="https://www.worldcubeassociation.org/competitions/<?= $competition->id ?>" target="_blank"> 
                                <?= $competition->name ?>
                            </a>
                        </p>                
                    <?php } ?>

                </td>
                <td>
                    <?= $user['user']->wca_id ?>
                </td>    
                <td>
                    <?php if ($user['user']->wca_id != $me->wca_id) { ?>        
                        <form method="POST" action="?Remove" onsubmit="return confirm('Attention: Remove [<?= $user['user']->name ?>] from your friends?')">
                            <input type="hidden" name="friend" value="<?= $user['user']->wca_id; ?>">
                            <a href="#" style='color:var(--red)' onclick='$(this).parent().submit(); return false;'>
                                remove
                            </a>
                        </form>
                    <?php } ?>   
                </td>       
            </tr>
        <?php } ?>
    </table>       
</div> 

<div class="shadow" >    
    <h2>Upcoming friends' competitions</h2>
    <table class='table_new'>
        <?php foreach ($competitions as $competition) { ?>
            <tr>
                <td>    
                    <span class='flag-icon flag-icon-<?= strtolower($competition['competition']->country_iso2) ?>'></span>
                    <b>
                        <?= dateRange($competition['competition']->start_date, $competition['competition']->end_date); ?>
                    </b>
                    <a href='<?= $competition['competition']->url; ?>' target='_blank'>
                        <?= $competition['competition']->name; ?>
                    </a>
                </td>
                <td>
                    <?php foreach ($competition['users'] as $user) { ?>
                        <p>
                            <span class='flag-icon flag-icon-<?= strtolower($user->country_iso2) ?>'></span>
                            <a href='<?= $user->url; ?>' target='_blank'>
                                <?= $user->name ?>
                            </a>
                            <?php
                            foreach ($competition['competition']->delegates ?? [] as $delegate) {
                                if ($delegate->wca_id == $user->wca_id) {
                                    ?>
                                    <i class="fas fa-user-tie"></i>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            foreach ($competition['competition']->organizers ?? [] as $organizer) {
                                if ($organizer->wca_id == $user->wca_id) {
                                    ?>
                                    <i class="fas fa-user-alt"></i>
                                    <?php
                                }
                            }
                            ?>
                            </div>        
                        <?php } ?>
                </td>
                <td>
                    <b><?= countyName($competition['competition']->country_iso2); ?></b>,
                    <?= $competition['competition']->city ?>
                </td>
            </tr>
        <?php }
        ?>
    </table>
</div>