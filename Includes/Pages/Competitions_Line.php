<?php if(CheckAdmin()){
        $competitions=DataBaseClass::SelectTableRows('Competition','Name<>""');    
    }else{
        $competitions=DataBaseClass::SelectTableRows('Competition','Status=1 and Name<>""');
    }
    ?>
<div class="line">
    <?php foreach($competitions as $competition_row){?>
        <a class="<?= $competition_row['Competition_ID']==$competition['Competition_ID']?"line_select":""?>"   title="<?= $competition_row['Competition_Name'] ?> &bull; <?= $competition_row['Competition_StartDate'] ?> / <?= $competition_row['Competition_EndDate'] ?>"  href="<?= LinkCompetition($competition_row['Competition_WCA']) ?>"><?= ImageCompetition($competition_row['Competition_WCA'],50) ?></a>
    <?php } ?>
</div>