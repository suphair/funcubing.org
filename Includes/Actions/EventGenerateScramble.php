<?php
$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$ID=$request[2];

CheckingRoleDelegateEvent($ID);

DeleteScramble($ID);

DataBaseClass::FromTable('Event',"ID=$ID");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat','Format');
$data=DataBaseClass::QueryGenerate(false);
$Discipline=$data['Discipline_Code'];
$Attemption=$data['Format_Attemption'];

include 'Extras.php';

for($A=1;$A<=$Attemption+$exs;$A++){
    for ($I=1;$I<=$data['Event_Groups'];$I++){
        $scramble=GenerateScramble($Discipline);
        if($scramble){  
            $scramble=DataBaseClass::Escape($scramble);
            DataBaseClass::Query("Insert into Scramble (`Event`,`Scramble`,`Group`,`Attempt`) values ($ID,'$scramble',$I,$A) ");
            $Scrumble_ID=DataBaseClass::getID();
            include 'Scramble/'.$Discipline.'.php';
        }
    }
}

SetMessage();
header('Location: '.PageIndex()."Actions/EventPrintScrambles/?ID=$ID");
exit();  