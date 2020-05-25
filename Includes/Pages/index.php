<?php
if(isset($_GET['Records'])){ 
    $type='Records';
}elseif(isset($_GET['Competitors'])){
    $type='Competitors';
}elseif(isset($_GET['Events'])){
    $type='Events';
}elseif(isset($_GET['Judges'])){
    $type='Judges';
}elseif(isset($_GET['Regulations'])){
    $type='Regulations';
}elseif(isset($_GET['Logs'])){
    $type='Logs';
}elseif(isset($_GET['Visiters'])){
    $type='Visiters';
}elseif(isset($_GET['Texts'])){
    $type='Texts';
}elseif(isset($_GET['Meetings'])){
    $type='Meetings';
}elseif(isset($_GET['CompetitionGoals'])){
    $type='CompetitionGoals';
}elseif(isset($_GET['Achievements'])){
    $type='Achievements';
}elseif(isset($_GET['FriendsCompetitions'])){
    $type='FriendsCompetitions';
}elseif(isset($_GET['Schedule'])){
    $type='Schedule';
}elseif(isset($_GET['MosaicBuilding'])){
    $type='MosaicBuilding';
}elseif(isset($_GET['WCA_API'])){
    $type='WCA_API';
}elseif(isset($_GET['AllProjects'])){
    $type='AllProjects';
}else{
    $type='AllProjects';
} ?>
   
<?php include 'Navigator.php' ?>


<?php include $type.'.php'; ?>