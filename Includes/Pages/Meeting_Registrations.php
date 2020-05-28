<style>

    .MeetingRegistationDiscipline img{
        border: 1px solid white;   
    }

    .MeetingRegistationDiscipline:hover{
        cursor: pointer;
        color:green;
    }

    .MeetingRegistationDiscipline:hover  img{
        border: 1px solid green;
    }

</style>
<br><br><br>
<div class="shadow2">
<h2>
        <i class="fas fa-users-cog"></i>
        Registrations
              </h2>

<table class="table_new">
    <thead>
        <tr>
            <td width=60px></td>
            <td>#</td>
            <td>Competitor</td>
            <?php
            DataBaseClass::FromTable("MeetingDiscipline", "Meeting=" . $meeting['Meeting_ID']);
            DataBaseClass::Where_current("Round=1");
            DataBaseClass::Join_current("MeetingDisciplineList");
            DataBaseClass::OrderClear("MeetingDisciplineList", "ID");
            $disciplines = DataBaseClass::QueryGenerate();
            $disciplines_code = array();
            foreach ($disciplines as $discipline) {
                $disciplines_code[$discipline['MeetingDisciplineList_Name']] = $discipline['MeetingDisciplineList_Code'];
            }
            DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
            DataBaseClass::Join_current("MeetingCompetitor");
            $registrations = array();
            foreach (DataBaseClass::QueryGenerate() as $r) {
                if (!isset($registrations[$r['MeetingCompetitor_ID']]['Delete'])) {
                    $registrations[$r['MeetingCompetitor_ID']]['Delete'] = 0;
                }
                $registrations[$r['MeetingCompetitor_ID']][$r['MeetingDisciplineList_ID']] = array('Place' => $r['MeetingCompetitorDiscipline_Place']);
                if ($r['MeetingCompetitorDiscipline_Place']) {
                    $registrations[$r['MeetingCompetitor_ID']]['Delete'] = 1;
                }
            }
            foreach ($disciplines as $discipline) {
                ?>
                <td>
                    <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>    
                </td>
            <?php } ?>    
            <td width="20px" >ALL</td>
        </tr>
    </thead>
    <tbody>
        <?php
        DataBaseClass::FromTable("MeetingCompetitor", "Meeting=" . $meeting['Meeting_ID']);
        DataBaseClass::OrderClear("MeetingCompetitor", "Name");
        $registration_rows = DataBaseClass::QueryGenerate();
        $disciline_count = array();
        foreach ($registration_rows as $r => $row) {
            ?>
            <tr id="CompetitorShow_<?= $row['MeetingCompetitor_ID'] ?>" class="CompetitorShow"
                onclick="
                        $('.CompetitorEdit').hide();
                        $('.CompetitorShow').show();
                        $('#CompetitorEdit_<?= $row['MeetingCompetitor_ID'] ?>').show();
                        $('#CompetitorShow_<?= $row['MeetingCompetitor_ID'] ?>').hide();"
                style="border-bottom:1px blue dotted; padding-bottom:0px;">
                <td style="width:30px"/>
                <td><?= $r + 1 ?></td>
                <td style="width:250px">
                    <?= $row['MeetingCompetitor_Name'] ?>
                </td>
                <?php
                foreach ($disciplines as $discipline) {
                    if (!isset($disciline_count[$discipline['MeetingDisciplineList_ID']])) {
                        $disciline_count[$discipline['MeetingDisciplineList_ID']] = 0;
                    }
                    ?>
                    <td align="center">
                        <?php
                        if (isset($registrations[$row['MeetingCompetitor_ID']][$discipline['MeetingDisciplineList_ID']])) {
                            $disciline_count[$discipline['MeetingDisciplineList_ID']] ++;
                            ?>
                            <?php if ($registrations[$row['MeetingCompetitor_ID']][$discipline['MeetingDisciplineList_ID']]['Place']) { ?>
                                +
                            <?php } else { ?>
                                &bull;
                            <?php } ?>
                    <?php } ?>
                    </td>    
    <?php } ?>
            </tr>
            <tr hidden class="CompetitorEdit" id="CompetitorEdit_<?= $row['MeetingCompetitor_ID'] ?>">
                <td style="vertical-align: middle;" >
    <?php if (!isset($registrations[$row['MeetingCompetitor_ID']]['Delete']) or ! $registrations[$row['MeetingCompetitor_ID']]['Delete']) { ?>
                        <form action="<?= PageIndex() . "Actions/MeetingCompetitorDelete" ?>" method="post"
                              onsubmit="return confirm('Delete competitor [<?= $row['MeetingCompetitor_Name'] ?>]?')">   
                            <input style="color:red; background-color:white; margin:0px; padding:0px 3px; border:1px solid red;" type="submit" value="Delete">
                            <input type="hidden" name="Competitor" value="<?= $row['MeetingCompetitor_ID'] ?>">
                            <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
                        </form>
    <?php } ?>
                </td>
                <td><?= $r + 1 ?></td>
        <form action="<?= PageIndex() . "Actions/MeetingCompetitorChange" ?>" method="post">
            <td><nobr>   
                <input style="font-size:14px" name="Name" value="<?= $row['MeetingCompetitor_Name'] ?>">
                <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
                <input type="hidden" name="Competitor" value="<?= $row['MeetingCompetitor_ID'] ?>">
                <input style="color:green; background-color:white; margin:0px; padding:0px 3px; border:1px solid green;" type="submit" value="Save">
            </nobr></td>
            <?php
            $checkedALL = true;
            foreach ($disciplines as $discipline) {
                ?>
                <td align="center" style="vertical-align: middle;" >
                    <input type="hidden" name="Registration[<?= $discipline['MeetingDiscipline_ID'] ?>]" value="off">
                    <?php if (isset($registrations[$row['MeetingCompetitor_ID']][$discipline['MeetingDisciplineList_ID']]) and $registrations[$row['MeetingCompetitor_ID']][$discipline['MeetingDisciplineList_ID']]['Place']) { ?>
                        <input type="checkbox" name="Registration[<?= $discipline['MeetingDiscipline_ID'] ?>]" disabled checked>
                        <?php
                    } else {
                        if (!isset($registrations[$row['MeetingCompetitor_ID']][$discipline['MeetingDisciplineList_ID']])) {
                            $checkedALL = false;
                        }
                        ?>
                        <input class="checkboxFor<?= $row['MeetingCompetitor_ID'] ?>" type="checkbox" name="Registration[<?= $discipline['MeetingDiscipline_ID'] ?>]" <?= isset($registrations[$row['MeetingCompetitor_ID']][$discipline['MeetingDisciplineList_ID']]) ? 'checked' : '' ?>>
                <?php } ?>
                </td>    
    <?php } ?>
            <td align="center" style="vertical-align: middle;">
                <input type="checkbox" <?= $checkedALL ? 'checked' : '' ?> 
                       onclick="
                               if ($(this).is(':checked')) {
                                   $('.checkboxFor<?= $row['MeetingCompetitor_ID'] ?>').attr('checked', true);
                               } else {
                                   $('.checkboxFor<?= $row['MeetingCompetitor_ID'] ?>').attr('checked', false);
                               }">
            </td>
        </form>
    </tr>
<?php } ?>
</tbody>
<tfoot>
    <tr >
        <td/><td/><td/>
