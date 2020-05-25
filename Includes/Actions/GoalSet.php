<?php
    

CheckPostIsset('Goals','Competitor','Records');
CheckPostNotEmpty('Competitor');
CheckPostIsNumeric('Competitor');


$competitorWID=GetCompetitorData()->id;
if(GetCompetitorData()->id!=$_POST['Competitor']){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();  
}
foreach($_POST['Goals'] as $competition=>$events){
    
    $competition=DataBaseClass::Escape($competition);
    DataBaseClass::FromTable('GoalCompetition',"WCA='$competition'");
    DataBaseClass::Where('GoalCompetition', 'DateStart>now()');
    $competitionID=DataBaseClass::QueryGenerate(false)['GoalCompetition_ID'];
    if($competitionID){
        foreach($events as $event=>$goals){
            $event=DataBaseClass::Escape($event);
            DataBaseClass::FromTable('GoalDiscipline',"Code='$event'");        
            $disciplineID=DataBaseClass::QueryGenerate(false)['GoalDiscipline_ID'];
            if($disciplineID){
                foreach($goals as $format=>$goal){
                    if(in_array($format,['single','average'])){
                        DataBaseClass::FromTable("Goal");
                        DataBaseClass::Where("Competition='$competition'");
                        DataBaseClass::Where("Discipline='$event'");
                        DataBaseClass::Where("Competitor=$competitorWID");
                        DataBaseClass::Where("Format='$format'");
                        if(isset($_POST['Records'][$competition][$event][$format])){
                            $record=$_POST['Records'][$competition][$event][$format];
                        }else{
                            $record='';
                        }
                        
                        if($event=='333fm' and $format=='average' and strlen($goal)==3){
                            $goal=substr($goal,1,2).".00";
                         }
                        $record_int=GoalResultToInt($record);
                        $goal_int=GoalResultToInt($goal);
                        $progress=(!$goal_int or !$record_int)?'':((round(($record_int-$goal_int)/$record_int*100,1)).'%');
                        $TimeFixed= DataBaseClass::QueryGenerate(false)['Goal_TimeFixed'];
                        if(!$TimeFixed){
                            if($goal!=''){
                                DataBaseClass::Query("Insert into Goal"
                                    . " (Competition,Discipline,Competitor,Format,Goal,TimeFixed,Record,Progress) values"
                                    . " ('$competition','$event',$competitorWID,'$format','$goal',DATE_ADD(NOW(), INTERVAL 2 HOUR),'$record','$progress') ");
                            }
                        }else{
                            if(strtotime($TimeFixed)>time("now")){
                                if($goal==''){
                                    DataBaseClass::Query("Delete from Goal where Competition='$competition' and Discipline='$event'"
                                            . " and Competitor=$competitorWID and Format='$format'");
                                }else{
                                    DataBaseClass::Query("Update Goal Set Goal='$goal',TimeFixed= DATE_ADD(NOW(), INTERVAL 2 HOUR),Record='$record',Progress='$progress' "
                                    . " where Competition='$competition' and Discipline='$event'"
                                            . " and Competitor=$competitorWID and Format='$format' ");
                                }
                            }
                        }
                    }
                }
            }
        }    
    }
    
    
    GoalImageCreate($competition,$competitorWID);  
}

SetMessage();

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  
