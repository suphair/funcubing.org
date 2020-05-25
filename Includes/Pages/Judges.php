<div class="judges_list" id="Judges">   
<?php
DataBaseClass::Query("Select DATEDIFF(max(C.EndDate),now()) Latestactivity,"
        . " max(C.EndDate)  EndDate,"
        . " DATEDIFF(case when now()<max(C.EndDate) then max(C.EndDate) else now() end ,min(C.EndDate)) Period, "
        . "D.*,DelC.Country, count(distinct Cm.ID) Count_Competitors, count(distinct C.ID) Count_Competitions"
        . "   from Delegate D left outer join Competitor DelC on "
        . " ((D.WCA_ID and D.WCA_ID=DelC.WCAID) or (D.WID and D.WID=DelC.WID)) "
        . " left outer join CompetitionDelegate CD on CD.Delegate=D.ID"
        . " left outer join Competition C on C.ID=CD.Competition"
        . " left outer join Event E on E.Competition=C.ID"
        . " left outer join Command Com on Com.Event=E.ID and Com.Decline!=1"
        . " left outer join CommandCompetitor CC on CC.Command=Com.ID"
        . " left outer join Competitor Cm on Cm.ID=CC.Competitor "
        . " where D.Status='Active' "
        . " group by D.ID, DelC.Country "
        . " order by DelC.Country,  D.Name");
$Judge_rows=DataBaseClass::GetRows();

$Judges=array();
foreach($Judge_rows as $judge){
    if($judge['Admin']){
        $Judges['Senior Judges'][]=$judge;
    }elseif($judge['Candidate']){
        $Judges['Candidate Judges'][]=$judge;
    }else{
        $Judges['Judges'][]=$judge;   
    }
} 
?>
    <h2><a href="<?= PageIndex()."RequestCandidate" ?>">Join the Judges</a>
    </h2><br>
    <table class="Disciplines">
        <tr class="tr_title">
            <td>Name</td>
            <td>Country</td>
            <td></td>
            <td>Competitions</td>
            <?php if(GetDelegateData()){ ?>
                <td>Days</td>
                <td>Cs/m</td>
                <td>Latest activity</td>
            <?php } ?>
           
            <td>Competitors</td>
            <td>Events</td>
        </tr>
        <?php foreach($Judge_rows as $judge){ ?>
            <tr>
                <td>
                    <a href="<?= LinkDelegate($judge['WCA_ID'])?>">
                    <?= Short_Name($judge['Name']) ?>
                </a></td>
                <td><?= ImageCountry($judge['Country'], 30)?> <?= CountryName($judge['Country'])?></td>
                <td><?php 
                if($judge['Admin']){ ?>
                    <span class='message'>Senior Judge</span>
                <?php }elseif($judge['Candidate']){ ?>
                    Junior Judge
                <?php }else{ ?>
                    Middle Judge
                <?php } ?>
                </td>
                <td class="attempt">
                    <?= $judge['Count_Competitions'] ?>
                </td>

                <?php if(GetDelegateData()){ ?>
                    <td align="right" class="border-left-dotted">    
                        <?= $judge['Period'] ?>
                    </td>
                    <td align="right">
                        <?php if ($judge['Count_Competitions']>0 and $judge['Period']>30){ 
                            $r= round($judge['Count_Competitions']/$judge['Period']*30,1); ?>
                            <span class="
                                <?= $r<=0.4?'error':''?>
                                <?= $r>=1?'message':''?>">
                                <?= $r ?>   
                            </span>   
                        <?php } ?>  
                    </td>    
                    <td align="right" class="border-right-dotted"> 
                        <span class="
                        <?= $judge['Latestactivity']<-120?'error':''?>
                        <?= $judge['Latestactivity']>-30?'message':''?>">
                            <?= $judge['Latestactivity'] ?>
                        </span>
                        <span class="<?= strtotime($judge['EndDate'])>time()?'message':'' ?>">
                        [<?= date_range($judge['EndDate']); ?>]
                        </span>
                    </td>
                <?php } ?>

                <td class="attempt">
                    <?= $judge['Count_Competitors'] ?>
                </td>
                <td>
                    <?php DataBaseClass::FromTable("Delegate","ID=".$judge['ID']);
                    DataBaseClass::Join_current("CompetitionDelegate");
                    DataBaseClass::Join_current("Competition");
                    DataBaseClass::Join_current("Event");
                    DataBaseClass::Join_current("DisciplineFormat");
                    DataBaseClass::Join_current("Discipline");
                    DataBaseClass::OrderClear("Discipline", "Code");
                    DataBaseClass::Select("Distinct D.*");
                    $j=0; 
                    foreach(DataBaseClass::QueryGenerate() as $discipline){ ?>
                          <a href="<?= LinkDiscipline($discipline['Code']) ?>"><?= ImageDiscipline($discipline['Code'],30,$discipline['Name']);?></a>
                          <?php $j++;
                          if($j==6){
                              $j=0;
                          echo "<br>";
                      }
                    } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>