<head>
    <title>Distribution of competitors by groups</title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>  
<?php

$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$Event=$request[2];

DataBaseClass::Query("Select E.vRound, D.Name Discipline, C.Name Competition,C.WCA Competition_WCA, D.Code Discipline_Code, C.ID CompetitionID, E.ID EventID, E.Groups EventGroups "
        . " from `Event` E join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join  Discipline D on D.ID=DF.Discipline"
        . " join `Competition` C on C.ID=E.Competition Where E.ID='". $Event."'");

if(DataBaseClass::rowsCount()==0){
    exit();
}
    
$data=DataBaseClass::getRow();
$Competition=$data['Competition'];
$Discipline=$data['Discipline'];
if(CheckingRoleDelegateEvent($Event,false)) { ?> 

    <h3>Distribution of competitors by groups</h3>

    <?php 
    
    DataBaseClass::FromTable('Command',"Event='".$Event."'");
    
    $commands=DataBaseClass::QueryGenerate();?>
    
    <table>    
        <tr class="middle">
            <td>
                <?= ImageCompetition($data['Competition_WCA'],40) ?>   
            </td>
            <td>
                <?= $Competition ?>
            </td>
            <td>
                <?= ImageDiscipline($data['Discipline_Code'],40) ?>   
            </td>
            <td>
                <?= $Discipline ?><?= $data['vRound'] ?>
            </td>
        </tr>
    </table>
        <form method="POST" action="<?= PageIndex()?>Actions/EventGroupsSave">
        <input name="ID" type="hidden" value="<?= $Event ?>" />    
        <table class="delegate row">
            <tr>
                <td/>
                <?php 
                $Group=array(-1=>0);
                for($i=0;$i<$data['EventGroups'];$i++){ 
                    $Group[$i]=0; ?>
                    <td><?= Group_Name($i)?></td>
                <?php } ?>
                 <td></td>   
            </tr>
                <?php foreach($commands as $command){ 
                    $Group[$command['Command_Group']]++; ?>
                <tr>
                    <td class='left'>
                        <?php DataBaseClass::FromTable('CommandCompetitor',"Command=".$command['Command_ID']);
                        DataBaseClass::Join_current('Competitor');
                        DataBaseClass::OrderClear('Competitor', 'Name');
                        $names=array();
                        foreach(DataBaseClass::QueryGenerate() as $competitor){  
                            $names[]=$competitor['Competitor_Name']?>
                            <div class="competitor_td">
                                <nobr><?= $competitor['Competitor_Name'] ?> &#9642; <?= $competitor['Competitor_WCAID']; ?></nobr>
                            </div>
                        <?php } ?>
                    </td>
                    <?php for($i=0;$i<$data['EventGroups'];$i++){  ?>
                        <td <?= ($command['Command_Group']==$i)?"style='background:lightblue'":"" ?> >
                            <input type="radio" <?= ($command['Command_Group']==$i)?"checked":"" ?> name="Command_ID[<?= $command['Command_ID'] ?>]" value="<?= $i ?>">
                        </td>
                    <?php } ?>
                        <td <?= ($command['Command_Group']==-1)?"style='background:gray'":"" ?> >
                            <input type="radio" <?= ($command['Command_Group']==-1)?"checked":"" ?> name="Command_ID[<?= $command['Command_ID'] ?>]" value="-1">
                        </td>            
                </tr>
                <?php } ?>
            <tr>
                <td/>
                <?php 
                for($i=0;$i<$data['EventGroups'];$i++){ ?>
                    <td><?= $Group[$i] ?></td>
                <?php } ?>
                 <td><?= $Group[-1] ?></td>   
            </tr>

        </table>
        <center><input style="background-color:lightblue" type="submit" value="Save group distribution"></center>
    </form>
    <form method="POST" action="<?= PageIndex()?>Actions/EventGroupsReset">
        <input name="ID" type="hidden" value="<?= $Event ?>" />   
        <center><input style="background-color:lightpink" type="submit" value="Reset group distribution"></center>
    </form>
<?php }else{ ?>
    Acsess denied
<?php } ?>


<?php exit();