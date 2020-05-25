<?php if(CheckAdmin()){ 
    
    DataBaseClass::Query("Select distinct C.WID from Competitor C  where  C.WID is not null and WCAID=''");         
    $Beginners=DataBaseClass::getRows();
    
    DataBaseClass::Query("Select * from Competitor C where  C.WID is null and WCAID!=''");
    $Repairs1=DataBaseClass::getRows();
    
    DataBaseClass::Query("Select * from Competitor C where  C.WID is null and WCAID='' and Name!=''");
    $Repairs2=DataBaseClass::getRows();
    
    DataBaseClass::Query("Select * from Competitor C where  Country=''");
    $NonCountries=DataBaseClass::getRows();
    
    
    DataBaseClass::Query("Select C.Name,C.ID from Competitor C where (C.WID is null  or C.WCAID='') and C.ID not in (Select Competitor from CommandCompetitor)");
    $Deletes=DataBaseClass::getRows();
    ?>

    <?php if(sizeof($Beginners) or sizeof($Repairs1) or sizeof($Repairs2)){ ?>
        <div class='form'>
                <?php if(sizeof($Beginners)){ ?><span class="error"><?= sizeof($Beginners) ?></span> without wca_id &#9642; <?php } ?>
                <?php if(sizeof($Repairs1) ){ ?><span class="error"><?= sizeof($Repairs1) ?></span> without user_id &#9642;<?php } ?>
                <?php if(sizeof($Repairs2)){ ?><span class="error"><?= sizeof($Repairs2) ?></span> only name &#9642;<?php } ?>
                <?php if(sizeof($Deletes)){ ?><span class="error"><?= sizeof($Deletes) ?></span> for removal &#9642;<?php } ?>
        <a href="<?= PageIndex()?>Actions/CompetitorsUpdate">Update</a>
                (<?= GetValue('CompetitorsUpdate'); ?>)
        </div>
    <?php } ?>
    <?php if(sizeof($NonCountries)){?>
        <div class='form'>
            without country
             <?php foreach($NonCountries as $NonCountry){?>
                <a target="_blank" href="<?= LinkCompetitor($NonCountry['ID'])?>"><?= $NonCountry['ID']?></a>
             <?php } ?>
        </div>
    <?php } ?>

<?php } ?>

    <?php
    
    if(isset($_GET['Country'])){
        $country_filter=DataBaseClass::Escape($_GET['Country']);
        if(!$country_filter){
            $country_filter='0';
        }
    }else{
        $country_filter='0';    
    }
    ?>
    
    <script>
        <?php if($country_filter!='0'){ ?>
            document.title = "Unofficial Events ▪ Competitors ▪ <?= CountryName($country_filter) ?> ";
        <?php }else{ ?>
            document.title = "Unofficial Events ▪ Competitors";
        <?php } ?>
    </script>
        
    <?php 
    DataBaseClass::Query("Select C.Country, count(distinct C.ID) count from Competitor C "
            . " join CommandCompetitor CC on CC.Competitor=C.ID "
            . " join Command Com on CC.Command=Com.ID "
            . " join Event E on E.ID=Com.Event "
            . " where Com.Decline!=1 and E.Round = (select max(E2.Round) from Event E2 where E2.Competition=E.Competition)"
            . "group by C.Country order by 2 desc ");
    
    $competitors_countries=DataBaseClass::getRows();
    $competitors_countries_all=0;
    foreach($competitors_countries as $competitors_country){
        $competitors_countries_all+=$competitors_country['count'];
    }      
    DataBaseClass::FromTable("Competitor");
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Command");
    DataBaseClass::Where("Command","Decline!=1");
    DataBaseClass::OrderClear("Competitor","Name");
    if($country_filter){
        DataBaseClass::Where("Competitor","Country='".$country_filter."'");
    }
    DataBaseClass::Select("Distinct Cm.*");
    
    $competitors=DataBaseClass::QueryGenerate();
    
    $sort='';
    $sort_name='Name';
    if(isset($_GET['Sort']) and in_array($_GET['Sort'],
        array('Name','WCAID','Country','Competitions','Events','Medals','Gold','Silver','Bronze'))){
        if(in_array($_GET['Sort'],array('Name','WCAID'))){
            $sort=$_GET['Sort'].',';
        }else{
            $sort=$_GET['Sort'].' desc,';
        }
        $sort_name=$_GET['Sort'];
    }
    
    DataBaseClass::Query("
        Select C.WCAID WCAID, C.Name, C.Country,C.ID,
        sum(case when Com.Place=1 then 1 else 0 end) Gold,
        sum(case when Com.Place=2 then 1 else 0 end) Silver,
        sum(case when Com.Place=3 then 1 else 0 end) Bronze,
        sum(case when Com.Place in (1,2,3) then 1 else 0 end) Medals,
        count(distinct Cm.ID) Competitions,
        count(distinct D.ID) Events
        from Competitor C join CommandCompetitor CC on CC.Competitor=C.ID 
        join Command Com on Com.ID=CC.Command and Com.Decline!=1
        join Event E on E.ID=Com.Event
        join DisciplineFormat DF on E.DisciplineFormat=DF.ID
        join Discipline D on D.ID=DF.Discipline
        join Competition Cm on Cm.ID=E.Competition
        where ('$country_filter'='0' or '$country_filter'=C.Country)
        and E.Round = (select max(E2.Round) from Event E2 where E2.Competition=E.Competition)
        group by C.WCAID, C.Name, C.Country,C.ID 
        order by 
        $sort
        C.Name,
        WCAID,
        Competitions desc,
        Events desc,
        Medals desc,
        Gold desc,
        Silver desc,
        Bronze desc
            ");

    $competitors_medals=DataBaseClass::getRows(); ?>
    <h2>
        <?php if($country_filter!='0'){ ?>
            <?= ImageCountry($country_filter, 50)?> Competitors from <?= CountryName($country_filter)?>
        <?php }else{ ?>
            <?= ImageCountry('', 50)?> All competitors
        <?php } ?>
        <select onchange="document.location='<?= PageIndex()?>' + this.value ">
            <option <?= ($country_filter=='0')?'selected':''?> value="?Competitors">All competitors: <?= $competitors_countries_all ?></option>
            <option disabled>------</option>
            <?php foreach($competitors_countries as $competitors_country){ ?>
                    <option <?= $country_filter==$competitors_country['Country']?'selected':''?> value="?Competitors&Country=<?= $competitors_country['Country']?>">        
                        <?= CountryName($competitors_country['Country']) ?> [<?= $competitors_country['Country']?>]: <?= $competitors_country['count'] ?>
                    </option> 
            <?php } ?>      
        </select>
    </h2>
    
    <select hidden tabindex="1" ID="SelectCompetitors" style="width: 800px"
                         data-placeholder="Find competition by Name or WCAID" 
                         class="chosen-select" multiple onchange="if(this.value){
                location.href = '<?= PageIndex()?>Competitor/' + this.value;
            }">
            <option value=""></option>
            <?php 
            foreach($competitors as $competitor){ ?>
                <option value="<?= $competitor['ID'] ?>"> <?= $competitor['WCAID'] ?> &#9642; <?= $competitor['Name'] ?> &#9642; <?= CountryName($competitor['Country']) ?>  </option>    
            <?php } ?>
    </select>
    
    <table class="Competitors">
    <?php 
    foreach($competitors_medals as $i=>$competitors_medal){ ?>
            <?php if(ceil($i/10)*10==$i){ ?>
                <?php if($i==0){ 
                        $urlSort=PageIndex()."?Competitors".($country_filter?"&Country=".$country_filter:"")."&Sort="; ?>
                        <tr class="tr_title">
                        <td/>
                        <td><a href="<?= $urlSort ?>Name" class="<?= $sort_name!='Name'?'local_link':'select_link'?>">Competitor</a></td>
                        <td><a href="<?= $urlSort ?>WCAID" class="<?= $sort_name!='WCAID'?'local_link':'select_link'?>">WCAID</a></td>
                        <?php if($country_filter=='0'){ ?><td><a href="<?= $urlSort ?>Country" class="<?= $sort_name!='Country'?'local_link':'select_link'?>">Country</a></td><?php } ?>    
                        <td><a href="<?= $urlSort ?>Competitions" class="<?= $sort_name!='Competitions'?'local_link':'select_link'?>">Competitions</a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>Events" class="<?= $sort_name!='Events'?'local_link':'select_link'?>">Events</a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>Medals" class="<?= $sort_name!='Medals'?'local_link':'select_link'?>">Medals</a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>Gold" class="<?= $sort_name!='Gold'?'local_link':'select_link'?>">Gold</a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>Silver" class="<?= $sort_name!='Silver'?'local_link':'select_link'?>">Silver</a></td> 
                        <td class="attempt"><a href="<?= $urlSort ?>Bronze" class="<?= $sort_name!='Bronze'?'local_link':'select_link'?>">Bronze</a></td>
                    </tr> 
                <?php }else{ ?>
                    <tr class="tr_title">
                       <td/>
                       <td>Competitor</td>
                       <td>WCAID</td>
                       <?php if($country_filter=='0'){ ?><td>Country</td><?php } ?>    
                       <td>Competitions</td>
                       <td class="attempt">Events</td>
                       <td class="attempt">Medals</td>
                       <td class="attempt">Gold</td>
                       <td class="attempt">Silver</td> 
                       <td class="attempt">Bronze</td>
                   </tr>   
                <?php } ?>
            <?php } ?>
        
    
            <tr class="<?= ceil(($i+1)/10)*10==($i+1)?'no_border':'' ?>">
                <td><?= $i+1 ?></td>
                <td class="border-left-solid">
                    <a href="<?= PageIndex() ?>Competitor/<?= $competitors_medal['WCAID']?$competitors_medal['WCAID']:$competitors_medal['ID'] ?>">            
                        <?= trim(explode("(",$competitors_medal['Name'])[0]) ?> 
                    </a>
                </td>
                <td>
                   <?= $competitors_medal['WCAID']; ?> 
                </td>
                <?php if($country_filter=='0'){ ?>
                    <td>
                        <?= ImageCountry($competitors_medal['Country'], 15)?>
                        <?= CountryName($competitors_medal['Country']) ?>
                    </td>
                <?php } ?>
                <td class="attempt border-left-solid"><?= $competitors_medal['Competitions']; ?></td>
                <td class="attempt"><?= $competitors_medal['Events']; ?></td>
                <td class="attempt border-left-solid" ><b><?= $competitors_medal['Medals']?$competitors_medal['Medals']:"" ?></b></td>
                <td class="attempt"><?= $competitors_medal['Gold']?$competitors_medal['Gold']:"" ?></td>
                <td class="attempt"><?= $competitors_medal['Silver']?$competitors_medal['Silver']:"" ?></td>
                <td class="attempt"><?= $competitors_medal['Bronze']?$competitors_medal['Bronze']:"" ?></td>
            </tr>    
    <?php } ?>
    </table>

<script>
$("#SelectCompetitors").show();
</script>