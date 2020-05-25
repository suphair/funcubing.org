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
                    <a href="<?= PageIndex() ?>Goals/<?=$competition['id'] ?>"><?=$competition['name'] ?></a> &#9642; <?= date("d.m.Y",strtotime($competition['start_date']))." - ". date("d.m.Y",strtotime($competition['end_date'])) ?>
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
                <span class="error">To set goals on competitions you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a></span> 
            </div> 
        <?php }
            DataBaseClass::Query("select CG.Name CompetitionName,CG.WCA CompetitionCode, t.TimeFixed,C.Name CompetitorName,C.WCAID CompetitorWCAID,C.WID CompetitorWID from(
select max(TimeFixed) TimeFixed,Competition,Competitor from Goal
group by Competition,Competitor
)t 
join GoalCompetition CG on CG.WCA=t.Competition
join Competitor C on C.WID=t.Competitor
where TimeFixed<now()
order by TimeFixed desc limit 10"); 
            $rows=DataBaseClass::getRows();?>
            <h2>Recent <?= min([sizeof($rows),10]) ?></h2> 
            <table>
                <tr class="tr_title">
                    <td>Competition</td>
                    <td>Competitor</td>
                    <td>Time</td>
                    <td>Image</td>
                </tr>
        <?php foreach($rows as $row){ 
            $filename="Image/GoalImages/".$row['CompetitionCode']."_".$row['CompetitorWID']."_".md5("GOALS".$row['CompetitionCode'].$row['CompetitorWID']).".png";
                    ?>          
           <tr>
               <td><a target="_blank" href="https://www.worldcubeassociation.org/competitions/<?=$row['CompetitionCode']?>"><?=$row['CompetitionName']?></a></td>
               <td>
              <?php if ($row['CompetitorWCAID']){ ?>    
                <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?=$row['CompetitorWCAID']?>"><?=$row['CompetitorName']?></a>
              <?php }else{ ?>
                   <?= Short_Name($row['CompetitorName']) ?>
               <?php } ?>
              </td>
               <td><?= date("d M H:i",strtotime($row['TimeFixed']))?></td>
               <td>
                   <a target="_blank" href="<?= $filename ?>">
                        view
                    </a> 
               </td>
           </tr>
        <?php } ?>
        </table>
        <?php if(CheckGoalGrand()){   
            foreach (scandir('Image/GoalImages') as $filename){
                if(strpos($filename,".png")){ ?>
                    <a target="_blank" href="Image/GoalImages/<?= $filename ?>">
                        <img  width=220px src='Image/GoalImages/<?= $filename ?>'>
                    </a>    
                <?php }
            }
            //GoalImageCreate('YJMoscowSpecial2019',79079);
        }?>    
        <div class="wrapper">
            <div class="form instruction" align='left'>
                <h2>Instructions</h2>
                <?php if(!$Competitor){ ?>  
                    <font style='color:var(--green)'>▪</font> <a href="<?= GetUrlWCA(); ?>">Sign in with WCA</a> <br>
                    <font style='color:var(--green)'>▪</font> Go to "My goals on competitions" <br>
                <?php }else{ ?>
                    <font style='color:var(--green)'>▪</font> Go to <a href="<?= PageIndex() ?>?Goals&My">My goals on competitions</a><br>
                <?php } ?>
                    <font style='color:var(--green)'>▪</font> Select upcoming competitions <br>
                    <img  width="600px" src='Image/GoalInstructions/1.png'><br>
                    <font style='color:var(--green)'>▪</font> Set goals on competition <br> goals can be changed or deleted only two hours after creation <br>
                    <img  width="600px" src='Image/GoalInstructions/2.png'><br>
                    <font style='color:var(--green)'>▪</font> Generate image and post it on social networks<br> 
                    Example: <font style='color:var(--green)'>If I reach 5 goals, I will buy VALK 5 M</font><br>
                    <img  src='Image/GoalInstructions/3.png'><br>
                    <font style='color:var(--red)'>▪ Instructions will be added after the competition YJ Moscow Special 2019 </font><br> 
            </div>
         </div>
<?php } ?>