<?php 
$request=Request();
$Code=DataBaseClass::Escape($request[1]);
DataBaseClass::Query(""
        . " Select D.ID, D.Code, D.Name, coalesce(max(A.Attempt),1) Attempt"
        . " from Discipline D "
        . " left outer join DisciplineFormat DF on DF.Discipline=D.ID "
        . " left outer join Event E on E.DisciplineFormat=DF.ID "
        . " left outer join Command Com on Com.Event=E.ID"
        . " left outer join Attempt A on A.Command=Com.ID and A.Attempt is not null"
        . " where D.Code='$Code'"
        . " group by D.ID");

$discipline=DataBaseClass::getRow();
$ID=$discipline['ID'];
include "Disciplines_Line.php" ?>
<hr class="hr_round">
<?php
if($Code=='MosaicBuilding' ){ ?>
    <h1>
        <img align="center" title="Picture" height=50px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
            Mosaic Building
    </h1>
    
<?php DataBaseClass::Query("Select distinct C.ID, C.Name, C.WCA,C.StartDate from MosaicBuilding MB "
            . " join Competition C on C.ID=MB.Competition "
            . " order by C.StartDate desc");
    $competitions=DataBaseClass::getRows();

    foreach($competitions as $competition){ ?>
<h3><a href="<?= PageIndex() ?>Competition/<?= $competition['WCA'] ?>/MosaicBuilding"><?= $competition['Name']?></a></h3>
        <?php DataBaseClass::FromTable("MosaicBuilding","Competition=".$competition['ID']);
              DataBaseClass::OrderSpecial("length(MB.Description) desc");
            foreach(DataBaseClass::QueryGenerate() as $mosaic_building){ 
                $competitors=[];
                $Description=$mosaic_building['MosaicBuilding_Description']; 
                $template = "/\{(.*)\}/";
                preg_match($template, $Description, $maches); 
                if(isset($maches[1])){
                    $Description=str_replace("{".$maches[1]."}","",$Description);
                    $competitors=explode(",",$maches[1]);
                } ?>
                <div class="form">
                    <h3><?= $Description?></h3>
                    <?php foreach($competitors as $competitor){ 
                        $competitor=trim($competitor);
                        DataBaseClass::Query("Select * from Competitor where WCAID='$competitor' or Name like '$competitor%'");
                        $comp= DataBaseClass::getRow();
                    ?>
                    <p><?php
                    if(is_array($comp)){ ?>
                        <a href="<?= PageIndex()?>Competitor/<?= $comp['ID'] ?>"><?= Short_Name($comp['Name'])?></a>
                    <?php }else{ ?>
                        <?= $competitor ?>
                    <?php } ?>
                    </p>
                <?php } ?>
                    <?php DataBaseClass::FromTable("MosaicBuildingImage","MosaicBuilding=".$mosaic_building['MosaicBuilding_ID']);
                        foreach(DataBaseClass::QueryGenerate() as $file){ ?>
                            <div style="width:300px;height: 200px; float: left; ">
                                    <img class="imageSmall" style=" max-height: 100%;max-width: 100%;
                                         display: block; margin: auto;height: auto;
                          " src="<?= PageIndex()?>Image/MosaicBuilding/<?= $file['MosaicBuildingImage_Filename']?>">
                            </div>  
                    <?php } ?>
                </div>
            <?php } ?>
    <?php } ?>
<?php include 'MosaicBuilding_Show.php'; ?>
<?php }else{

$FilterAverage =    isset($_GET['Single'])?'Single':'Average';
$FilterCountry =   isset($_GET['Country']) ? 
                    DataBaseClass::Escape($_GET['Country']) : 
                    'All';
$FilterResults =   isset($_GET['Results'])?'Results':'Persons';

if($Code=='Scrambling'){
    $FilterAverage ='Sum';
}
?>

<?php
function sort_by_vOrder($a,$b){
    if(!isset($a['vOrder']) or !isset($b['vOrder']))return false;
    return $a['vOrder']>$b['vOrder'];
}    
            

$Scrambling=[49=>2,59=>10];
 
DataBaseClass::Query("
    Select Com.vCountry, count(distinct Com.vCompetitorIDs) count ,'Single' type
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and vCountry!='' and ((D.ID='$ID' and A.Special='Best')".       
(isset($Scrambling[$ID])?" or (A.Attempt=".$Scrambling[$ID]." and D.Code='Scrambling' )":'').
")
group by vCountry
union 
  Select 'All', count(distinct Com.vCompetitorIDs) count ,'Single' type
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and ((D.ID='$ID' and A.Special='Best')".       
(isset($Scrambling[$ID])?" or (A.Attempt=".$Scrambling[$ID]." and D.Code='Scrambling' )":'').
")
union
    Select Com.vCountry, count(distinct Com.vCompetitorIDs) count ,'Average' type
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Mean','Average') and vCountry!='' and D.ID='$ID'
group by vCountry
union 
  Select 'All', count(distinct Com.vCompetitorIDs) count ,'Average' type
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Mean','Average') and D.ID='$ID'
union
    Select Com.vCountry, count(distinct Com.vCompetitorIDs) count ,'Sum' type
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Sum') and vCountry!='' and D.ID='$ID'
group by vCountry
union 
  Select 'All', count(distinct Com.vCompetitorIDs) count ,'Sum' type
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join CommandCompetitor CC on Com.ID=CC.Command
    join Competitor C on C.ID=CC.Competitor
    join Event E on E.ID=Com.Event
    join Competition Cn on Cn.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
    where A.IsDNF=0 and A.IsDNS=0 and A.Special in ('Sum') and D.ID='$ID'");

$Countries=[];
foreach(DataBaseClass::getRows() as $country){
    if(!isset($Countries[$country['vCountry']])){
        $Countries[$country['vCountry']]=['Name'=>CountryName($country['vCountry']),'Single'=>0,'Average'=>0];
    }
    $Countries[$country['vCountry']][$country['type']]=$country['count'];
    
}
$Countries['All']['Name']='.';
function SortByCountry($a,$b){
    return $a['Name']>$b['Name'];
}
uasort($Countries,'SortByCountry');
$Countries['All']['Name']='All countries';

$Results=[];



if($FilterAverage=='Single'){
    DataBaseClass::Query("
    Select Com.vCompetitorIDs,Com.vName, Com.vCountry, Com.Video,
    A.vOut, A.vOrder, A.Attempt,
    C.WCA Competition_WCA,C.Name Competition_Name,C.Country Competition_Country,  E.Round,E.vRound, E.ID Event_ID,D.ID,D.Code
    from `Attempt` A 
    join Command Com on Com.ID=A.Command
    join Event E on E.ID=Com.Event
    join Competition C on C.ID=E.Competition
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    join Discipline D on D.ID=DF.Discipline
where A.IsDNF=0 and A.IsDNS=0 and ((D.ID='$ID' and A.Attempt!=0)".       
(isset($Scrambling[$ID])?" or (A.Attempt=".$Scrambling[$ID]." and D.Code='Scrambling' )":'').
")
    and '$FilterCountry' in ('All',vCountry)
    order by vOrder,vName");
    $Results=DataBaseClass::getRows();
    
}

if($FilterAverage=='Average'){
    $sql="
        Select Com.vCompetitorIDs,Com.vName, Com.vCountry,Com.Video,
        A.vOut, A.vOrder, 
        C.WCA Competition_WCA,C.Name Competition_Name,C.Country Competition_Country, E.Round,E.vRound, E.ID Event_ID";
    
    for($i=1;$i<=$discipline['Attempt'];$i++){
        $sql.=",case when A{$i}.Except then concat('(',A{$i}.vOut,')') else A{$i}.vOut end  Attempt{$i}";
    }
    
    $sql.="
        from `Attempt` A 
        join Command Com on Com.ID=A.Command
        join Event E on E.ID=Com.Event
        join Competition C on C.ID=E.Competition
        join DisciplineFormat DF on DF.ID=E.DisciplineFormat
        join Discipline D on D.ID=DF.Discipline";

    for($i=1;$i<=$discipline['Attempt'];$i++){
        $sql.=" left outer join `Attempt` A{$i} on A{$i}.Command=A.Command and A{$i}.Attempt={$i}";
    }
    $sql.="
        where D.ID='$ID' and A.Special in('Average','Mean')
        and A.IsDNF=0 and A.IsDNS=0 
        and '$FilterCountry' in ('All',vCountry)
        order by vOrder,vName";

    DataBaseClass::Query($sql);
    $Results=DataBaseClass::getRows(); 
}


if($FilterAverage=='Sum'){
    $sql="
        Select Com.vCompetitorIDs,Com.vName, Com.vCountry,Com.Video,
        A.vOut, A.vOrder, 
        C.WCA Competition_WCA,C.Name Competition_Name,C.Country Competition_Country, E.Round,E.vRound, E.ID Event_ID";
    
    for($i=1;$i<=$discipline['Attempt'];$i++){
        $sql.=",case when A{$i}.Except then concat('(',A{$i}.vOut,')') else A{$i}.vOut end  Attempt{$i}";
    }
    
    $sql.="
        from `Attempt` A 
        join Command Com on Com.ID=A.Command
        join Event E on E.ID=Com.Event
        join Competition C on C.ID=E.Competition
        join DisciplineFormat DF on DF.ID=E.DisciplineFormat
        join Discipline D on D.ID=DF.Discipline";

    for($i=1;$i<=$discipline['Attempt'];$i++){
        $sql.=" left outer join `Attempt` A{$i} on A{$i}.Command=A.Command and A{$i}.Attempt={$i}";
    }
    $sql.="
        where D.ID='$ID' and A.Special in('Sum')
        and A.IsDNF=0 and A.IsDNS=0 
        and '$FilterCountry' in ('All',vCountry)
        order by vOrder,vName";

    DataBaseClass::Query($sql);
    $Results=DataBaseClass::getRows(); 
}

if($FilterResults!='Results'){
    $exceptAttempCommand=[];
    foreach($Results as $r=>$Result){
        if(in_array($Result['vCompetitorIDs'],$exceptAttempCommand)){
           unset($Results[$r]); 
        }else{
            $exceptAttempCommand[]=$Result['vCompetitorIDs'];
        }
    }
} 
 ?>

<a name='Results'>
<h1 class="<?= $discipline['Status'] ?>">
    <?= ImageDiscipline($discipline['Code'],50) ?>
    <a href="<?= LinkDiscipline($discipline['Code'])?>"><?= $discipline['Name'] ?></a>
    <?php if(CheckAdmin()){ ?>
        <a href="<?= LinkDiscipline($discipline['Code'])?>/config">&#9642; Setting</a>
    <?php } ?>
</h1>
 <h2>
    <?= regulation_block($discipline); ?>    
</h2>    
    <?= scramble_block($discipline['ID']);?>
    <?= scorecard_block($discipline['ID']);?>
    <?php
        DataBaseClass::Query("
            select distinct C.ID, C.Name, C.WCA, C.Country, C.StartDate, C.EndDate, C.City
            from `Discipline` D 
            join DisciplineFormat DF on D.ID=DF.Discipline 
            join Event E on DF.ID=E.DisciplineFormat 
            join Competition C on C.ID=E.Competition 
            where D.ID='$ID' and C.StartDate>now() order by C.StartDate ");
        $EventsAll=DataBaseClass::getRows();
            
        if(sizeof($EventsAll)){ ?>
    <br>
    <div class="form">
        Upcoming competitions
            <?php foreach($EventsAll as $competition){ ?> 
                <nobr>&nbsp;
                    <img width="30" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competition['Country'])?>.png">
                    <a href="<?= LinkCompetition($competition['WCA'])?>/<?= $discipline['Code'] ?>"><?= $competition['Name']; ?></a>
                </nobr>
            <?php } ?>
    </div>            
<?php }  ?>

<h2>
    <?php if($FilterCountry!='All') { ?>
        <?= CountryName($FilterCountry); ?>  
        <img width="30" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($FilterCountry)?>.png">
    <?php }else{ ?>
        All countries
    <?php } ?>    
    &#9642;
    <?= $FilterAverage ?> &#9642;
    <?= $FilterResults ?>
</h2>
    
    
<form name="Filter">
    <select ID="FilterCountry" onchange="reload();">
        <?php foreach($Countries as $countryName=>$countryAttempts){ ?>
        <option value="<?= $countryName ?>" <?= $countryName==$FilterCountry?'selected':'' ?> >
            <?= $countryAttempts['Name'] ?>
            <?php if ($Code!=='Scrambling'){ ?>
                 [ <?= $countryAttempts['Average'] ?> / <?= $countryAttempts['Single'] ?> ] 
            <?php } else { ?>
                 [ <?= $countryAttempts['Sum'] ?> ]
            <?php } ?>
        </option>
        <?php } ?>
    </select>
    &nbsp;&#9642;&nbsp;
<?php 
if($Code!=='Scrambling'){
    foreach(['Average','Single'] as $type){ ?> 
        <input hidden value="<?= $type ?>" type="radio" class='FilterAverage' ID="FilterAverage_<?= $type ?>" name="FilterAverage" <?= $type==$FilterAverage?'checked':'' ?> onchange="reload();">
            <label  onclick="reload();" for="FilterAverage_<?= $type ?>"><span class='badge <?= $type==$FilterAverage?'select':'' ?>'><?= $type ?></span></label>
    <?php } ?>    
    &nbsp;&#9642;&nbsp;
<?php } ?>    
<?php foreach(['Persons','Results'] as $type){ ?> 
    <input hidden value="<?= $type ?>" type="radio" class='FilterResults'  ID="FilterResults_<?= $type ?>" name="FilterResults" <?= $type==$FilterResults?'checked':'' ?> onchange="reload();">
        <label  onclick="reload();" for="FilterResults_<?= $type ?>"><span class='badge <?= $type==$FilterResults?'select':'' ?>'><?= $type ?></span></label>
<?php } ?>    
    <script>
    function reload(){
        let str = [];
        var FilterCountry=$('#FilterCountry').val();
        var FilterAverage=$('.FilterAverage:checked').val();
        var FilterResults=$('.FilterResults:checked').val();
        
        if(FilterCountry!=='All'){
            str.push('Country=' +FilterCountry);
        }
        if(FilterAverage!='Average'){
            str.push(FilterAverage);
        }
        if(FilterResults!='Persons'){
            str.push(FilterResults);
        }
                  
    var url= '<?= PageIndex() ?>Discipline/<?= $discipline['Code'] ?>'+'/?' + str.join('&') + "#Results";
    location.href = url;
    }    
    </script>
        
    
    
</form>

<table class='result'>
    <tr class='tr_title'>
        <td/>
        <td width='<?= 150* $discipline['Competitors'] ?>px' >Competitor</td>
        <td><nobr>Citizen of</nobr></td>
        <td class='attempt select'><?=$FilterAverage ?></td>
        <td/>
        <?php if($FilterResults=='Results'){?>
            <?php if($FilterAverage=='Single'){ ?>
                <td>Competition &#9642; Round &#9642; Attempt</td>
            <?php }else{ ?>
                <td>Competition &#9642; Round</td>
            <?php } ?>
        <?php }else{ ?>
            <td>Competition</td>
        <?php } ?>
        <?php if($FilterAverage=='Average'){ ?>
            <?php for($i=1;$i<=$discipline['Attempt'];$i++){ ?>
                <td align="center"><?= $i ?></td>
            <?php } ?>
        <?php } ?>
        <?php if($FilterAverage=='Sum'){ ?>
            <?php for($i=1;$i<=$discipline['Attempt'];$i++){ ?>
                    <td align="center">
                    <?php if($image=IconAttempt($discipline['Code'],$i)){ ?>
                          <img src="<?= PageIndex() ?>/<?= $image ?>" width="20px">
                    <?php }else{ ?>
                        <?= $i ?>
                    <?php } ?>
                </td>
            <?php } ?>
        <?php } ?>
        
    </tr>
<?php 
$n=0; $fl=false; $prev=0;
    foreach($Results as $Result){ 
        $n++;
        $fl=($prev!==$Result['vOut']);
        if($fl){
            $new=$n;
        }
        $prev=$Result['vOut']; ?>    
    <tr>
        <td>
        <?php if($fl){ ?>
            <?= $new ?>
        <?php } else{ ?>
            <font size=2 style='color:var(--gray)'><?= $new ?></font>
        <?php } ?>    
        </td>
        <td>
            <?php
               $Competitors_Name=Explode(",",$Result['vName']);
               $Competitors_ID=Explode(",",$Result['vCompetitorIDs']);
               $competitors=[];
            ?>        
            <?php foreach($Competitors_Name as $i=>$Competitor_Name){ 
                $Competitor_Name=trim($Competitor_Name);?>
                <?php ob_start(); 
                if(sizeof(explode(' ',$Competitor_Name))>2){
                    $Competitor_Name=str_replace(' de ',' de&nbsp;',$Competitor_Name);
                    ?><a href='<?= LinkCompetitor( trim($Competitors_ID[$i]) )?>'><?=  $Competitor_Name ?></a><?php 
                }else{
                    ?><nobr><a href='<?= LinkCompetitor( trim($Competitors_ID[$i]) )?>'><?=  $Competitor_Name ?></a></nobr><?php 
                }
                $competitors[]=ob_get_clean(); ?>
            <?php } ?>
            <?= implode(" &#9642; ",$competitors); ?>        
        </td>
        <td> <nobr><?php if($Result['vCountry']){ ?>
                <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($Result['vCountry'])?>.png">
                <?= CountryName($Result['vCountry']) ?>
            <?php }else{ ?>
                -
            <?php } ?></nobr>
       </td>                                 
        <td class='attempt select border-right-dotted'>
            <?=  $Result['vOut'] ?>
        </td>
        <td>
            <?php if($Result['Video']){?>    
                <a target=_blank" href="<?= $Result['Video'] ?>"><img class="video" src="<?= PageIndex()?>Image/Icons/Video.png"></a>
            <?php } ?>
        </td>
        <td class="border-right-dotted">
            <nobr>
                <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($Result['Competition_Country'])?>.png">
                <a href="<?= LinkEvent($Result['Event_ID']) ?>"><?=  $Result['Competition_Name'] ?></a>
                <?php if($FilterResults=='Results'){?>
                    R<?= $Result['Round'] ?>
                    <?php if($FilterAverage=='Single' and $ID==$Result['ID']){ ?>
                        A<?= $Result['Attempt'] ?>
                    <?php } ?>
                    <?php if($FilterAverage=='Single' and $ID!=$Result['ID']){ ?> 
                        <?= ImageDiscipline($Result['Code'],20); ?>       
                    <?php } ?>
                <?php }else{ ?>
                    <?php if($FilterAverage=='Single' and $ID!=$Result['ID']){?>        
                        <?= ImageDiscipline($Result['Code'],20); ?>       
                    <?php } ?>
                <?php } ?>    
            </nobr>
        </td>
        <?php if($FilterAverage=='Average'){
            for($i=1;$i<=$discipline['Attempt'];$i++){ ?>
                <td align="center">
                    <?= $Result['Attempt'.$i] ?>
                </td>
            <?php }    
        } ?>
        <?php if($FilterAverage=='Sum'){
            for($i=1;$i<=$discipline['Attempt'];$i++){ ?>
                <td align="center">
                    <?php if($Result['Attempt'.$i]=='DNF'){ ?>
                        <span class="gray"><?= $Result['Attempt'.$i] ?></span>
                    <?php }else{ ?>
                        <?= $Result['Attempt'.$i] ?>
                    <?php } ?>
                </td>
            <?php }    
        } ?>
    </tr>    
<?php } ?>
</table>    

    
<?php
DataBaseClass::Query("
    select distinct C.ID, C.Name, C.WCA, C.Country, C.StartDate, C.EndDate, C.City
    from `Discipline` D 
    join DisciplineFormat DF on D.ID=DF.Discipline 
    join Event E on DF.ID=E.DisciplineFormat 
    join Competition C on C.ID=E.Competition 
    where D.ID='$ID' and C.EndDate<now() order by C.EndDate ");
$EventsAll=DataBaseClass::getRows();

if(sizeof($EventsAll)){ ?>
<br>
<div class="form">
    Past competitions<br>
        <?php foreach($EventsAll as $competition){ ?> 
            <nobr>&nbsp;
                <img width="30" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competition['Country'])?>.png">
                <a href="<?= LinkCompetition($competition['WCA'])?>/<?= $discipline['Code'] ?>"><?= $competition['Name']; ?></a>
            </nobr>
        <?php } ?>
</div>            
<?php }  ?>
    
    
<?php 
}