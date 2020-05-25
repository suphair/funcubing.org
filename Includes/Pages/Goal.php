<script>
    
    var d = new Date();
    var loc = Date.UTC(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds());
    var time_zone = ((<?= time();?> - loc/1000)/60).toFixed(0);

    function two(num) { return ("0" + num).slice(-2);} // подставляет недостающий ноль 

    function mydate(t) {
      var d = new Date((t-time_zone*60)*1000);
      return two(d.getUTCDate())+'.'+ two(d.getUTCMonth()+1)+'.'+d.getUTCFullYear()+' '+ two(d.getUTCHours())+':'+ two(d.getUTCMinutes());
    }
    
    function GoalEnter(el,event,format){
        var value = el.val();

        value=value.replace(/\D+/g,'');
        value=value.replace(/^0+/,'');   
        value=value.substring(0,7);
        if(value==''){
            el.val(value);
            return;   
        }

        if(event=='333fm' && format=='single'){
            if(value.length>2){
                value=value.substr(0,2);
            }
            el.val(value);
            return;
        }
        
        if(event=='333fm' && format=='average'){
            value='000' + value ;
            value=value.substr(-4,4)
            value=value.substr(0,2) + '.' + value.substr(2,2);
            value=value.replace(/^[0]+/g,"");
            el.val(value);
            return;
        }

        var minute=0;
        var second=0;
        var milisecond=0;

        if(value.length===1){
            value='0.0' + value ;
        }else if(value.length===2){
            value='0.' + value ; 
        }else if(value.length===3){
            second=Number.parseInt(value.substr(0,1));
            value=value.substr(0,1) + '.' + value.substr(1,2) ;
        }else if(value.length===4){
            second=Number.parseInt(value.substr(0,2));
            value=value.substr(0,2) + '.' + value.substr(2,2) ;
        }else if(value.length===5){
            second=Number.parseInt(value.substr(1,2));
            minute=Number.parseInt(value.substr(0,1));
            value=value.substr(0,1) + ':' + value.substr(1,2) + '.' + value.substr(3,2) ;
        }else if(value.length===6){
            second=Number.parseInt(value.substr(2,2));
            minute=Number.parseInt(value.substr(0,2));
            milisecond=Number.parseInt(value.substr(4,2));
            if(milisecond>=50){
                second=second+1;
            }
            if(second===60){
                second=0;
                minute=minute+1;
            }
            value=('0'+minute).substr(-2,2)  + ':' + ('0'+second).substr(-2,2) + '.00' ;
        }else{
            value='';
        }                    
       el.val(value);
    }    