<?php foreach ($disciplines as $discipline) { ?>
            <td>
                <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
            </td>
<?php } ?>    
    </tr> 
    <tr>
        <td/><td/><td>Total</td>
        <?php foreach ($disciplines as $discipline) { ?>
            <td align="center"><?= isset($disciline_count[$discipline['MeetingDisciplineList_ID']]) ? $disciline_count[$discipline['MeetingDisciplineList_ID']] : '0' ?></td>
<?php } ?>
    </tr>
</tfoot>
</table>
<?php if (sizeof($registration_rows)) { ?>    
    Click on row for edit competitor/registration
<?php } ?>
<?php if (sizeof($registration_rows) and ( $meeting['Meeting_Competitor'] == $Competitor->id or CheckMeetingGrand())) { ?>    
    <form action="<?= PageIndex() . "Actions/MeetingCompetitorsDelete" ?>" method="post"
          onsubmit="return confirm('Delete all competitors?')">   
        <input style="color:red; background-color:white;  border:1px solid red;" 
               type="submit" value="Delete all competitors">
        <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
    </form>
<?php } ?>
</div>
<div class="shadow2">
    <table><tr class="no_border"><td>
                <form method="POST" action="<?= PageIndex() . "Actions/MeetingCompetitorsAdd" ?>">
                    <textarea id="Competitors_" name="Competitors" style="width: 400px; height: 300px; font-size:20px;"></textarea>
                    <br>
                    <input type="submit" value="Add competitors and registration">
                    <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
                </form>
            </td>
            <td><h3><span style="border-bottom:1px dotted rgb(0,182,67); cursor: help; color:rgb(0,182,67);" 
                          data-tooltip="
                          <font style='color:rgb(0,182,67)'>▪</font> Enter competitors separated by a <b>newline</b> or <b>comma</b><font style='color:rgb(0,182,67)'>:</font><br>
                          [Competitor Alfa<br>Competitor Beta] <font style='color:rgb(0,182,67)'>&#8658;</font> <b>1)</b> Competitor Alfa <b>2)</b> Competitor Beta<br>
                          [Competitor Delta, Competitor Gamma] <font style='color:rgb(0,182,67)'>&#8658;</font> <b>1)</b> Competitor Delta <b>2)</b> Competitor Gamma<br>
                          <font style='color:rgb(0,182,67)'>▪</font> You can enter the name in any case the first letters will become large<br>
                          [comPETitor epSiLon] <font style='color:rgb(0,182,67)'>&#8658;</font> Competitor Epsilon<br>
                          <font style='color:rgb(0,182,67)'>▪</font> When you create a competitor, you can register it for the discipline by adding a tag<br>
                          (also you can click on the discipline on the right)<br>
                          [Competitor Dseta 2 3] <font style='color:rgb(0,182,67)'>&#8658;</font> Competitor Dseta +2x2x2 Cube +3x3x3 Cube<br>
                          <br>
                          <font style='color:rgb(0,182,67)'>▪</font> You can register a competitor in the disciplines later
                          <br>
                          <font style='color:rgb(0,182,67)'>▪</font> You can add competitors in several parts
                          ">Instruction</span></h3><br>
                    <?php foreach ($disciplines_code as $name => $code)
                        if ($code) {
                            ?>
                        <p class="MeetingRegistationDiscipline" onclick="
                                var el = $('#Competitors_');
                                var s = el[0].selectionStart;
                                var val = $('#Competitors_').val();
                                var val_s = val.substring(0, s);
                                var val_e = val.substring(s);
                                var code = ' <?= $code ?> ';
                                el.val(val_s + code + val_e);
                                el.focus();
                                el[0].setSelectionRange(s + code.length, s + code.length);
                           ">
                            <?= $code ?>
                            <img width="20px" src="<?= PageIndex() . "Image/MeetingImage/" . $name ?>.png">
                        <?= $name ?>
                        </p> 
    <?php } ?>
            </td>
        </tr>
    </table>
