<h2>Настройка анкеты</h2>
<?php
DataBaseClass::FromTable("RequestCandidateTemplate");
$templates=DataBaseClass::QueryGenerate();?>
<table>
<?php foreach($templates as $template){ ?>
    <tr>
        <form method="POST" action="<?= PageIndex()."Actions/RequestCandidateTemplateAction" ?>">
            <input type="hidden" name="ID" value="<?= $template['RequestCandidateTemplate_ID'] ?>">
            <td><input id="Language<?= $template['RequestCandidateTemplate_ID'] ?>"  type="input" name="Language" style="width:30px" value="<?= $template['RequestCandidateTemplate_Language'] ?>"></td>    
        <td><input style="width:500px; font-size:16px;" required type="input" value="<?= $template['RequestCandidateTemplate_Name'] ?>"name="Name"</td>
        <td>
                <input id="TypeInput<?= $template['RequestCandidateTemplate_ID'] ?>" <?= $template['RequestCandidateTemplate_Type']=='input'?'checked':'' ?> type="radio" name="Type" value="input">
                <label for="TypeInput<?= $template['RequestCandidateTemplate_ID'] ?>">input</label><br>
                <input id="TypeTextarea<?= $template['RequestCandidateTemplate_ID'] ?>" <?= $template['RequestCandidateTemplate_Type']=='textarea'?'checked':'' ?>  type="radio" name="Type" value="textarea">
                <label for="TypeTextarea<?= $template['RequestCandidateTemplate_ID'] ?>">textarea</label><br>
        </td>
        <td><input type="submit" name="Action" value="Изменить"></td>
        <td><input class="delete" type="submit" name="Action" value="Удалить"></td>
        </form>
    </tr>
<?php  } ?>
   <tr>
        <form method="POST" action="<?= PageIndex()."Actions/RequestCandidateTemplateAction" ?>">
            <td><input id="Language0"  type="input" name="Language" style="width:30px" value="US"></td>    
            <td><input style="width:500px; font-size:16px;" required type="input" value=""name="Name"</td>
        <td>
                <input id="TypeInput0" checked  type="radio" name="Type" value="input">
                <label for="TypeInput0">input</label><br>
                <input id="TypeTextarea0"  type="radio" name="Type" value="textarea">
                <label for="TypeTextarea0">textarea</label><br>
        </td>
        <td><input  type="submit" name="Action" value="Добавить"></td>
        </form>
    </tr> 
    
</table>
<?php 
$langs=array();
DataBaseClass::Query("Select distinct Language from RequestCandidateTemplate");
foreach(DataBaseClass::getRows() as $row){
    if(!in_array($row['Language'],$langs)){
    $langs[]=$row['Language'];
    }
}

foreach($langs as $lang){ 
    DataBaseClass::FromTable("RequestCandidateTemplate","Language='".$lang."'");
    $templates=DataBaseClass::QueryGenerate();
    ?>
<h2>Пример анкеты <?= $lang ?></h2>
    <div class='form'>
        <input type="hidden" name='ID' value="<?= $competitor->id ?>">
        <?php
        foreach($templates as $template){ ?>
            <div class="form_field">
                <?= $template['RequestCandidateTemplate_Name'] ?>
            </div>
        <div class="form_input">
            <?php if($template['RequestCandidateTemplate_Type']=='input'){ ?>
                <input  type="text">
            <?php }else{ ?>
                    <textarea></textarea>
            <?php } ?>
        </div>
        <?php } ?>        
        <div class="form_change">
            <input type="submit" value="Подать заявку">
        </div>
     </div>
<?php } ?>    