<?php
if(isset($_GET['Meetings'])){
    $type='Meetings';
}elseif(isset($_GET['Goals'])){
    $type='Goals';
}elseif(isset($_GET['Achievements'])){
    $type='Achievements';
}elseif(isset($_GET['FriendsCompetitions'])){
    $type='FriendsCompetitions';
}elseif(isset($_GET['MosaicBuilding'])){
    $type='MosaicBuilding';
}elseif(isset($_GET['AllProjects'])){
    $type='AllProjects';
}else{
    $type='AllProjects';
} ?>
   
<?php include $type.'.php'; ?>