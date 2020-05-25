<?php if(CheckAdmin()){ ?>
    <?php include 'Delegates.php'; ?>
<?php } ?>
<?php 
$delegate=DataBaseClass::SelectTableRow("Delegate","WCA_ID='".RequestClass::getParam1()."'");
$Delegate=GetDelegateData();
?>

<h1><?= $delegate['Delegate_Name'] ?> 
    <?php if(CheckAdmin()){ ?>
        &#9642; <a href="<?= LinkDelegate($delegate['Delegate_WCA_ID']) ?>/config">Setting</a> 
<?php } ?>
<h2>       
<?php if($delegate['Delegate_Status']!='Active'){ ?>
    Archive
<?php }elseif($delegate['Delegate_Admin']){ ?>
    Senior Judge    
<?php }elseif($delegate['Delegate_Candidate']){ ?>
    Junior Judge
<?php } else{ ?>
    Middle Judge 
<?php } ?>
  &#9642;  <a href="https://www.worldcubeassociation.org/persons/<?= $delegate['Delegate_WCA_ID'] ?>"><?= $delegate['Delegate_WCA_ID'] ?></a>
  <?php if($Delegate and !$Delegate['Delegate_Candidate']){ ?>
    &#9642; <a href="<?= LinkCompetition('Add')?>"><?= svg_blue(20,"Add competition")?>Add competition</a>
  <?php } ?>
</h2>
<?php if($delegate['Delegate_Contact']){ ?>
    <div class="form"><?= Echo_format($delegate['Delegate_Contact']) ?></div><br>
<?php } ?>
<?php
    DataBaseClass::FromTable('Delegate',"ID='".$delegate['Delegate_ID']."'");
    DataBaseClass::Join_current('CompetitionDelegate');
    DataBaseClass::Join_current('Competition');
    if(!$Delegate){
        DataBaseClass::Where_current('Status=1');
    }
    DataBaseClass::OrderClear('Competition', 'StartDate desc');
    $competitions=DataBaseClass::QueryGenerate();
    //usort($competitions,'Competition_Sort');
    
    ?><br>
<?php if(sizeof($competitions)){ ?>
    <table class="Competitions">
        <tr class="tr_title">
                <td>Name</td>
                <td>Date</td>
                <td>Country, City</td>
                <td>Events</td>
            </tr>
            <?php foreach($competitions as $competition){ ?>
            <tr>
                <td>
                    <a class="<?= $competition['Competition_Status']!='1'?"archive":""; ?> "  href="<?= LinkCompetition($competition['Competition_WCA']) ?>">
                                <?= $competition['Competition_Name'] ?>
                    </a>
                </td>
                <td>
                    <?= date_range($competition['Competition_StartDate'],$competition['Competition_EndDate']); ?>
                </td>   
                <td>
                    <?= ImageCountry($competition['Competition_Country'], 30)?>
                    <?= CountryName($competition['Competition_Country']) ?>, <?= CountryName($competition['Competition_City']) ?>
                <td>
                <?php DataBaseClass::FromTable("Event","Competition=".$competition['Competition_ID']);
                      DataBaseClass::Join_current("DisciplineFormat");
                      DataBaseClass::Join_current("Discipline");
                      DataBaseClass::OrderClear("Discipline", "Name");
                      DataBaseClass::Select("distinct D.*");

                      $j=0; 
                      foreach(DataBaseClass::QueryGenerate() as $discipline){ ?>
                            <a href="<?= LinkDiscipline($discipline['Code']) ?>"><?= ImageDiscipline($discipline['Code'],30,$discipline['Name']);?></a>
                            <?php $j++;
                            if($j==8){
                                $j=0;
                            echo "<br>";
                        }
                      } ?>
                </td>
            </tr>    
            <?php } ?>
        
    </table>
<?php } ?>




    