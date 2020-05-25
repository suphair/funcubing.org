<h2>Unofficial events
    <?php if(CheckAdmin()){ ?>         
        <a href="<?= LinkDiscipline("Add") ?>"><?= svg_blue(20,"Add event")?>Add event </a>
    <?php } ?>
</h2>
    <?php 
    $isAdmin=CheckAdmin();
    $Competitor=GetCompetitorData();

    
    DataBaseClass::Query("select D.ID,D.Code,D.Status,D.Name,D.Competitors, count(distinct C.ID) countCompetitors, count(distinct E.Competition) countCompetitions,
    (sum(case when Com.Place>0 then 1 else 0 end)>0) AttemptExists
    from `Discipline` D
    left outer join `DisciplineFormat` DF on DF.Discipline=D.ID
    left outer join `Event` E on E.DisciplineFormat=DF.ID And E.Competition!=129
    left outer join `Command` Com on Com.Event=E.ID 
    left outer join `CommandCompetitor` CC on CC.Command=Com.ID  
    left outer join `Competitor` C on C.ID=CC.Competitor
    ".($isAdmin?"": " where D.Status='Active'") ."
    group by D.ID
    order by (sum(case when Com.Place>0 then 1 else 0 end)>0),count(distinct C.ID) desc, count(distinct E.Competition) desc,  D.Name"); 
    $disciplines= DataBaseClass::getRows(); 
    ?>
    <table class="Disciplines">
    <?php $attempt_exists='-1';
    foreach( $disciplines as $d=>$discipline){ ?>
         <?php if($discipline['AttemptExists']!=$attempt_exists){
            $attempt_exists = $discipline['AttemptExists']; ?>
            <tr class="no_border">
                <td colspan="9"><b>
                        <?= $d?'<br>':'' ?>
                    <?php if($attempt_exists){ ?>
                        Events with results   
                    <?php }else{ ?>    
                        New events
                    <?php } ?>
                    </b>
                </td>
            </tr>
            <tr class="tr_title">
                <td>Name</td>
                <?php if($Competitor){?>
                    <td class="attempt" colspan="2"><b>Personal Record</b></td>
                    <td class="attempt" colspan="2">
                        <?php if($attempt_exists){ ?>
                            <?= ImageCountry($Competitor->country_iso2, 25) ?> 
                        <?php } ?>
                        <b>National Record</b></td>
                <?php } ?>
                <td class="attempt" colspan="2"><b>World Record</b></td>
                <td>Competitors</td>
                <td>Competitions</td>
            </tr>
            <?php if($attempt_exists){?>
            <tr >
                <td></td>
                <?php if($Competitor){?>
                    <td class="attempt">Single</td>
                    <td class="attempt">Average</td>
                    <td class="attempt">Single</td>
                    <td class="attempt">Average</td>
                <?php } ?>
                <td class="attempt">Single</td>
                <td class="attempt">Average</td>
                <td></td>
                <td></td>
            </tr>
            <?php } ?>
        <?php 
        } ?>
    <tr valign="bottom">
    <td class="border-right-dotted">
        <?= ImageDiscipline($discipline['Code'],30,$discipline['Name']) ?>  
        <a  class="<?= ($discipline['Status']=='Archive')?"archive":""; ?>" href="<?= LinkDiscipline($discipline['Code']) ?>">
                <?= $discipline['Name'] ?>
        </a>
    </td>
    
        <?php 
        $Record=array();
        $BaseSql="Select A.vOut,Com.vCountry,A.Special from Attempt A "
                . " join Command Com on Com.ID=A.Command and A.Special in ('@Special') @Country"
                . " join Event E on E.ID=Com.Event "
                . " join CommandCompetitor CC on CC.Command=Com.ID "
                . " join Competitor C on C.ID=CC.Competitor @Competitor" 
                . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat and DF.Discipline=".$discipline['ID'] 
                . " where A.Special in (select Result from Format F where F.ID=DF.Format union select ExtResult from Format F where F.ID=DF.Format)   "
                . " order by A.vOrder limit 1";
        
        $params=array("@Special","@Country","@Competitor");
        $values=array("","","");
        
        $values[0]="Best','Sum";
        DataBaseClass::Query(str_replace($params,$values,$BaseSql));
        $Record[0][0]=DataBaseClass::getRow();
        
        $values[0]="Average','Mean";
        DataBaseClass::Query(str_replace($params,$values,$BaseSql));
        $Record[0][1]=DataBaseClass::getRow();
        
        if($Competitor){
            $values[1]="and Com.vCountry='".$Competitor->country_iso2."'";
            
            $values[0]="Best','Sum";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[1][0]=DataBaseClass::getRow();

            $values[0]="Average','Mean";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[1][1]=DataBaseClass::getRow();
        }
        
        
        
        if($Competitor){ 
            $values[1]="";
            $values[2]=" and C.wid='".$Competitor->id."'";

            $values[0]="Best','Sum";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[2][0]=DataBaseClass::getRow();

            $values[0]="Average','Mean";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[2][1]=DataBaseClass::getRow();
            
            ?>
            <td class="attempt border-left-dotted">
                <?php if(isset($Record[2][0]['vOut'])){
                    $r=$Record[2][0]; ?>
                    <?php if($r['vOut']==$Record[0][0]['vOut']){?>
                        <span class="message">WR</span>
                    <?php }elseif($r['vOut']==$Record[1][0]['vOut']){?>
                        <span class="message">NR</span>
                    <?php } ?>
                        <?= $r['vOut'] ?>
                <?php } ?>
            </td>
            <td class="attempt border-right-dotted">
                <?php if(isset($Record[2][1]['vOut'])){
                    $r=$Record[2][1]; ?>
                    <?php if($r['vOut']==$Record[0][1]['vOut']){?>
                        <span class="message">WR</span>
                    <?php }elseif($r['vOut']==$Record[1][1]['vOut']){?>
                        <span class="message">NR</span>
                    <?php } ?>
                        <?= $r['vOut'] ?>
                <?php } ?>
            </td>            
            <td class="attempt border-left-dotted">
                <?php if(isset($Record[1][0]['vOut'])){
                    $r=$Record[1][0]; ?>
                    <a href='<?= PageIndex()?>?Records&Discipline=<?= $discipline['Code'] ?>&Country=<?= $Competitor->country_iso2 ?>'><?= $r['vOut']?></a>
                <?php } ?>
            </td>
            <td class="attempt border-left-dotted">
                 <?php if(isset($Record[1][1]['vOut'])){ 
                     $r=$Record[1][1]; ?>
                     <a href='<?= PageIndex()?>?Records&Discipline=<?= $discipline['Code'] ?>&Country=<?= $Competitor->country_iso2 ?>'><?= $r['vOut'] ?></a>
                <?php } ?>
            </td>
        <?php } ?>
        <td class="border-left-dotted">
        <?php if(isset($Record[0][0]['vOut'])){
            $r=$Record[0][0]; ?>
                <?php if( $r['vCountry'] ){ ?>
                    <?= ImageCountry($r['vCountry'], 30)?>
                <?php } ?>
                <a href='<?= PageIndex()?>?Records&Discipline=<?= $discipline['Code'] ?>'><?= $r['vOut'] ?></a>
        <?php } ?>
        </td> 
        <td class="border-right-dotted">    
        <?php if(isset($Record[0][1]['vOut'])){ 
            $r=$Record[0][1]; ?>
                <?php if($r['vCountry']){ ?>
                    <?= ImageCountry($r['vCountry'], 30)?>
                <?php } ?>
                <a href='<?= PageIndex()?>?Records&Discipline=<?= $discipline['Code'] ?>'><?= $r['vOut'] ?></a>
        <?php } ?>
        </td>  
    <td class="attempt" >
        <?= $discipline['countCompetitors'] ?>
    </td>
    <td class="attempt" >
        <?= $discipline['countCompetitions'] ?>
    </td>
    </tr>
<?php } ?>
    <tr>
        <td>
            <img align="center" title="Picture" height=30px src="<?= PageIndex() ?>Image/Discipline/MosaicBuilding.png">
            <a title="Mosaic Building" href="<?=Pageindex(); ?>Discipline/MosaicBuilding">
                Mosaic Building
            </a>
        </td>
        <td colspan='<?= 2+($Competitor?4:0)   ?>'></td>
        <td/>
        <td  class="attempt" ><?php DataBaseClass::FromTable("Competition","EventPicture=1"); ?>
            <?= count(DataBaseClass::QueryGenerate()); ?>
        </td>
    </tr>
    </table>
    
    