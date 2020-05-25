<?php
function R($int){
    if($int>0){
        $minute= (int)floor($int/60/100);
        $second= (int)floor(($int-$minute*60*100)/100);
        $milisecond=(int)($int-$minute*60*100-$second*100);
        if($minute){
            $str= sprintf( "%d:%02d.%02d", $minute,$second,$milisecond);
        }elseif($second){
            $str= sprintf( "%2d.%02d", $second,$milisecond);
        }else{
            $str= sprintf( "0.%02d", $milisecond);
        }
    }else{
        $str='-';
    }
    return $str;   
}

function GetPersonalRecords($WCAID){
    $data=GetValue('persons_'.$WCAID,true);
    if(!$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/persons/".$WCAID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);

        SaveValue('persons_'.$WCAID, $data);
        $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $records=[];
        if($status==200){
            $records=json_decode($data,true); 
        }
        curl_close($ch);
    }else{
        $records=json_decode($data,true); 
    }
    return $records;
}

function GetRecords(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/records");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $records=[];
    if($status==200){
        $records=json_decode($data,true);
    }
    curl_close($ch);
    return $records;
}

$RECORDS=[];

$competitor=GetCompetitorData();
if($competitor and $competitor->wca_id){

    $WCAID=0;
    if(!isset($_GET['config']) or !CheckAchievementGrand()){
        $WCAID=false;
        if(isset($_GET['WCA_ID']) and isset($_GET['test']) and $_GET['WCA_ID']){
            $WCAID=$_GET['WCA_ID'];
        }elseif($competitor=GetCompetitorData() and $competitor->wca_id){
            $WCAID=$competitor->wca_id;
        }
        if(!$WCAID)exit();
        $personal_records=GetPersonalRecords($WCAID);
        //$records=GetRecords();
    }

    $Achievements=[];
    $Achievements_Complete=[];
    DataBaseClass::Query(" Select * from AchievementGroup AG");
    foreach(DataBaseClass::getRows() as $achievement_group){
        $ID=$achievement_group['ID'];
        unset($achievement_group['ID']);
        $Achievements[$ID]=['Achievements'=>[],'AchievementGroup'=>$achievement_group];
    }
    DataBaseClass::Query(" Select * from Achievement A order by Rank");
    foreach(DataBaseClass::getRows() as $achievement){
        $ID=$achievement['ID'];
        $Group=$achievement['Group'];
        unset($achievement['ID']);
        unset($achievement['Group']);
        $Achievements[$Group]['Achievements'][$ID]=$achievement;
        $Achievements[$Group]['Achievements'][$ID]['AchievementGoals']=[];
    }

    DataBaseClass::Query(" Select AG.*, A.Group from AchievementGoal AG join Achievement A on A.ID=AG.Achievement");
    foreach(DataBaseClass::getRows() as $achievement_goal){
        $ID=$achievement_goal['ID'];
        $Group=$achievement_goal['Group'];
        $Achievement=$achievement_goal['Achievement'];
        unset($achievement_goal['ID']);
        unset($achievement_goal['Group']);
        unset($achievement_goal['Achievement']);

        $Achievements[$Group]['Achievements'][$Achievement]['AchievementGoals'][$ID]=$achievement_goal;
    }

          //ClearData
    if(isset($_GET['config']) and CheckAchievementGrand()){
        include 'AchievementsConfig.php';
    }else{ 
        foreach($Achievements as $AGroup=>$achievement_groups){
            foreach($achievement_groups['Achievements'] as $A=>$achievement_goals){
                $And=true;
                $Sum=0;
                $SumFl=true;
                $Or=false;

                $Total=json_decode($achievement_goals['Total'],true);
                unset($achievement_goals['Total']);
                if($Total){
                    $achievement_goals['Total']=$Total;
                }

                foreach($achievement_goals['AchievementGoals'] as $AGoal=>$achievement_goal){  
                    $Condition=json_decode($achievement_goal['Condition'],true);       
                    unset($achievement_goal['Condition']);
                    if($Condition){
                        $achievement_goal['Condition']=$Condition;
                    }


                    if(isset($personal_records['personal_records'][$achievement_goal['Event']][$achievement_goal['Result']]['best'])){
                        $achievement_goal['Condition']['personal_record']=$personal_records['personal_records'][$achievement_goal['Event']][$achievement_goal['Result']]['best'];
                    }else{
                        $achievement_goal['Condition']['personal_record']=false;
                    }

                    if(isset($achievement_goal['Condition']['record'])){
                        $achievement_goal['Condition']['value']=$records[$achievement_goal['Condition']['record']][$achievement_goal['Event']][$achievement_goal['Result']];
                    }

                    if(isset($achievement_goal['Condition']['wcaid'])){
                        $wcaid=$achievement_goal['Condition']['wcaid'];
                        if(!isset($RECORDS[$wcaid])){
                            $RECORDS[$wcaid]=GetPersonalRecords($wcaid);
                        }
                        $GetRecord=$RECORDS[$wcaid];
                        $achievement_goal['Condition']['value']=$GetRecord['personal_records'][$achievement_goal['Event']][$achievement_goal['Result']]['best'];
                        $achievement_goal['Condition']['person']=$GetRecord['person'];
                    }

                    if(isset($achievement_goal['Condition']['value']) and isset($achievement_goal['Condition']['mult'])){
                        $achievement_goal['Condition']['value']*=$achievement_goal['Condition']['mult'];
                    }


                    if(!$achievement_goal['Condition']['personal_record']){
                        $achievement_goal['Condition']['Complete']=false;
                    }else{
                        if(isset($achievement_goal['Condition']['value'])){
                           $achievement_goal['Condition']['Complete']=$achievement_goal['Condition']['value']>$achievement_goal['Condition']['personal_record']; 
                        }else{
                            $achievement_goal['Condition']['Complete']=true;    
                        }
                    }



                    $And = ($And and $achievement_goal['Condition']['Complete']);
                    $Or = ($Or or $achievement_goal['Condition']['Complete']);

                    if(isset($achievement_goal['Condition']['value'])){
                        $achievement_goal['Condition']['value']=R($achievement_goal['Condition']['value']);
                    }


                    if($achievement_goal['Condition']['personal_record']){
                        $Sum += $achievement_goal['Condition']['personal_record'];
                    }else{
                        $SumFl=false;
                    }

                    $achievement_goal['Condition']['personal_record']=R($achievement_goal['Condition']['personal_record']);    
                    $achievement_goals['AchievementGoals'][$AGoal]=$achievement_goal;
                }

                if(isset($achievement_goals['Total']['And'])){
                    $achievement_goals['Total']['Complete']=$achievement_goals['Total']['And']==$And;
                }
                if(isset($achievement_goals['Total']['Or'])){
                    $achievement_goals['Total']['Complete']=$achievement_goals['Total']['Or']==$Or;
                }
                if(isset($achievement_goals['Total']['Sum'])){
                    if($SumFl){
                        $achievement_goals['Total']['Complete']=($achievement_goals['Total']['Sum']>$Sum);
                        $achievement_goals['Total']['personal_record']=R($Sum);
                    }else{
                        $achievement_goals['Total']['Complete']=false;
                        $achievement_goals['Total']['personal_record']=R(0);
                    }
                    $achievement_goals['Total']['Sum']=R($achievement_goals['Total']['Sum']);
                }
                
                if($achievement_goals['Total']['Complete'] and !isset($Achievements_Complete[$AGroup])){
                    $Achievements_Complete[$AGroup]=$achievement_goals;
                    $Achievements_Complete[$AGroup]['Total']=$achievement_goals['Total'];
                }
                
                $achievement_groups['Achievements'][$A]=$achievement_goals; ?>
            <?php } 
            $Achievements[$AGroup]=$achievement_groups;
        } ?>

<?php if(isset($_GET['test']) and CheckAchievementGrand()){ 
        include 'AchievementTest.php';
     }else{ ?>
        <h1>Achievements
            <?php if(CheckAchievementGrand()){ ?>
                ▪ <a href="<?= PageIndex()?>?Achievements&config">Setting</a>
            <?php } ?>
        </h1>
     <?php } ?>

<div class='form'>
    <h2><nobr><?= Short_Name($competitor->name); ?></nobr></h2>
    <?php 
    
    $ranks=[];
    foreach($Achievements_Complete as $A=>$Achievement_Complete){     
        $ranks[$Achievement_Complete['Rank']][]=$A;
    }
    
    $sum_rank=0;
    $max_rank=0;
    for($j=1;$j<=5;$j++){
        if($sum_rank<3 and isset($ranks[$j])){
            $sum_rank+=sizeof($ranks[$j]);
            $max_rank=$j;
        }
    }
        
    

    for($i=1;$i<=$max_rank;$i++){
       if(isset($ranks[$i]))
       foreach($ranks[$i] as $A){
           $Achievements_Complete_Out[]=$Achievements_Complete[$A]; 
       } 
       
    }
    
    if(sizeof($Achievements_Complete_Out)){
    
        foreach($Achievements_Complete_Out as $Achievement_Complete){ 
            $Events=[];
            if(isset($Achievement_Complete['Total']['Or'])){
                foreach($Achievement_Complete['AchievementGoals'] as $AchievementGoal){
                    if($AchievementGoal['Condition']['Complete']){
                        $Events[]=$AchievementGoal['Event'];
                    }
                }
            }
            ?>


        <p><nobr>
            <?php for($i=5;$i>=$Achievement_Complete['Rank'];$i--){ ?>
                <img valign=middle width="30px" src="<?= PageIndex()?>Image/SA_small.jpeg">
            <?php } ?>
            <?= $Achievement_Complete['Name']; ?>
            <?php foreach($Events as $event){ ?>
                <img valign=middle width="30px" src="Image/AchievementImage/<?= $event ?>.png">
            <?php } ?>    
        </nobr></p>    
        <?php } ?>
    <?php }else{ ?>
        No Achievements
    <?php } ?>
</div>

<?php } ?>
      
<?php }else{ ?>    
        <div class="form">
            <span class="error">
                <?php if($competitor){ ?>
                    You don't have WCAID, so you don't have achievements 
                <?php }else{ ?>    
                    <?php $_SESSION['Refer']=$_SERVER['REQUEST_URI'];  ?>    
                    To see your achievements you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a>
                <?php } ?>        
            </span> 
        </div> 
<?php } ?>