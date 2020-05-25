<?php
$print=false;
$Competitor= GetCompetitorData();
if($Competitor and isset($_GET['Secret']) and isset($_GET['Discipline']) and is_numeric($_GET['Discipline'])){
    $Secret= DataBaseClass::Escape($_GET['Secret']);
    $Discipline= $_GET['Discipline'];
    
    
    DataBaseClass::FromTable("Meeting");
    DataBaseClass::Where("Secret='$Secret'");
    DataBaseClass::Join_current("MeetingDiscipline");
    DataBaseClass::Where("MD.ID=$Discipline");
    DataBaseClass::Join_current("MeetingFormat");
    DataBaseClass::Join("MeetingDiscipline","MeetingDisciplineList");
    $meeting=DataBaseClass::QueryGenerate(false);
    
    if(is_array($meeting)){
           $print=true; 
    }
} 
if(!$print){
    echo 'Not found';
    exit();
}
        
    DataBaseClass::Join("MeetingDiscipline","MeetingCompetitorDiscipline");
    DataBaseClass::Where("MCD.Place is not null");
    DataBaseClass::Join_current("MeetingCompetitor");
    DataBaseClass::OrderClear("MeetingCompetitorDiscipline","Place");

    $results=DataBaseClass::QueryGenerate();
    
    foreach($results as $result){ ?>
        <?= $result['MeetingCompetitor_Name']?>
        <?php for($i=1;$i<=$meeting['MeetingFormat_Attempts'];$i++){ ?>
        <?= $result['MeetingCompetitorDiscipline_Attempt'.$i] ?>
    <?php }?>
    <br>
    <?php } 
exit();
