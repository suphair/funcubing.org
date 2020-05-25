<?php
$isAdmin=CheckAdmin();
if(isset($_GET['Country'])){
    $country_filter=$_GET['Country'];    
}else{
    $country_filter='GB';
}

$countries=['GB','RU'];
$languages=['English','Russian'];
foreach($countries as $c=>$country){ ?>
        <span class="badge_navigation <?= $country_filter==$country?'badge_navigation_select':''?>">        
                <?= ImageCountry($country,20) ?>
                <a href="<?= PageIndex() ?>?Regulations&Country=<?= $country ?>" >
                    <?= $languages[$c]  ?>
                </a>
        </span> 
<?php }


if($Regulation=GetBlockText('Regulation',$country_filter)){ ?>
    <br>
    <div class="form">
        <?= Echo_format($Regulation); ?>
    </div>
<?php  }
        
DataBaseClass::Query("Select D.ID, D.Name, D.Code,R.Country,R.Text from Discipline D  "
        . " left outer join Regulation R on R.Discipline=D.ID and R.Country='$country_filter' where D.Status='Active'");
$disciplines=DataBaseClass::getRows(); ?>
<div class="regulation line">
    <?php foreach($disciplines as $discipline_row){ ?>
        <?php if($discipline_row['Text']){ ?>
            <a href="#<?= $discipline_row['Code'] ?>"><?= ImageDiscipline($discipline_row['Code'],35) ?></a> 
        <?php }else{ ?>
             <span class="disabled"><?= ImageDiscipline($discipline_row['Code'],35) ?></span> 
        <?php } ?>
        
    <?php } ?>
</div>
<hr class="hr_round">
<?php foreach($disciplines as $discipline_row){ ?>
<a name="<?= $discipline_row['Code'] ?>"></a>
<div class="form">
   <h2>
       <?= ImageCountry($country_filter,30) ?>
        <?= ImageDiscipline($discipline_row['Code'],40) ?>
        <?= $discipline_row['Name'] ?> 
       <?php foreach($countries as $country){ ?>   
            <?php if($country_filter!=$country){ 
                DataBaseClass::Query("Select * from Regulation R where R.Country='$country' and R.Discipline=".$discipline_row['ID']);
                if(DataBaseClass::getRow()['ID']){ ?>
                    <span style="padding:0px; height: 20px;" class="badge_navigation <?= $country_filter==$country?'badge_navigation_select':''?>">        
                         <a href="<?= PageIndex() ?>?Regulations&Country=<?= $country ?>#<?= $discipline_row['Code'] ?>" >
                                 <?= ImageCountry($country,20) ?>
                         </a>
                     </span> 
               <?php } ?>
            <?php } ?>
       <?php } ?>
   </h2>
        <?= scramble_block($discipline_row['ID']);?>
        <?= scorecard_block($discipline_row['ID']);?>
    <div id="Text_<?= $discipline_row['ID'] ?>"><?= Echo_format($discipline_row['Text']);?>
    <?php if($isAdmin){ ?>
        <br><a href="#" class="message"
               onclick=" 
                   $('#TextArea_<?= $discipline_row['ID'] ?>').show();
                   $('#Text_<?= $discipline_row['ID'] ?>').hide();
                   return(false);">Edit</a></div>
        <div hidden id="TextArea_<?= $discipline_row['ID'] ?>">
            <form method="POST" action="<?= PageIndex()."Actions/RegulationSave" ?>">
                <textarea name="Text" style="width: 700px;height: 200px;font-size: 16px;"><?= $discipline_row['Text'] ?></textarea>
                <br>
                <input type="submit" name="Save" value="Save">
                <input type="hidden" name="Country" value="<?= $country_filter ?>">
                <input type="hidden" name="Discipline" value="<?= $discipline_row['ID'] ?>">
                <a href="#" class="error"
                   onclick=" 
                       $('#TextArea_<?= $discipline_row['ID'] ?>').hide();
                       $('#Text_<?= $discipline_row['ID'] ?>').show();
                       return(false);">Cancel</a>
            </form>
    <?php } ?>
    </div>
</div>
<br>
<?php 
} ?>
