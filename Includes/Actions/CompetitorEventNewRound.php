<?php
CheckPostIsset('ID','Secret');
CheckPostNotEmpty('ID','Secret');
CheckPostIsNumeric('ID');

$ID=$_POST['ID'];
$Secret=$_POST['Secret'];

CheckingScoreTakerEvent($ID,$Secret);

DataBaseClass::Query("Select E.*,DF.Discipline"
        . "  from Event E "
        . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join Discipline D on D.ID=DF.Discipline"
        . " where E.ID=$ID");
$event=DataBaseClass::getRow();

DataBaseClass::Query("select Com.ID Command, Com.vName,case when Com.Place>0 then Com.Place else '' end Place, Com.ID, Com.Decline,Com.CardID "
        . " from `Command` Com"
        . " where Com.Event='".$event['ID']."' "
        . " order by case when Com.Place>0 then Com.Place else 999 end, Com.Decline, Com.CardID");
$commands=DataBaseClass::getRows(); 

DataBaseClass::Query("Select E.ID Event, E.vRound,E.Round, E.Competitors, count(distinct Com.ID) Commands "
        . " from Event E left outer join Command Com on Com.Event=E.ID"
        . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join Discipline D on D.ID=DF.Discipline"
        . " where Round=".($event['Round']+1)." and Competition=".$event['Competition']." and DF.Discipline=".$event['Discipline']." group by E.ID");
if(DataBaseClass::rowsCount()>0){
    $Next=DataBaseClass::getRow();
    if(!$Next['Commands']){
        $competitorsWinner=min($Next['Competitors'],floor(sizeof($commands)*0.75));
        for($i=0; $i<$competitorsWinner; $i++){
            $Command=0;
            DataBaseClass::Query("Select Competitor from CommandCompetitor where Command=".$commands[$i]['Command']);
            foreach(DataBaseClass::getRows() as $competitor){
                $Command=CommandAdd($Command,$Next['Event'],$competitor['Competitor']);   
            }
        }        
    }    
}

SetMessage(""); 
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  