<?php
function scramble_block($ID){
    ob_start(); 
        DataBaseClass::FromTable('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Where_current('Discipline='.$ID);
        DataBaseClass::Where('Event','Competition=129');
        $date=DataBaseClass::QueryGenerate(false);
        DataBaseClass::Join('Event','Scramble');
        $scramble=DataBaseClass::QueryGenerate(false);
        
    if($scramble['Scramble_Timestamp']){ ?>
        &#9642; <a target="_blank"  href="<?= PageIndex()?>Actions/EventPrintScrambles/?ID=<?= $date['Event_ID'] ?>">Example scrambles</a>
    <?php } ?>            
    <?php $file="Image/Scramble/Hard_".md5($date['Event_ID'].GetIni('PASSWORD','admin')).".pdf";
        if (file_exists($file)){ ?>
            &#9642; <a target="_blank"  href="<?= PageIndex().$file ?>">Example scrambles</font></a>
    <?php } ?>
    <?php
    $return = ob_get_contents();
    ob_end_clean();
    return "<nobr>$return</nobr>";
}