</div>
<?php
DataBaseClass::FromTable("Meeting", "Competitor=" . $meeting['Meeting_Competitor']);
DataBaseClass::Join_current("MeetingCompetitor");
DataBaseClass::OrderClear("MeetingCompetitor", "Name");
$Competitors = array();
$CompetitorsName = array();
foreach (DataBaseClass::QueryGenerate() as $row) {
    $Competitors[$row['MeetingCompetitor_Name']][] = $row['Meeting_ID'];
    $CompetitorsID[$row['MeetingCompetitor_Name']] = $row['MeetingCompetitor_ID'];
}
$selectComp = false;
foreach ($Competitors as $competitorName => $competitions) {
    if (!in_array($meeting['Meeting_ID'], $competitions)) {
        $selectComp = true;
    }
}
if ($selectComp) {
    ?>
    <form action="<?= PageIndex() . "Actions/MeetingCompetitorsAddSelect" ?>" method="post">
        <h3>Competitors of your competitions</h3>
        <table class="table_new">
            <thead>
                <tr>
                    <td>Competitor (<?= sizeof($Competitors) ?>)</td>
                    <td>Add</td>
                </tr>
            </thead>
            <tbody>
                        <?php foreach ($Competitors as $competitorName => $competitions) { ?>
                    <tr >
                        <td>
                            <?= $competitorName ?>
                        </td>
                        <td align='center'>
                            <?php if (in_array($meeting['Meeting_ID'], $competitions)) { ?>
                                +
                            <?php } else { ?>
                                <input name="Competitors[<?= $CompetitorsID[$competitorName] ?>]" type='checkbox'>
                    <?php } ?>    
                        </td>
                    </tr>
    <?php } ?>
            </tbody>
        </table>
        <input type="hidden" name="Secret" value="<?= RequestClass::getParam1() ?>">
        <input type="submit" value="Add selected competitors">
    </form>
    </div>
<?php } ?>
</div>