<?php
include_once  'initIncluders.php';
if(isset($_POST['Pixels']) and is_numeric($_POST['Pixels'])){
    if($_POST['Pixels']>=2 and $_POST['Pixels']<=Mosaic::MAX_PIXELS){
        Mosaic::setPixels($_POST['Pixels']);
    }
    Mosaic::changeCube();
}
header('Location:index.php');
exit;