<?php
function scorecard_block($ID){
    ob_start(); 
        DataBaseClass::FromTable('Event');
        DataBaseClass::Join_current('DisciplineFormat');
        DataBaseClass::Where_current('Discipline='.$ID);
        DataBaseClass::Where('Event','Competition=129');
        $date=DataBaseClass::QueryGenerate(false); ?>
        &#9642; <a target="_blank"  href="<?= PageIndex()?>Actions/EventPrintScoreCards/?ID=<?= $date['Event_ID'] ?>">Example scorecards</a>
    <?php
    $return = ob_get_contents();
    ob_end_clean();
    return "<nobr>$return</nobr>";
}