</script>
<?php $Competitor=GetCompetitorData(); ?>
<?php if(!$Competitor){ ?>    
    <div class="form">
        <?php $_SESSION['Refer']=$_SERVER['REQUEST_URI'];  ?>    
        <span class="error">To set goals on competitions you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a></span> 
    </div> 
<?php }else{
        $competition_wca=RequestClass::getParam1();
        $competition = DataBaseClass::SelectTableRow('GoalCompetition', "WCA='$competition_wca'"); ?>
<h2> <?=$competition['GoalCompetition_Name'] ?></a> &#9642; <?= date("d.m.Y",strtotime($competition['GoalCompetition_DateStart']))." - ". date("d.m.Y",strtotime($competition['GoalCompetition_DateEnd'])) ?></h2>
        <?php
        DataBaseClass::Query("Select * from Goal where Competition='$competition_wca' and  Competitor=".$Competitor->id." and Goal is not null");
        $dateLoad=''; 
        if(strtotime($competition['GoalCompetition_DateStart'])<strtotime("now")){
            if(isset($_POST['ReloadResult']) or !DataBaseClass::getRow()['ID']){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/competitions/$competition_wca/results");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $results=json_decode($data,true);
                if($status==200 and sizeof($results)){
                    $dateLoad=strtotime("now");
                    $result=[];
                    $result_best=['best'=>[],'average'=>[]];;
                    foreach($results as $result){
                        if($result['wca_id']==$Competitor->wca_id){
                            foreach(['best','average'] as $format){
                                if($result[$format]>0){
                                    if(!isset($result_best[$format][$result['event_id']])){
                                        $result_best[$format][$result['event_id']]=$result[$format];
                                    }elseif($result_best[$format][$result['event_id']]<$result[$format]){
                                        $result_best[$format][$result['event_id']]=$result[$format];
                                    }
                                }
                            }
                        }
                    }
                    $result_best['single']=$result_best['best'];
                    DataBaseClass::Query("Select * from Goal where Competitor=".$Competitor->id." and Competition='$competition_wca'");
                    foreach(DataBaseClass::getRows() as $row){
                        if(isset($result_best[$row['Format']][$row['Discipline']])){
                            $result_in=GoalEnter($result_best[$row['Format']][$row['Discipline']],$row['Discipline'],$row['Format']);
                            DataBaseClass::Query("Update Goal set Result='$result_in' where Discipline='".$row['Discipline']."' and Competitor=".$Competitor->id." and Competition='$competition_wca' and Format='".$row['Format']."'");
                        }
                    }

                    GoalImageCreate($competition_wca,$Competitor->id);  
                }
            }?>
                <form method="POST" action="">
                    <input type='submit' name='ReloadResult' value='Reload results'>
                    <?php if($dateLoad){ ?>
                        Loaded at <span ID="Reload"></span>
                        <script>$("#Reload").html(mydate(<?= $dateLoad  ?>))</script>
                    <?php }?>
                </form>   
        <?php }else{

            if(  !isset($_SESSION["GoalEvents"]['Competition']) 
                or !isset($_SESSION["GoalEvents"]['Expired']) 
                or !isset($_SESSION["GoalEvents"]['Created']) 
                or !isset($_SESSION["GoalEvents"]['Competitor']) 
                or !isset($_SESSION["GoalEvents"]['Events']) 
                or $_SESSION["GoalEvents"]['Expired']<strtotime("now")
                or $_SESSION["GoalEvents"]["Competitor"]!=$Competitor->id
                or $_SESSION["GoalEvents"]["Competition"]!=$competition_wca){
                unset($_SESSION["GoalEvents"]);
            }

            $GoalEvents=[];
            if(isset($_SESSION["GoalEvents"])){
                $GoalEvents=$_SESSION["GoalEvents"];
            }else{
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/competitions/$competition_wca/registrations");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if($status==200){
                        foreach(json_decode($data,true) as $registration){
                            if($registration['user_id']==$Competitor->id){
                                $GoalEvents['Events']=$registration['event_ids'];
                            }
                        }
                        $GoalEvents['Competition']=$competition_wca;
                        $GoalEvents['Competitor']=$Competitor->id;        
                        $GoalEvents['Expired']=strtotime("+1 hour");
                        $GoalEvents['Created']=strtotime("now");
                    }else{
                        $StatusWrong=true;
                    }

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.worldcubeassociation.org/api/v0/persons/".$Competitor->wca_id);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if($status==200){
                        foreach(json_decode($data,true)['personal_records'] as $event=>$record){    
                            if($event!=='333mbf'){
                                if(isset($record['single']['best'] )){
                                    $GoalEvents['Records'][$event]['single']=$record['single']['best'] ;
                                }
                                if(isset($record['average']['best'] )){
                                    $GoalEvents['Records'][$event]['average']=$record['average']['best'] ;
                                }
                            }
                        }
                    }else{
                        $StatusWrong=true;
                    }

                $_SESSION["GoalEvents"]=$GoalEvents;
            }

            if(!sizeof($GoalEvents)){ ?>
                <span class="error">Your has no events on this competition</span>
            <?php } ?>
            <form method="POST" action="<?= PageIndex()."Actions/GoalReloadEvents" ?>">
                Yours events and records from WCA at time <span ID='Created'></span> <input type='submit' name='Reload' value='Reload'>
                <script>$("#Created").html(mydate(<?= $_SESSION["GoalEvents"]["Created"] ?>))</script>
            </form>
    <?php
            DataBaseClass::Query("Select G.TimeFixed < now() Past, G.* from Goal G where Competitor=".$Competitor->id." and Competition='".$competition_wca."'");
            $Goals=[];
            foreach(DataBaseClass::getRows() as $row){
               $Goals[$row['Discipline']][$row['Format']]=$row;
            }  

            DataBaseClass::FromTable("GoalDiscipline");
            $Disciplines=[];
            foreach(DataBaseClass::QueryGenerate() as $row){
                $Disciplines[$row['GoalDiscipline_Code']]=$row['GoalDiscipline_Name'];
            }
        ?>

        <form method="POST" action="<?= PageIndex()."Actions/GoalSet" ?>">
            <input hidden value='<?= $Competitor->id ?>' name='Competitor'>    
        <table>
            <tr class='tr_title'>
                <td></td>
                <td>Event</td>
                <td class='attempt'>Single</td>
                <td class='attempt'>Goal</td>
                <td class='attempt'>Progress</td>
                <td class='attempt'>Average</td>
                <td class='attempt'>Goal</td>
                <td class='attempt'>Progress</td>
            </tr>
            <?php foreach($GoalEvents['Events'] as $event){
                 if(isset($Goals[$event]['single'])){
                     $single=$Goals[$event]['single'];
                 }else{
                     $single='';
                 }
                if(isset($Goals[$event]['average'])){
                     $average=$Goals[$event]['average'];
                 }else{
                     $average='';
                 }

                ?>
                <tr>
                    <td style='vertical-align: middle'>
                        <img width="20px" src="<?= PageIndex()."Image/GoalImage/".$Disciplines[$event]?>.png">
                    </td>
                    <td class='border-right-solid' style='vertical-align: middle'>
                        <?= $Disciplines[$event]; ?>
                    </td>
                    <td style='vertical-align: middle' class=' attempt border-right-dotted'>
                        <?= GoalRecordFormat($GoalEvents['Records'],$event,'single'); ?>
                        <input hidden value='<?= GoalRecordFormat($GoalEvents['Records'],$event,'single'); ?>' name='Records[<?= $competition_wca ?>][<?= $event ?>][single]'>
                    </td>
                    <td class='border-right-dotted attempt'  style='vertical-align: middle'>
                        <?php if(!$single or ($single and !$single['Past'])){ ?>
                            <input name=Goals[<?= $competition_wca ?>][<?= $event ?>][single] maxlength='8' autocomplete="off" style="width:90;  font-family: monospace; font-size: 16px;text-align: center" name="Value"class="value_input error" 
                                oninput="GoalEnter($(this),'<?= $event?>','single')"
                                value="<?= $single?$single['Goal']:'' ?>">
                        <?php }else{ ?>
                            <b><?= $single?$single['Goal']:'' ?></b>
                        <?php } ?>
                    </td>
                    <td class='attempt border-right-solid message'  style=' vertical-align: middle'>
                        <?= GoalProgress($GoalEvents['Records'],$event,'single',$single) ?>
                    </td>
                    <td style='vertical-align: middle' class='attempt border-right-dotted'>
                        <font ><?= GoalRecordFormat($GoalEvents['Records'],$event,'average'); ?></font>
                        <input hidden value='<?= GoalRecordFormat($GoalEvents['Records'],$event,'average'); ?>' name='Records[<?= $competition_wca ?>][<?= $event ?>][average]'>
                    </td>    
                    <td  class=' attempt border-right-dotted' align='right'  style='vertical-align: middle'>
                        <?php if(!$average or ($average and !$average['Past'])){ ?>
                            <input  name=Goals[<?= $competition_wca ?>][<?= $event ?>][average] maxlength='8' autocomplete="off" style="width:90;  font-family: monospace; font-size: 16px;text-align: center" name="Value"class="value_input error" 
                               oninput="GoalEnter($(this),'<?= $event?>','average')"
                               value="<?= $average?$average['Goal']:'' ?>">
                        <?php }else{ ?>
                            <b><?= $average?$average['Goal']:'' ?></b>
                        <?php } ?>
                    </td>
                    <td class='attempt message'  style='vertical-align: middle'>
                        <?= GoalProgress($GoalEvents['Records'],$event,'average',$average) ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
            <?php DataBaseClass::Query("Select max(TimeFixed) TimeFixed from Goal where TimeFixed>now() ");
            if($TimeFixed=DataBaseClass::getRow()['TimeFixed']){ ?>
                <span class="error">Can be changed before <?= $TimeFixed; ?></span><br>
            <?php } ?>
            <input type='submit' value='Save goals / Rebuild image'>
        </form>  
    <?php } ?>        
    <?php if(file_exists("Image/GoalImages/".$competition_wca."_".$Competitor->id."_".md5("GOALS".$competition_wca.$Competitor->id).".png")){ ?>
        <br>
        <img src="<?= PageIndex() ?>Image/GoalImages/<?= $competition_wca."_".$Competitor->id."_".md5("GOALS".$competition_wca.$Competitor->id) ?>.png?<?= random_string(5); ?>">
    <?php } ?>        

<?php } ?>
