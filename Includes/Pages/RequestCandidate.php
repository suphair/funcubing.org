<?php 
$delegate= GetDelegateData();
$isAdmin=CheckAdmin();
if($delegate){ ?>
  
    <h1>Requests for join to Judges <?php if($isAdmin){ ?> <nobr>&#9642; <a href="<?= PageIndex()?>RequestCandidate/config">Setting</a></nobr> <?php } ?></h1>
    <hr>
    <?php DataBaseClass::FromTable("RequestCandidate","Status=0");
          DataBaseClass::Join_current("Competitor");
          DataBaseClass::OrderClear("RequestCandidate","ID desc");
          $RequestCandidates=DataBaseClass::QueryGenerate();
          DataBaseClass::Join("RequestCandidate","RequestCandidateField");
          DataBaseClass::Order("RequestCandidateField","ID");
          $RequestCandidateFields=DataBaseClass::QueryGenerate();
          if(!sizeof($RequestCandidates)){ ?>
             <h2>Нет заявок</h2>
          <?php }
          
          foreach($RequestCandidates as $RequestCandidate){ ?>
             <h2><span onclick="
             if(document.getElementById(<?= $RequestCandidate['Competitor_ID'] ?>).hidden){
                 document.getElementById(<?= $RequestCandidate['Competitor_ID'] ?>).hidden=false;
             }else{
                 document.getElementById(<?= $RequestCandidate['Competitor_ID'] ?>).hidden=true;
             }
                                    " style="border-bottom: 1px double blue; cursor: pointer">
                    <nobr><?= $RequestCandidate['Competitor_Name'] ?></nobr> 
                    </nobr>
                 </span>
             </h2>
        <div hidden id="<?= $RequestCandidate['Competitor_ID'] ?>">  
           <h2>
                <nobr><?= $RequestCandidate['Competitor_WCAID'] ?> &#9642; 
                <?= CountryName($RequestCandidate['Competitor_Country']) ?> </nobr>
          </h2>
          <h3>
              <nobr><a href="<?= PageIndex()."Competitor/".$RequestCandidate['Competitor_WCAID'] ?>">Профиль FC</a>
              <a href="https://www.worldcubeassociation.org/persons/<?= $RequestCandidate['Competitor_WCAID'] ?>">Профиль WCA</a></nobr>
              <nobr> &#9642; <?= $RequestCandidate['RequestCandidate_Datetime'] ?></nobr>
          </h3>
          <table>
            <?php foreach($RequestCandidateFields as $RequestCandidateField){
                if($RequestCandidateField['RequestCandidateField_RequestCandidate']==$RequestCandidate['RequestCandidate_ID']){ ?>
                    <tr>
                        <td width="400"><?= $RequestCandidateField['RequestCandidateField_Field'] ?></td>
                        <td><?= $RequestCandidateField['RequestCandidateField_Value'] ?></td>
                    <tr>
                <?php }
            } ?>
            
            <?php 
            #$result = file_get_contents(GetIni('WCA_API','person')."/".$RequestCandidate['Competitor_WCAID'], false); 
            #$person=json_decode($result);
            #$person_arr=json_decode($result,true);?>
            <!--<tr><td>Competitions WCA</td><td><?php # $person->competition_count ?></td></tr>
            <tr><td>Disciplines WCA</td><td><?php # sizeof($person_arr['personal_records']) ?></td></tr>-->

            <?php DataBaseClass::FromTable("Competitor","ID=".$RequestCandidate['Competitor_ID']);
                  DataBaseClass::Join_current("CommandCompetitor");
                  DataBaseClass::Join_current("Command");
                  DataBaseClass::Join_current("Event");
                  DataBaseClass::Join_current("Competition");
                  DataBaseClass::Join("Event","DisciplineFormat");
                  DataBaseClass::Join_current("Discipline");
                  DataBaseClass::OrderClear("Competition","StartDate");
                  $events=DataBaseClass::QueryGenerate();
                  $competitions=array();
                  $disciplines=array();        
                  foreach($events as $event){
                      $competitions[$event['Competition_Name']]=$event['Competition_Name'];
                      $disciplines[$event['Discipline_Name']]=$event['Discipline_Name'];
                  } ?>
                <tr><td>Competitions FC</td><td><?= sizeof($competitions) ?></td></tr>
                <tr><td>Disciplines FC</td><td><?= sizeof($disciplines) ?></td></tr>
                  
                                    
                  
             </table>
        <?php if($isAdmin){ ?>
    <table><tr><td>
                <form method="POST" action="<?= PageIndex()."Actions/RequestCandidateAction" ?>" onsubmit="return confirm('Внимание:Подтвердите отказ для <?= $RequestCandidate['Competitor_Name'] ?>.')">
                    <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID']?>'>    
                    <input class='delete' name="RequestCandidateAction" type='submit' value='Отказать'>
                </form>
    </td><td>
                <form method="POST" action="<?= PageIndex()."Actions/RequestCandidateAction" ?>" onsubmit="return confirm('Внимание:Подтвердите принятие <?= $RequestCandidate['Competitor_Name'] ?>.')">
                    <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID']?>'>    
                    <input type='submit' name="RequestCandidateAction" value='Принять'>
                </form>
    </td></tr></table>
        <?php }  ?>
        </div>
    <?php }  ?>
            
    
<?php  }else{ 
    
    $langs=array();
    DataBaseClass::Query("Select distinct Language from RequestCandidateTemplate");
    foreach(DataBaseClass::getRows() as $row){
        if(!in_array($row['Language'],$langs)){
        $langs[]=$row['Language'];
        }
    }
    
    $competitor= GetCompetitorData(); 
    if(isset($_GET['Lang']) and in_array($_GET['Lang'],$langs)){
        $lang=$_GET['Lang'];    
    }else{
        if($competitor and in_array($competitor->country_iso2,array('RU','UA','BY'))){
            $lang='RU';
        }else{
            if(in_array('US',$langs)){
                $lang='US';
            }else{
                $lang=$langs[0];  
            }
        }
    }
    
    if($competitor){    
        DataBaseClass::fromTable("RequestCandidateField");
        DataBaseClass::Join_current("RequestCandidate");
        DataBaseClass::Join_current("Competitor");
        DataBaseClass::Where_current("WID=".GetCompetitorData()->id);
        DataBaseClass::OrderClear("RequestCandidateField", "ID");
        $RequestCandidateFields=DataBaseClass::QueryGenerate();
    }

    foreach($langs as $l){ ?>
        <img width="40" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($l)?>.png">
        <?php if($l==$lang){ ?>
            <span class="error"><?= $l ?></span>
        <?php }else{ ?>
            <a class="local_link" href="<?= PageIndex()?>RequestCandidate/?Lang=<?= $l ?>"><?= $l ?></a>
        <?php } ?>
             
    <?php }
    
    if($lang=='RU'){ ?>
        <h1>Заявка в судьи неофициальных дисциплин</h1>
    <?php }else{ ?>
        <h1>Request for candidate judges</h1>
    <?php } ?>
        <?php if(!$competitor){ ?>
            <span class="error">You need <a href="<?= GetUrlWCA(); ?>">sign in with WCA</span> 
        <?php }elseif(!$competitor->wca_id){ ?>
           <?php if($lang=='RU'){ ?>
                    <span class="error">У вас должен быть WCAID</span>  
            <?php }else{ ?>
                    <span class="error">You need have WCAID</span>  
            <?php } ?>
           
           <?php }elseif(sizeof($RequestCandidateFields) and $RequestCandidateFields[0]['RequestCandidate_Status']==-1){ ?>
                <?php if($lang=='RU'){ ?>
                        <span class="error">Ваша заявка отклонена</span>  
                <?php }else{ ?>
                        <span class="error">You request have been declined</span>
                <?php } ?>
                  
            <?php }else{ ?>
            <h2><?= $competitor->name; ?> &#9642; <?= $competitor->wca_id; ?> &#9642; <?= CountryName($competitor->country_iso2) ?></h2>
            <div class='form'>
                <form method="POST" action="<?= PageIndex()."Actions/RequestCandidateAdd" ?>">
                    <input type="hidden" name='ID' value="<?= $competitor->id ?>">
                    <?php
                    DataBaseClass::FromTable("RequestCandidateTemplate","Language='$lang'");
                    foreach(DataBaseClass::QueryGenerate() as $template){ ?>
                        <div class="form_field">
                            <?= $template['RequestCandidateTemplate_Name'] ?>
                        </div>
                    <div class="form_input">
                        <?php if($template['RequestCandidateTemplate_Type']=='input'){ ?>
                            <input required name="Fields[<?= DataBaseClass::Escape($template['RequestCandidateTemplate_Name']) ?>]" value="" type="text">
                        <?php }else{ ?>
                                <textarea required name="Fields[<?= DataBaseClass::Escape($template['RequestCandidateTemplate_Name']) ?>]"></textarea>
                        <?php } ?>
                    </div>
                    <?php } ?>        
                    <div class="form_change">
                        <input type="submit" value="Подать заявку">
                    </div>
                </form>
                <?php $err=GetMessage("RequestCandidateAdd");
                    if($err){ ?>
                        <br><span class="error"><?= $err?></span>
                    <?php } ?>
            </div>
            <br>
            <?php 
            if(sizeof($RequestCandidateFields)){ ?>
            <h2>Заявка подана <?= $RequestCandidateFields[0]['RequestCandidate_Datetime']?></h2>

            <?php foreach($RequestCandidateFields as $RequestCandidateField){ ?>
                <p>
                    <?= $RequestCandidateField['RequestCandidateField_Field'] ?> &#9642;
                    <?= $RequestCandidateField['RequestCandidateField_Value'] ?>
                </p>  
            <?php } 
            }
        } ?>           
<?php } ?>

