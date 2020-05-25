<?php     
    $Competitor=GetCompetitorData();
    $Delegate= GetDelegateData();
    $competitor_competitions=array(-1);
    if($Competitor){
        DataBaseClass::Query(
                "Select distinct(E.Competition) Competition from Competitor C"
                ." join CommandCompetitor CC on CC.Competitor=C.ID"
                ." join Command Com on Com.ID=CC.Command"
                ." join Event E on E.ID=Com.Event "
                ." where C.WID=".$Competitor->id.""
                );

        foreach(DataBaseClass::GetRows() as $row){
            $competitor_competitions[]=$row['Competition'];
        }
    }
    
    
    $My=0;
    $country_filter='0';
    if(isset($_GET['My'])){
        $My=1;
    }else{
        if(isset($_GET['Country'])){
            $country_filter= DataBaseClass::Escape($_GET['Country']);
        }
    }
   
    DataBaseClass::Query("Select Cn.Country, count(*) count from Competition Cn "
            .($isAdmin?"": " where Cn.Status=1 ")
            . "group by Cn.Country order by 2 desc ");
    
    $competitions_countries=DataBaseClass::getRows();
    $competitions_countries_all=0;
    foreach($competitions_countries as $competitions_country){
        $competitions_countries_all+=$competitions_country['count'];
    }      
    
    ?>
    
    <script>
        <?php if($country_filter!='0'){ ?>
            document.title = "Unofficial Events ▪ Competitions ▪ <?= CountryName($country_filter) ?> ";
        <?php }else{ ?>
            document.title = "Unofficial Events ▪ Competitions";
        <?php } ?>
    </script>    
    <?php 
    $isAdmin=CheckAdmin();
    $Delegate= GetDelegateData();
    
    
    DataBaseClass::Query("
    select t.*,
    case 
    when t.ID IN(129,145) then 2
    when Status=0 then -1
    when countDisiplines=0 and EventPicture then 1
    when countDisiplines=0 then -1
    when (ResultExists and nonResultExist) or (current_date>=StartDate and nonResultExist) or (current_date>=StartDate and current_date<=EndDate) then 0
    when ResultExists and !nonResultExist then 1
    when ( !countCompetitors or (!ResultExists and nonResultExist)) and current_date<StartDate then 0.5
    end UpcomingStatus

        from(    

    select Cn.*, count(distinct C.ID) countCompetitors, count(distinct E.DisciplineFormat) countDisiplines,
    case when max(coalesce(Com.Place,0))>0 then 1 else 0 end ResultExists,
    case when sum(case when A.ID is null and Com.Decline!=1 then 1 else 0 end)>0 then 1 else 0 end nonResultExist
    from `Competition` Cn
    left outer join `Event` E on E.Competition=Cn.ID
    left outer join `Command` Com on Com.Event=E.ID  and Com.Decline!=1
    left outer join `Attempt` A on A.Command=Com.ID and A.Attempt=1
    left outer join `CommandCompetitor` CC on CC.Command=Com.ID 
    left outer join `Competitor` C on C.ID=CC.Competitor
    where ".($Delegate?"1=1": "Cn.Status=1") ."
    and ('$country_filter'='0' or '$country_filter'=Cn.Country) 
    and ($My=0 or Cn.id in (".implode(",",$competitor_competitions)."))   
    group by Cn.ID 
    
    )t
    order by UpcomingStatus, t.StartDate desc"); 
    $results= DataBaseClass::getRows();
    ?>
    <h2> 
        
        <?php if($My){ ?>
            <?php if($Competitor){ ?>
                <img style="vertical-align: middle"  src="<?= $Competitor->avatar->thumb_url?>" width="50">
            <?php } ?>
            My competitions
        <?php }elseif($country_filter=='0'){ ?>
            All competitions
        <?php }else{ ?>
            <?= ImageCountry($country_filter, 50)?>
            Competitions in <?= CountryName($country_filter)?>
        <?php } ?>
            
        <select onchange="document.location='<?= PageIndex()?>' + this.value ">
            <option <?= ($country_filter=='0' and $My==0)?'selected':''?> value="?Competitions">All competitions: <?= $competitions_countries_all ?></option>
            <option <?= $My=='1'?'selected':''?> value="?Competitions&My">My competitions<?php if(sizeof($competitor_competitions)-1>0){ ?>: <?= sizeof($competitor_competitions)-1 ?> <?php } ?></option>
            <option disabled>------</option>

            <?php foreach($competitions_countries as $competitions_country)if($competitions_country['Country']){ ?>
                    <option <?= $country_filter==$competitions_country['Country']?'selected':''?> value="?Competitions&Country=<?= $competitions_country['Country']?>">        
                        <?= CountryName($competitions_country['Country']) ?> [<?= $competitions_country['Country'] ?>]: <?= $competitions_country['count'] ?>
                    </option> 
            <?php } ?>      

        </select>
    </h2>
    <table class="Competitions">
    <?php 
    $comp_status='-2';
    foreach( $results as $i=>$r){ ?>
        <?php 
            if($r['UpcomingStatus']!=$comp_status){
            $comp_status = $r['UpcomingStatus']; ?>
            <?php if($i){ ?>
                <tr class="no_border"><td></td></tr>
            <?php } ?>    
            <tr class="no_border">
                <td colspan="7" class="tr_title">
                    <?php if($comp_status==1){ ?>
                        Past competitions
                    <?php }elseif($comp_status==0){ ?>    
                        Competitions in progress
                    <?php }elseif($comp_status==0.5){ ?>
                        Upcoming competitions
                    <?php }elseif($comp_status==-1){ ?>
                        Hidden competitions (displayed only to judges)
                    <?php }else{ ?>
                        Secret competitions (displayed only to judges)
                    <?php } ?>
                </td>
            </tr>
            <!--<tr class="tr_title">
                <td>Date</td>
                <td>Name</td>
                <td>Location</td>
                <td>Persons</td>
                <td>Events</td>
                <?php if($Delegate){ ?><td>Report</td><?php } ?>
            </tr>-->
        <?php 
        } ?>
    <tr valign="bottom">
        <td>
            <?= date_range($r['StartDate'],$r['EndDate']); ?>
        </td>   
        <td>
            <?php if($comp_status==0.5){ ?>
                <?php if($r['Registration']){ ?>
                    <?= svg_green(10,'Registration is opened') ?>
                <?php }else{ ?>
                    <?= svg_red(10,'Registration is closed') ?>
                <?php } ?>
            <?php } ?>    
            <?php if($comp_status==0){ ?>
                <?php if($r['Onsite']){ ?>
                    <?= svg_green(10,'On-site registration is enabled') ?>
                <?php } ?>
            <?php } ?>    
            <a href="<?= LinkCompetition($r['WCA']) ?>">
                <?= $r['Name'] ?>
            </a>
        </td>
        <td>
            <?= ImageCountry($r['Country'],20) ?>
            <?= CountryName($r['Country']) ?>, <?= CountryName($r['City']) ?>
        </td>
        <td class="attempt">
            <?= $r['countCompetitors']?$r['countCompetitors']:'' ?>
        </td>
        <td>
            <?php 
                    DataBaseClass::Query(
                            "Select E.ID Event, D.Name,D.Code,sum(case when Com.Decline!=1 and A.ID is null then 1 else 0 end) notResult "
                            . " from Discipline D"
                            . " join DisciplineFormat DF on DF.Discipline=D.ID "
                            . " join Event E on E.DisciplineFormat=DF.ID "
                            . " left outer join Command Com on Com.Event=E.ID and Com.Decline!=1 "
                            . " left outer join Attempt A on A.Command=Com.ID"
                            . ($My?(" join CommandCompetitor CC on CC.Command=Com.ID "
                            . " join Competitor C on C.ID=CC.Competitor and C.WID=".$Competitor->id):"")
                            . " where E.Competition=".$r['ID']." and E.Round=1"
                            . " group by E.ID, D.Code, D.Name "
                            ." order by D.Name");
            
            
                  $j=0; 
                  
                  $diciplines=DataBaseClass::getRows();
                  foreach($diciplines as $discipline){ ?>
                        <a href="<?= LinkEvent($discipline['Event']) ?>"><span 
                                <?php if($comp_status==0){?>
                                    <?php if($discipline['notResult']){ ?>
                                        class="nonResult"
                                    <?php }else{ ?>
                                        class="existsResult"
                                    <?php } ?>
                                <?php } ?>
                            ><?= ImageDiscipline($discipline['Code'],25,$discipline['Name']);?></span></a>
                        <?php $j++;
                        if($j==6){
                            $j=0;
                        echo "<br>";
                    }
                  } 
                  if($r['EventPicture']){ ?>
                        <a href="<?= PageIndex() ?>Competition/<?= $r['WCA']?>/MosaicBuilding">
                           <img title='Mosaic Building'  align="center" title="Picture" height=25px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
                       </a>    
                   <?php } ?>
        </td>
        <?php if($Delegate){ ?>
            <td>
                <?php if($comp_status==1 or $comp_status==0){
                    DataBaseClass::FromTable("CompetitionDelegate","Competition=".$r['ID']);
                    $delegates=[];
                    foreach(DataBaseClass::QueryGenerate() as $tt){
                       $delegates[]=$tt['CompetitionDelegate_Delegate'];
                    }
                    
                    DataBaseClass::FromTable("CompetitionReport","Competition=".$r['ID']);
                    DataBaseClass::Where_current("Report<>''");
                    $reports=[];
                    foreach(DataBaseClass::QueryGenerate() as $tt){
                       $reports[]=$tt['CompetitionReport_Delegate'];
                    } ?>
                    <a href="<?= PageIndex()?>Competition/<?= $r['WCA'] ?>/report">
                        <?php if(in_array($Delegate['Delegate_ID'],$delegates)){ ?>
                            <?php if(in_array($Delegate['Delegate_ID'],$reports)){ ?>
                                <?php if(sizeof($reports)){ ?>
                                    <span class="message">Report<?= sizeof($reports)>1?('s '.sizeof($reports).''):''?></span>
                            <?php } ?>
                            <?php }else{ ?>
                                    <span class="error">Create</span>
                            <?php } ?>
                        <?php }else{ ?>
                            <?php if(sizeof($reports)){ ?>
                                <span class="message">Report<?= sizeof($reports)>1?('s '.sizeof($reports).''):''?></span>
                            <?php } ?>
                        <?php } ?>                
                    </a>
                <?php } ?>
            </td>
        <?php } ?>
    </tr>
<?php } ?>
    <?php if (!sizeof($results)){?>
        <h3>No competitions found</h3>
    <?php } ?>
    
    </table>
