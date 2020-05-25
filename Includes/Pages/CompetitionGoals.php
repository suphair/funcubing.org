<script>
    
    var d = new Date();
    var loc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds());
    var time_zone = ((<?= time();?> - loc/1000)/60).toFixed(0);

    function two(num) { return ("0" + num).slice(-2);} // подставляет недостающий ноль 

    function mydate(t) {
      var d = new Date((t-time_zone*60)*1000);
      return two(d.getUTCDate())+'.'+ two(d.getUTCMonth()+1)+'.'+d.getUTCFullYear()+' '+ two(d.getUTCHours())+':'+ two(d.getUTCMinutes());
    }
</script>
<?php 
#if(CheckGoalGrand()){
#    include 'CompetitionGoalsReload.php';
#}

if($Competitor and isset($_GET['My'])){
        if(  !isset($_SESSION["GoalCompetitions"]['Competitions']) 
            or !isset($_SESSION["GoalCompetitions"]['Expired']) 
            or !isset($_SESSION["GoalCompetitions"]['Created']) 
            or !isset($_SESSION["GoalCompetitions"]['Competitor'])  
            or $_SESSION["GoalCompetitions"]['Expired']<strtotime("now")
            or $_SESSION["GoalCompetitions"]["Competitor"]!=$Competitor->id){
        unset($_SESSION["GoalCompetitions"]);
        }
        
        $_SESSION["GoalCompetitions"]["Competitor"]=$Competitor->id;
        $new_session=false;
        $StatusWrong=false;

        $GoalCompetitions=[];
        if(isset($_SESSION["GoalCompetitions"]["Competitions"])){
            $GoalCompetitions=$_SESSION["GoalCompetitions"]["Competitions"];
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/users/".$Competitor->id."?upcoming_competitions=true");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($status==200){   
                $GoalCompetitions=json_decode($data,true)['upcoming_competitions'];
            }else{
                $StatusWrong=true;
            }
            $session_competitions=[];

            foreach($GoalCompetitions as $competition){
                $session_competitions[]=[
                    'name'=>$competition['name'],
                    'start_date'=>$competition['start_date'],
                    'end_date'=>$competition['end_date'],
                    'id'=>$competition['id']];
                
                DataBaseClass::FromTable("GoalCompetition","WCA='".$competition['id']."'");
                if(!DataBaseClass::QueryGenerate(false)['GoalCompetition_ID']){
                    DataBaseClass::Query("insert into GoalCompetition (Name, DateStart, DateEnd, WCA) values ('".$competition['name']."','".$competition['start_date']."','".$competition['end_date']."','".$competition['id']."')");
                }else{
                    DataBaseClass::Query("Update GoalCompetition set "
                            . " Name='".$competition['name']."', "
                            . " DateStart='".$competition['start_date']."', "
                            . " DateEnd='".$competition['end_date']."'"
                            . " where WCA='".$competition['id']."'");
                }    
            }

            $new_session=true;
            $_SESSION["GoalCompetitions"]["Competitions"]=$session_competitions;
        }
        
        if($new_session){
            $_SESSION["GoalCompetitions"]["Expired"]=strtotime("+1 hour");
            $_SESSION["GoalCompetitions"]["Created"]=strtotime("now");
        }
        ?>
        <form method="POST" action="<?= PageIndex()."Actions/GoalReloadCompetitions" ?>">
            <h2>My competitions: upcoming and with goals</h2>
            Your competition from WCA at time <span ID='Created'></span> <input type='submit' name='Reload' value='Reload'>
            <script>$("#Created").html(mydate(<?= $_SESSION["GoalCompetitions"]["Created"] ?>))</script>
        </form>
        <hr class="hr_round">
        <?php
        $comp_arr=[];
        foreach($GoalCompetitions as $competition){ 
            $comp_arr[]=$competition['id'];
        }
                DataBaseClass::Query("Select Distinct GC.* from Goal G join GoalCompetition GC on GC.WCA=G.Competition where G.Competitor=".$Competitor->id);
                foreach(DataBaseClass::getRows() as $comp){
                    if(!in_array($comp['WCA'],$comp_arr)){
                        $GoalCompetitions[]=[
                            'name'=>$comp['Name'],
                            'start_date'=>$comp['DateStart'],
                            'end_date'=>$comp['DateEnd'],
                            'id'=>$comp['WCA']];
                    }
                }
        
        usort($GoalCompetitions,'UpcomingCompetitionSort');
        $GoalCompetitions= array_reverse($GoalCompetitions);
        foreach($GoalCompetitions as $competition){  ?>
            <p>
                <?php DataBaseClass::FromTable("Goal","Competitor=".$Competitor->id);
                DataBaseClass::Where_current("Competition='".$competition['id']."'"); ?>
                <h2>
                    <a href="<?= PageIndex() ?>CompetitionGoals/<?=$competition['id'] ?>"><?=$competition['name'] ?></a> &#9642; <?= date("d.m.Y",strtotime($competition['start_date']))." - ". date("d.m.Y",strtotime($competition['end_date'])) ?>
                </h2>        
                <?php if(is_array(DataBaseClass::QueryGenerate(false))){ 
                    $fileName="Image/GoalImages/".$competition['id']."_".$Competitor->id."_".md5("GOALS".$competition['id'].$Competitor->id).".png";
                    if(file_exists($fileName)){ ?>
                        <a target="_blank" href="<?= PageIndex().$fileName ?>">Link for image</a>
                        <br><img src="<?= PageIndex().$fileName ?>?<?= random_string(5); ?>">
                    <?php } ?>
                <?php } ?>
            <hr>
        <?php } ?>

        
        
    <?php }else{ ?>
        <?php if(!$Competitor){ ?>  
            <div class="form">
                <?php $_SESSION['Refer']=$_SERVER['REQUEST_URI'];  ?>    
                <span class="error">To set competition goals you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a></span> 
            </div> 
        <?php } 
        
        DataBaseClass::FromTable("GoalDiscipline","Code<>'333mbf'");
        $disciplines=DataBaseClass::QueryGenerate();
        $CompetitorsDiscipline=[];
        foreach($disciplines as $discipline){
                DataBaseClass::Query("
                select  count(distinct G.Competitor) Competitors, CG.ID, GD.Code
                from Goal G
                   join GoalCompetition CG on CG.WCA=G.Competition
                   join GoalDiscipline GD on GD.Code=G.Discipline
                   group by CG.ID, GD.Code  
                ");
                foreach(DataBaseClass::getRows() as $r){
                    $CompetitorsDiscipline[$r['ID']][$r['Code']]=$r['Competitors'];
                }
        } ?>
       <?php if(CheckGoalGrand()){ ?>     
            <span class="badge">
                <?= 'Results have been updated: '.GetValue('GoalCompetitionReload') ?>
            </span>    
       <?php } ?>
        <h2>Сompetitions with goals</h2> 
                    <table style='white-space:nowrap' class='Competitions'>
                        <tr class="tr_title">
                            <td/>
                            <td>Competition</td>
                            <td>Date</td>
                            <td>Competitors</td>
                            <?php foreach($disciplines as $discipline){ ?>
                                <td><img width='20px' src='Image/GoalImage/<?= $discipline['GoalDiscipline_Name'] ?>.png'></td>
                            <?php } ?>
                        </tr> 
        <?php
        
        
        
        
        DataBaseClass::Query("
                select DateStart<now() close, sum(case when G.Result is not null then 1 else 0 end) Results,count(distinct G.Competitor) Competitors,
                CG.* 
               from Goal G
                   join GoalCompetition CG on CG.WCA=G.Competition
                   join GoalDiscipline GD on GD.Code=G.Discipline
                   group by CG.ID
                   order by CG.DateEnd desc,CG.WCA
                ");
        foreach(DataBaseClass::getRows() as $row){ ?>
            <tr>
                <td align='center'>
                    <?php if($row['Results']){ ?>
                        <span class='message'>V</span>
                    <?php }elseif($row['close']){ ?>
                        <span class='error'>?</span>
                    <?php }else{ ?>
                         <span style='color: var(--blue)'>&bull;</span>
                    <?php } ?>
                </td>
                <td> 
                    <a target="_blank" href="https://www.worldcubeassociation.org/competitions/<?=$row['WCA']?>"><?=$row['Name']?></a>
                </td>
                <td>
                    <?= date_range($row['DateStart'],$row['DateEnd']); ?>
                </td>
                <td align='center' class='border-right-solid'>
                    <?= $row['Competitors']?>
                </td>
                <?php foreach($disciplines as $discipline){ ?>
                    <td align='center'>
                        <?php if(isset($CompetitorsDiscipline[$row['ID']][$discipline['GoalDiscipline_Code']])){ ?>
                            <?= $CompetitorsDiscipline[$row['ID']][$discipline['GoalDiscipline_Code']] ?>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php }  ?>
                </table>    
<hr class='hr_round'>
<?php
                        $CompetitorsDiscipline=[];
                        foreach($disciplines as $discipline){
                                DataBaseClass::Query("
                                select  count(*) Goals, sum(Complete) Complete, CG.ID,  G.Competitor, GD.Code
                                from Goal G
                                   join GoalCompetition CG on CG.WCA=G.Competition
                                   join GoalDiscipline GD on GD.Code=G.Discipline
                                   group by CG.ID, G.Competitor, GD.Code  
                                ");
                                foreach(DataBaseClass::getRows() as $r){
                                    $CompetitorsDiscipline[$r['ID']][$r['Code']][$r['Competitor']]=[$r['Goals'],$r['Complete']];
                                }
                        }
                        ?>


                    <h2>Competition goals</h2> 
                    [&bull;] - goal for best result or average result, [&#247;] - goals for the best result and the average result
                    <table style='white-space:nowrap'  class='Competitions'>
                        <tr class="tr_title">
                            <td>Competition</td>
                            <td>Date</td>
                            <td>Competitor</td>
                            <td/>
                            <?php foreach($disciplines as $discipline){ ?>
                                <td><img width='20px' src='Image/GoalImage/<?= $discipline['GoalDiscipline_Name'] ?>.png'></td>
                            <?php } ?>
                        </tr>
                        <?php   

                        DataBaseClass::Query("
                                select CG.DateEnd,CG.DateStart,CG.Name CompetitionName,CG.ID CompetitionID,CG.WCA CompetitionCode,t.Result, t.TimeFixed, t.Goals, C.Name CompetitorName,C.WCAID CompetitorWCAID,C.Country CompetitorCountry,C.WID CompetitorWID from(
                                select max(TimeFixed) TimeFixed, sum(case when Result is not null then 1 else 0 end) Result,count(*) Goals, Competition,Competitor from Goal
                                group by Competition,Competitor
                                )t 
                                join GoalCompetition CG on CG.WCA=t.Competition
                                join Competitor C on C.WID=t.Competitor
                                where TimeFixed<now() and Result=0
                                order by CG.DateEnd Desc,CG.WCA,C.Name");         
                                               
                        foreach(DataBaseClass::getRows() as $row){ 
                        $filename="Image/GoalImages/".$row['CompetitionCode']."_".$row['CompetitorWID']."_".md5("GOALS".$row['CompetitionCode'].$row['CompetitorWID']).".png";
                                ?>          
                        <tr>
                           <td>
                                <a target="_blank" href="https://www.worldcubeassociation.org/competitions/<?=$row['CompetitionCode']?>"><?=$row['CompetitionName']?></a>
                           </td>
                           <td>
                                <?= date_range($row['DateStart'],$row['DateEnd']); ?>
                           </td>
                           <td>
                               <?= ImageCountry($row['CompetitorCountry'], 25)?>
                          <?php if ($row['CompetitorWCAID']){ ?>    
                                <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?=$row['CompetitorWCAID']?>"><?= Short_Name($row['CompetitorName']) ?></a>
                          <?php }else{ ?>
                               <?= Short_Name($row['CompetitorName']) ?>
                           <?php } ?>
                          </td>
                           <td align='right'>
                                <a target="_blank" href="<?= $filename ?>?<?= random_string(5); ?>">
                                    view
                                </a> 
                           </td>
                           <?php 
                           foreach($disciplines as $discipline){ ?>
                                <td align='center'>
                                    <?php if(isset($CompetitorsDiscipline[$row['CompetitionID']][$discipline['GoalDiscipline_Code']][$row['CompetitorWID']])){ 
                                        list($g,$c)=$CompetitorsDiscipline[$row['CompetitionID']][$discipline['GoalDiscipline_Code']][$row['CompetitorWID']]; 
                                        if($g==1){ ?>
                                            &bull;
                                        <?php }else{ ?>
                                            &#247;
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            <?php } ?>      
                       </tr>
                    <?php } ?>
                    </table>
<hr class='hr_round'>
                    <h2>Competition results</h2>
                    [<span style='color:var(--green)'>+</span>] - one goal and it is achieved, [<span style='color:var(--red)'>&minus;</span>] - one goal and it failed,<br>
                    [<span style='color:var(--green)'>&#8225;</span>] - two goals and both achieved, [ <span style='color:var(--blue)'>&#177;</span>] - two goals and one achieved, [<span style='color:var(--red)'>=</span>] - two goals and both failed<br>
                    <table style='white-space:nowrap'  class='Competitions'>
                        <tr class="tr_title">
                            <td>Competition</td>
                            <td>Date</td>
                            <td>Competitor</td>
                            <td/>
                            <?php foreach($disciplines as $discipline){ ?>
                                <td><img width='20px' src='Image/GoalImage/<?= $discipline['GoalDiscipline_Name'] ?>.png'></td>
                            <?php } ?>
                        </tr>
                        <?php   
                            DataBaseClass::Query("
                                select CG.DateEnd,CG.DateStart,CG.Name CompetitionName,CG.ID CompetitionID,CG.WCA CompetitionCode,t.Result, t.TimeFixed, t.Goals, C.Name CompetitorName,C.WCAID CompetitorWCAID,C.Country CompetitorCountry,C.WID CompetitorWID from(
                                select max(TimeFixed) TimeFixed, sum(case when Result is not null then 1 else 0 end) Result,count(*) Goals, Competition,Competitor from Goal
                                group by Competition,Competitor
                                )t 
                                join GoalCompetition CG on CG.WCA=t.Competition
                                join Competitor C on C.WID=t.Competitor
                                where TimeFixed<now() and Result>0
                                order by CG.DateEnd desc,CG.WCA,C.Name");         
                                               
                        foreach(DataBaseClass::getRows() as $row){ 
                            $filename="Image/GoalImages/".$row['CompetitionCode']."_".$row['CompetitorWID']."_".md5("GOALS".$row['CompetitionCode'].$row['CompetitorWID']).".png";
                                ?>          
                        <tr>
                           <td>
                               <a target="_blank" href="https://www.worldcubeassociation.org/competitions/<?=$row['CompetitionCode']?>"><?=$row['CompetitionName']?></a>
                           </td>
                           <td>
                                <?= date_range($row['DateStart'],$row['DateEnd']); ?>
                           </td>
                           <td>
                           <?= ImageCountry($row['CompetitorCountry'], 25)?>
                          <?php if ($row['CompetitorWCAID']){ ?>    
                            <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?=$row['CompetitorWCAID']?>"><?= Short_Name($row['CompetitorName']) ?></a>
                          <?php }else{ ?>
                               <?= Short_Name($row['CompetitorName']) ?>
                           <?php } ?>
                          </td>
                           <td align='right'>
                                <a target="_blank" href="<?= $filename ?>?<?= random_string(5); ?>">
                                    view
                                </a> 
                           </td>
                           <?php 
                           foreach($disciplines as $discipline){ ?>
                                <td align='center'>
                                    <?php if(isset($CompetitorsDiscipline[$row['CompetitionID']][$discipline['GoalDiscipline_Code']][$row['CompetitorWID']])){ 
                                         list($g,$c)=$CompetitorsDiscipline[$row['CompetitionID']][$discipline['GoalDiscipline_Code']][$row['CompetitorWID']]; 
                                        if($g==1){
                                            if($c==1){ ?>
                                                <span style='color:var(--green)'>+</span>
                                            <?php }else{ ?>
                                                <span style='color:var(--red)'>&minus;</span>
                                            <?php } ?>
                                        <?php } 
                                        if($g==2){
                                            if($c==2){ ?>
                                                <span style='color:var(--green)'>&#8225;</span>
                                            <?php }elseif($c==1){ ?>
                                                <span style='color:var(--blue)'>&#177;</span>
                                            <?php }else{ ?>
                                                <span style='color:var(--red)'>=</span>
                                            <?php } ?>        
                                        <?php } ?>
                                                
                                    <?php } ?>
                                </td>
                            <?php } ?>      
                       </tr>
                    <?php } ?>
                    </table>
        <div class="wrapper">
            <div class="form instruction" align='left'>
                <h2>Instructions</h2>
                <?php if(!$Competitor){ ?>  
                    <font style='color:var(--green)'>▪</font> <a href="<?= GetUrlWCA(); ?>">Sign in with WCA</a> <br>
                    <font style='color:var(--green)'>▪</font> Go to "My competition goals" <br>
                <?php }else{ ?>
                    <font style='color:var(--green)'>▪</font> Go to <a href="<?= PageIndex() ?>?CompetitionGoals&My">My competition goals</a><br>
                <?php } ?>
                    <font style='color:var(--green)'>▪</font> Select upcoming competitions <br>
                    <img  width="600px" src='Image/GoalInstructions/1.png'><br>
                    <font style='color:var(--green)'>▪</font> Set competition goals <br> they can be changed or deleted only within one hour after creation <br>
                    <img  width="600px" src='Image/GoalInstructions/2.png'><br>
                    <font style='color:var(--green)'>▪</font> Generate image and post it on social networks<br> 
                    Example: <font style='color:var(--green)'>If I reach 5 goals, I will buy VALK 5 M</font><br>
                    <img  src='Image/GoalInstructions/3.png'><br>
                    <font style='color:var(--green)'>▪</font> After the results are published on WCA,<br> you need to enter the competition to get a image with the results<br> 
                    <img  src='Image/GoalInstructions/4.png'><br>
            </div>
         </div>
<?php } ?>