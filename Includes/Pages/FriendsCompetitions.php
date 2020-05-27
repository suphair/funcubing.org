<div class="shadow" >
    <?php
    $competitor = GetCompetitorData();
    $competitions = [];
    if ($competitor and $competitor->wca_id) {
        DataBaseClass::Query("
                Select * 
                FROM Friend
                WHERE CompetitorWCAID='{$competitor->wca_id}'");
        $Friends = [];
        foreach (DataBaseClass::getRows() as $r) {
            $Friends[] = $r['FriendWCAID'];
        }

        foreach (array_merge([$competitor->wca_id], $Friends) as $u => $competitor_row) {
            $UserData = GetUpcomingCompetition($competitor_row);
            $FriendsData[$u] = $UserData['user'];
            $FriendsData[$u]['count'] = 0;
            $FriendsData[$u]['competitions'] = [];
            if (isset($UserData['upcoming_competitions'])) {
                $UpcomingCompetitions = $UserData['upcoming_competitions'];
                if ($UpcomingCompetitions)
                    foreach ($UpcomingCompetitions as $UpcomingCompetition) {
                        $FriendsData[$u]['competitions'][] = $UpcomingCompetition;
                        $competitions[$UpcomingCompetition['id']]['competition'] = $UpcomingCompetition;
                        $competitions[$UpcomingCompetition['id']]['users'][] = $UserData['user'];
                    }
            }
        }

        usort($FriendsData, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });


        usort($competitions, 'SortUpcomingCompetitionByDate');
        ?>
        <h2>Your friends</h2>
        <table class="table_new">
            <tr>
                <td colspan="2" align='right'>
                    <b>
                        Enter WCA ID your friend 
                    </b>
                    <p style="color:var(--red)">
                        <?= GetMessage("AddFriendError") ?>
                    </p>
                    <p style="color:var(--green)">
                        <?= GetMessage("AddFriendMessage") ?>
                    </p>
                </td>
                <td>    
                    <form ID="FriendAdd" method="POST" action="<?= PageIndex() . "Actions/FriendAdd" ?>">    
                        <input type='hidden' name="CompetitorWCAID" value="<?= $competitor->wca_id ?>">                
                        <input required autocomplete="false" type="input" placeholder="WCA ID" name="FriendWCAID" value="<?= GetMessage('AddFriend_FriendWCAID') ?>">  
                    </form>
                </td>
                <td>
                    <a onclick="$('#FriendAdd').submit(); return false;" href="#" style="color:var(--green)">
                        <i class="fas fa-user-plus"></i>
                        add
                    </a>
                </td>
            </tr>
            <?php foreach ($FriendsData as $user) { ?>
                <tr>      
                    <td>
                        <span class='flag-icon flag-icon-<?= strtolower($user['country_iso2']) ?>'></span>
                        <a href='<?= $user['url']; ?>' target='_blank'>
                            <?= Short_Name($user['name']); ?>
                        </a>
                        <?php if ($user['delegate_status']) { ?>
                            <i class="fas fa-user-tie"></i>
                        <?php } ?>
                    <td>
                        <?php foreach ($user['competitions'] as $competition) { ?>
                            <p>
                                <span class='flag-icon flag-icon-<?= strtolower($competition['country_iso2']) ?>'></span>
                                <b>
                                    <?= date_range($competition['start_date'], $competition['end_date']); ?>
                                </b>
                                <a href="https://www.worldcubeassociation.org/competitions/<?= $competition['id'] ?>" target="_blank"> 
                                    <?= $competition['name'] ?>
                                </a>
                            </p>                
                        <?php } ?>

                    </td>
                    <td>
                        <?= $user['wca_id']; ?>
                    </td>    
                    <td>
                        <?php if ($user['wca_id'] != $competitor->wca_id) { ?>        
                            <form method="POST" action="<?= PageIndex() . "Actions/FriendRemove" ?>" onsubmit="return confirm('Attention: Remove [<?= $user['name'] ?>] from your friends?')">
                                <input type='hidden' name="CompetitorWCAID" value="<?= $competitor->wca_id ?>">
                                <input type="hidden" name="FriendWCAID" value="<?= $user['wca_id']; ?>">
                                <a href="#" style='color:var(--red)' onclick='$(this).parent().submit();'>
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
                        <span class='flag-icon flag-icon-<?= strtolower($competition['competition']['country_iso2']) ?>'></span>
                        <b>
                            <?= date_range($competition['competition']['start_date'], $competition['competition']['end_date']); ?>
                        </b>
                        <a href='<?= $competition['competition']['url']; ?>' target='_blank'>
                            <?= $competition['competition']['name']; ?>
                        </a>
                    </td>
                    <td>
                        <?php foreach ($competition['users'] as $user) { ?>
                            <p>
                                <span class='flag-icon flag-icon-<?= strtolower($user['country_iso2']) ?>'></span>
                                <a href='<?= $user['url']; ?>' target='_blank'>
                                    <?= Short_Name($user['name']); ?>
                                </a>
                                <?php
                                foreach ($competition['competition']['delegates'] as $delegate) {
                                    if ($delegate['wca_id'] == $user['wca_id']) {
                                        ?>
                                        <i class="fas fa-user-tie"></i>
                                        <?php
                                    }
                                }
                                ?>
                                <?php
                                foreach ($competition['competition']['organizers'] as $organizer) {
                                    if ($organizer['wca_id'] == $user['wca_id']) {
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
                        <b><?= CountryName($competition['competition']['country_iso2']); ?></b>,
                        <?= $competition['competition']['city']; ?>
                    </td>
                </tr>
            <?php }
            ?>
        </table>
    <?php } else {
        ?>
        <h3 class="error" style="padding:20px 0px;">
            <i class="far fa-hand-paper"></i> 
            We don't know your WCA ID, so you don't have any friends.
        </h3>
    <?php } ?>
</div>

<?php
DataBaseClass::Query("Select distinct CompetitorWCAID from Friend");
$count = count(DataBaseClass::getRows());
?>
<p>
    <i class="fas fa-info-circle"></i>  
    Total competitors with friends: <?= $count ?>
</p>
