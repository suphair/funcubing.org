<?php
function regulation_block($discipline){
ob_start();
DataBaseClass::FromTable('Regulation',"Discipline='".$discipline['ID']."'");
$regulations=DataBaseClass::QueryGenerate();
if(sizeof($regulations)){ ?>
    <?php 
    $regulation_name=['RU'=>'Правила','GB'=>'Regulations'];
    foreach($regulations as $regulation){ ?>
        <?php if(isset($regulation_name[$regulation['Regulation_Country']])){ ?>
            <a href="<?= PageIndex()?>/?Regulations&Country=<?= $regulation['Regulation_Country'] ?>#<?= $discipline['Code'] ?>"><?= ImageCountry($regulation['Regulation_Country'],20) ?><?= $regulation_name[$regulation['Regulation_Country']] ?></a>
        <?php } ?>
    <?php } ?>    
<?php } 
$return= ob_get_contents();
ob_end_clean();
return $return;
} ?>