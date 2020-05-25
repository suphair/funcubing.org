<?php


$competitor=GetCompetitorData();
$competitions=[];
if($competitor and $competitor->wca_id){
    DataBaseClass::Query("Select * from Friend where CompetitorWCAID='".$competitor->wca_id."' order by FriendWCAID");
    $Friends=[];
    foreach(DataBaseClass::getRows() as $r){
        $Friends[]=$r['FriendWCAID'];
    }
    
    
    foreach(array_merge([$competitor->wca_id],$Friends) as $u=>$competitor_row){
        $UserData=GetUpcomingCompetition($competitor_row);
        $FriendsData[$u]=$UserData['user'];
        $FriendsData[$u]['count']=0;
        if(isset($UserData['upcoming_competitions'])){
            $UpcomingCompetitions=$UserData['upcoming_competitions']; 
            if($UpcomingCompetitions)foreach($UpcomingCompetitions as $UpcomingCompetition){ 
                $FriendsData[$u]['count']++;
                $competitions[$UpcomingCompetition['id']]['competition']=$UpcomingCompetition; 
                $competitions[$UpcomingCompetition['id']]['users'][]=$UserData['user'];
            }
        }
    } 
    usort($competitions,'SortUpcomingCompetitionByDate');
    
    ?>
    <h1>Your friends</h1>
    <?php foreach($FriendsData as $user){?>
        <div class='form form_mini' style=" <?= $user['wca_id']==$competitor->wca_id?'border-color:var(--red)':'' ?>">
            <table witdh='100%'><tr>      
                <?php if(!$user['avatar']['is_default']){ ?>
                    <td style="border-bottom:0px">    
                        <img style="float:left;padding:5px;" src='<?= $user['avatar']['thumb_url'];?>'>
                    </td>
                <?php } ?>
                <td style="border-bottom:0px">    
                    <b><?= Short_Name($user['name']);?></b>
                    <br>
                    <nobr><span class='badge'><?= $user['count'] ?></span>&nbsp;<a href='<?= $user['url'];?>' target='_blank'><?= $user['wca_id'];?></a></nobr>
                    <br>
                    <span class='flag-icon flag-icon-<?= strtolower($user['country_iso2']) ?>'></span>
                    
                    <?= CountryName($user['country_iso2']); ?>
                    <?php if($user['delegate_status']){ ?>
                            <p class="message">Delegate</p>
                    <?php } ?>
                    <?php if($user['wca_id']!=$competitor->wca_id){?>        
                            <form method="POST" action="<?= PageIndex()."Actions/FriendRemove" ?>" onsubmit="return confirm('Attention: Remove [<?= $user['name']?>] from your friends?')">
                                <input type='hidden' name="CompetitorWCAID" value="<?= $competitor->wca_id?>">
                                <input type="hidden" name="FriendWCAID" value="<?= $user['wca_id'];?>">
                                <input type="submit" class="delete form_row" value="X">
                            </form>
                    <?php } ?>   
                 </td>       
             </tr></table>       
        </div> 
    <?php } ?>
    <div class='form'>
            <form method="POST" action="<?= PageIndex()."Actions/FriendAdd" ?>">
                   <div class="form_field">
                       Enter your friend's WCA ID
                    </div>   
                    <div class="form_enter">
                        <input type='hidden' name="CompetitorWCAID" value="<?= $competitor->wca_id?>">
                        <input autocomplete="false" type="input" placeholder="WCA ID" name="FriendWCAID" value="<?= GetMessage('AddFriend_FriendWCAID') ?>">
                    </div>  
                    <p class='error'>
                        <?= GetMessage("AddFriend")?>
                    </p>
                   <input type="submit" class="form_row" value="Add friend">
            </form>
        </div> 
    <hr class="hr_round">
    <h1>Friends' competitions</h1>
    <hr>
    <?php foreach($competitions as $competition){?>
        <h2>    
            <?= date_range($competition['competition']['start_date'],$competition['competition']['end_date']); ?>
            &#9642; <?= $competition['competition']['name']; ?>
        </h2>
        <h3>
            <img width="30" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competition['competition']['country_iso2'])?>.png">
            <?= CountryName($competition['competition']['country_iso2']); ?>,
            <?= $competition['competition']['city']; ?>
             &#9642; <a href='<?= $competition['competition']['url']; ?>' target='_blank'>WCA</a>
        </h3>
        <?php foreach($competition['users'] as $user){ ?>
        <div class='form' style="height: 144px; <?= $user['wca_id']==$competitor->wca_id?'border-color:var(--red)':'' ?>">
            <?php if(!$user['avatar']['is_default']){ ?>
                <img style="float:left;padding:5px;" src='<?= $user['avatar']['thumb_url'];?>'>
            <?php } ?>
                <b><?= Short_Name($user['name']);?></b>
            <br>
            <a href='<?= $user['url'];?>' target='_blank'><?= $user['wca_id'];?></a>
            <br>
            <img width="20" src="<?= PageIndex()?>Image/Flags/<?= strtolower($user['country_iso2'])?>.png">
            <?= CountryName($user['country_iso2']); ?>
            <?php foreach($competition['competition']['delegates'] as $delegate){
                if($delegate['wca_id']==$user['wca_id']){ ?>
                    <p class="message">Delegate</p>
                <?php }
            } ?>
            <?php foreach($competition['competition']['organizers'] as $organizer){
                if($organizer['wca_id']==$user['wca_id']){ ?>
                    <p class="error">Organizer</p>
                <?php }
            } ?>
        </div>        
        <?php } ?>
        <hr>
    <?php }
}else{ ?>    
        <div class="form">
            <span class="error">
                <?php if($competitor){ ?>
                    You don't have WCAID, so you don't have friends 
                <?php }else{ ?>    
                    <?php $_SESSION['Refer']=$_SERVER['REQUEST_URI'];  ?>    
                    To see your friends’ competitions you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a>
                <?php } ?>        
            </span> 
        </div> 
<?php } ?>
