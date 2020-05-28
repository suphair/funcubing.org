<?php
if (isset($_POST['CompetitorRemove']) and isset($_POST['CompetitorID']) and is_numeric($_POST['CompetitorID'])) {
    $CompetitorID = $_POST['CompetitorID'];
    $meetingID = $meeting['Meeting_ID'];
    DataBaseClass::Query("Select* from MeetingCompetitorDiscipline MCD "
            . " join MeetingDiscipline MD on MD.ID=MCD.MeetingDiscipline "
            . "where MCD.ID=$CompetitorID and MD.Meeting=$meetingID and Place is null");

    $row = DataBaseClass::getRow();
    if (is_array($row)) {
        DataBaseClass::Query("Delete from MeetingCompetitorDiscipline where Place is null and ID=$CompetitorID");
    }
}

if (isset($_POST['AddRegistrations']) and isset($_POST['Competitor']) and isset($_POST['Discipline']) and is_numeric($_POST['Discipline'])) {
    $meetingID = $meeting['Meeting_ID'];
    $discipline = $_POST['Discipline'];
    DataBaseClass::Query("select MC.ID from `MeetingCompetitor` MC
    left outer join `MeetingCompetitorDiscipline` MCD on MCD.`MeetingDiscipline`=$discipline and MCD.`MeetingCompetitor`=MC.ID
    where MC.Meeting=$meetingID and MCD.ID is null");
    foreach (DataBaseClass::getRows() as $row) {
        if (isset($_POST['Competitor'][$row['ID']])) {
            DataBaseClass::Query("Insert into MeetingCompetitorDiscipline (MeetingDiscipline,MeetingCompetitor) values($discipline," . $row['ID'] . ") ");
        }
    }
}

if (isset($_POST['SaveAttempt'])) {
    if (
            isset($_POST['CompetitorID'])
            and isset($_POST['InputCompetitorName'])
            and isset($_POST['InputAttemptS'])
            and isset($_POST['InputAttempt'])
            and is_numeric($_POST['CompetitorID'])
    ) {
        DataBaseClass::FromTable("MeetingCompetitorDiscipline", "ID=" . $_POST['CompetitorID']);
        $row = DataBaseClass::QueryGenerate(false);

        if (is_array($row)) {
            $CompetitorID = $_POST['CompetitorID'];
            $InputCompetitorName = DataBaseClass::Escape($_POST['InputCompetitorName']);
            $InputAttemptS = DataBaseClass::Escape($_POST['InputAttemptS']);
            $InputAttempt = array();
            $mili = 0;
            foreach ($_POST['InputAttempt'] as $key => $value) {
                if (is_numeric($key) and $key <= 5) {
                    if ($InputAttemptS == '') {
                        $InputAttempt['Attempt' . $key] = '';
                    } else {
                        $value = str_replace(array("00:0", "00:", "0:0", "0:"), "", $value);
                        $InputAttempt['Attempt' . $key] = DataBaseClass::Escape($value);
                    }
                } elseif (in_array($key, array('Best', 'Average', 'Mean'))) {
                    if ($InputAttemptS == '') {
                        $InputAttempt[$key] = '';
                    } else {
                        $value = DataBaseClass::Escape($value);
                        if ($value == 'DNF' or $value == '-cutoff') {
                            $mili = $mili * 1000000 + 999999;
                        } else {
                            $value_t = substr("0000000" . $value, -8, 8);
                            $minute = substr($value_t, 0, 2);
                            $second = substr($value_t, 3, 2);
                            $milisecond = substr($value_t, 6, 2);
                            $value = str_replace(array("00:0", "00:", "0:0", "0:"), "", $value);
                            $mili = $mili * 1000000 + $minute * 100 * 60 + $second * 100 + $milisecond;
                        }
                        $InputAttempt[$key] = DataBaseClass::Escape($value);
                    }
                }

                $InputAttempt['MilisecondsOrder'] = $mili;
            }
            if ($InputAttemptS != '') {
                $sql = "Update MeetingCompetitorDiscipline set AttemptS='$InputAttemptS' ";
            } else {
                $sql = "Update MeetingCompetitorDiscipline set AttemptS=null ";
            }
            foreach ($InputAttempt as $key => $value) {
                $sql .= ",$key='$value'";
            }
            $sql .= " where ID=$CompetitorID";
            DataBaseClass::Query($sql);

            if ($InputCompetitorName) {
                DataBaseClass::Query("Update MeetingCompetitor set Name='$InputCompetitorName'where ID=" . $row['MeetingCompetitorDiscipline_MeetingCompetitor']);
            }
            MeetingUpdatePlace($row['MeetingCompetitorDiscipline_MeetingDiscipline']);
        }
    }
}


DataBaseClass::FromTable("MeetingDiscipline", "Meeting=" . $meeting['Meeting_ID']);
DataBaseClass::Join_current("MeetingDisciplineList");
DataBaseClass::OrderClear("MeetingDisciplineList", "ID");
DataBaseClass::Order("MeetingDiscipline", "Round");
$disciplines = DataBaseClass::QueryGenerate();
$discipline_rounds = array();
foreach ($disciplines as $discipline) {
    $discipline_rounds[$discipline['MeetingDisciplineList_ID']] = $discipline['MeetingDiscipline_Round'];
    if (isset($_GET['Discipline']) and $_GET['Discipline'] == $discipline['MeetingDiscipline_ID']) {
        $current_discipline = $discipline['MeetingDiscipline_ID'];
        $Current_discipline = $discipline;
    }
}
?>
<br><br><br>
<h1>
    <a href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>"><i class="fas fa-table"></i></a>    
    <?php foreach ($disciplines as $discipline) { ?>
        <a 
            class="<?= $current_discipline == $discipline['MeetingDiscipline_ID'] ? 'config' : '' ?>"
            title="<?= $discipline['MeetingDisciplineList_Name'] ?> / round <?= $discipline['MeetingDiscipline_Round'] ?>"
            href="<?= PageIndex() . "Meetings/" . RequestClass::getParam1() ?>/?Discipline=<?= $discipline['MeetingDiscipline_ID'] ?>"><i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i></a>
        <?php } ?>
</h1>
<?php $disciplines_special = [] ?>
<?php foreach ($disciplines as $discipline) { ?>
    <?php
    if ($discipline['MeetingDisciplineList_ID'] >= 200
            and ! in_array($discipline['MeetingDisciplineList_ID'], $disciplines_special)) {
        $disciplines_special[] = $discipline['MeetingDisciplineList_ID'];
        ?>
        <i class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
        <?= $discipline['MeetingDiscipline_Name'] ?>
    <?php } ?>
<?php } ?>  
<div class="shadow2" >
    <?php if (isset($Current_discipline)) { ?>       
        <h2>
            <i class="<?= $Current_discipline['MeetingDisciplineList_Image'] ?>"></i>
            <?= $Current_discipline['MeetingDiscipline_Name'] ?>
            / round <?= $Current_discipline['MeetingDiscipline_Round'] ?>
        </h2>        
        <p>
            <span class="data_tooltip" 
                  data-tooltip="
                  <font style='var(--green)'>▪</font> Click on competitor row in table to enter attempts<br>
                  <font style='var(--green)'>▪</font> Entered attempt without delimiters<font style='color:var(--green)'>:</font> [11122] <font style='color:var(--green)'>&#8658;</font> 1:11.22<br>
                  <font style='var(--green)'>▪</font> Attempts are separated by spaces<font style='color:var(--green)'>:</font> [1322 2243] <font style='color:var(--green)'>&#8658;</font> <b>1)</b> 0:13.22 <b>2)</b> 0:22.43<br>
                  <font style='var(--green)'>▪</font> DNF is entered as <b>-</b> or <b>DNF</b> or <b>dnf</b><font style='color:var(--green)'>:</font> [1134 -] <font style='color:var(--green)'>&#8658;</font> <b>1)</b> 0.11:34 <b>2)</b> DNF<br>
                  <font style='var(--green)'>▪</font> DNS is entered as <b>0</b> or <b>DNS</b> or <b>dns</b><font style='color:var(--green)'>:</font> [232 0 454] <font style='color:var(--green)'>&#8658;</font> <b>1)</b> 0.02:32 <b>2)</b> DNS <b>3)</b> 0:04.54<br>
                  <font style='var(--green)'>▪</font> Press enter to save attempts">
                <i class="fas fa-info-circle"></i> 
                Instruction
            </span>
        </p>
        <?php if ($Current_discipline['MeetingDiscipline_Comment']) { ?>
            <p>
                <i class="far fa-comment"></i>
                <?= $Current_discipline['MeetingDiscipline_Comment'] ?>
            </p>
        <?php } ?>
        <?php
        if (isset($current_discipline)) {
            DataBaseClass::FromTable("MeetingDiscipline", "ID=$current_discipline");
            DataBaseClass::Join("MeetingDiscipline", "MeetingFormat");
            $data = DataBaseClass::QueryGenerate(false);
            $Attemps = $data['MeetingFormat_Attempts'];
            $Format = $data['MeetingFormat_Format'];
            $isAmount = $data['MeetingDiscipline_Amount'];

            DataBaseClass::Join("MeetingDiscipline", "MeetingDisciplineList");
            if (!$isAmount) {
                $isAmount = DataBaseClass::QueryGenerate(false)['MeetingDisciplineList_Amount'];
            }



            DataBaseClass::Join("MeetingDiscipline", "MeetingCompetitorDiscipline");
            DataBaseClass::Join_current("MeetingCompetitor");

            DataBaseClass::OrderSpecial("coalesce(MCD.Place,999)");
            DataBaseClass::Order("MeetingCompetitor", "Name");
            if ($Format == 'Average') {
                $Formats = array('Average', 'Best');
            } elseif ($Format == 'Mean') {
                $Formats = array('Mean', 'Best');
            } else {
                $Formats = array('Best');
            }
            $competitors = DataBaseClass::QueryGenerate();
            ?> 
            <table class="table_new">
                <form action=""method="post"> 
                    <tr>
                        <td>&nbsp;</td>
                        <td width="300px">
                            <input hidden id="CompetitorID" name="CompetitorID">
                            <input hidden id="InputCompetitorName" name="InputCompetitorName">
                            <input hidden id="InputAttemptS" name="InputAttemptS">
                            <?php for ($i = 1; $i <= $Attemps; $i++) { ?>
                                <input hidden id="InputAttempt<?= $i ?>" name="InputAttempt[<?= $i ?>]">
                            <?php } ?>
                            <?php foreach ($Formats as $f => $format) { ?>
                                <input hidden id="InputAttempt<?= $format ?>" name="InputAttempt[<?= $format ?>]">
                            <?php } ?>
                            <input hidden autocomplete=off id="CompetitorName" value="" style="width:200px; font-size:18px; background-color:var(--light_green);"
                                   oninput="ParseName($(this).val())"
                                   > 
                        </td>
                        <td></td>
                        <td colspan="<?= $Attemps ?>">
                            <input disabled autocomplete=off id="AttemptS" placeholder="Click on competitor row in table" value="" style="width:100%; font-size:18px" 
                                   oninput="ParseResults($(this).val(),this.selectionStart)"
                                   onclick="ParseResults($(this).val(), this.selectionStart)"
                                   onkeyup="ParseResults($(this).val(), this.selectionStart)"
                                   >
                        </td>
                        <td class="table_new_center">
                            <input hidden style="margin:0px" type="submit" value="[Enter]" ID="Save" name="SaveAttempt"
                                   onclick="
                                           $('#InputCompetitorName').val($('#ParseName').html());
                                   <?php for ($i = 1; $i <= $Attemps; $i++) { ?>
                                               $('#InputAttempt<?= $i ?>').val($('#Attempt<?= $i ?>').html());
                                   <?php } ?>
                                   <?php foreach ($Formats as $f => $format) { ?>
                                               $('#InputAttempt<?= $format ?>').val($('#Attempt<?= $format ?>').html());
                                   <?php } ?>
                                           $('#InputAttemptS').val($('#AttemptS').val());
                                   "></td>
                        <td class="table_new_center"><input hidden style="margin:0px" type="submit" value="Back" ID="Back"  class="delete"
                                                            onclick="
                                                                    $('#CompetitorID').val('');
                                                                    $('#CompetitorName').val('');
                                                                    $('#AttemptS').val('');
                                                                    $('#AttemptS').prop('disabled', true);
                                                                    $('#CompetitorName').hide();
                                                                    $('.Attempt_edit').html('');
                                                                    $('.Attempt_edit').css('border', 'none');
                                                                    $('#ParseName').html('');
                                                                    this.hide();
                                                                    $('#Save').hide();
                                                                    return false;
                                                            "></td>
                    </tr>
                </form>
                <tr>
                    <td style="height:33px">&nbsp;</td>                    
                    <td style="vertical-align: middle;">
                        <div style="text-align:left; width:100%; font-size:18px; padding:2px; color:var(--gray);" id="ParseName">

                        </div>
                    </td>
                    <td></td>
                    <?php for ($i = 1; $i <= $Attemps; $i++) { ?>
                        <td>
                            <div style="text-align:center; border:1px solid var(--white); padding:2px;" class="Attempt_edit" id="Attempt<?= $i ?>">
                            </div>
                        </td>
                    <?php } ?>
                    <?php foreach ($Formats as $f => $format) { ?>
                        <td class="<?= $f == 0 ? 'border-left-solid' : '' ?>"><div style="text-align:center; width:100px; font-size:18px; border:1px solid var(--white); padding:2px;" class="Attempt_edit" id="Attempt<?= $format ?>"></div></td>
                    <?php } ?>
                </tr>
                <thead>
                    <tr>
                        <td>
                            #
                        </td>
                        <td>
                            Competitor [<?= sizeof($competitors) ?>]
                        </td>
                        <td></td>
                        <?php for ($i = 1; $i <= $Attemps; $i++) { ?>
                            <td class="attempt">
                                <?= $i ?>
                            </td>
                        <?php } ?>
                        <?php foreach ($Formats as $f => $format) { ?>  
                            <td class="<?= $f == 0 ? 'border-left-solid' : '' ?> attempt"><?= $format ?></td>  
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                <script>var CompetitorAttempS = []</script>
                <?php foreach ($competitors as $competitor) { ?>
                    <script>
                        CompetitorAttempS[<?= $competitor['MeetingCompetitorDiscipline_ID'] ?>] = '<?= $competitor['MeetingCompetitorDiscipline_Attempts'] ?>';
                        CompetitorName[<?= $competitor['MeetingCompetitorDiscipline_ID'] ?>] = '<?= $competitor['MeetingCompetitor_Name'] ?>';
                    </script>
                    <tr onclick="
                            var name = CompetitorName[<?= $competitor['MeetingCompetitorDiscipline_ID'] ?>];
                            var res = CompetitorAttempS[<?= $competitor['MeetingCompetitorDiscipline_ID'] ?>];
                            $('#CompetitorID').val(<?= $competitor['MeetingCompetitorDiscipline_ID'] ?>);
                            $('#CompetitorName').val(name);
                            $('#AttemptS').val(res);
                            $('#AttemptS').prop('disabled', false);
                            $('#Back').show();
                            $('#Save').show();
                            $('#CompetitorName').show();
                            ParseResults(res, 0);
                            ParseName(name);
                            $('#AttemptS').focus();
                            $('#AttemptS').get(0).setSelectionRange(0, 0);
                        ">
                        <td class="<?= $competitor ['MeetingCompetitorDiscipline_Place'] <= 3 ? 'podium' : '' ?>">
                            <?= $competitor['MeetingCompetitorDiscipline_Place'] ?>
                        </td>
                        <td>
                            <?= $competitor['MeetingCompetitor_Name'] ?>
                        </td>
                        <td>
                            <?php if (!$competitor['MeetingCompetitorDiscipline_Place']) { ?>
                                <input onclick="
                                        if (!confirm('Remove <?= $competitor['MeetingCompetitor_Name'] ?>?')) {
                                            return(false)
                                        }
                                        ;
                                        $('#CompetitorIDRemove').val(<?= $competitor['MeetingCompetitorDiscipline_ID'] ?>);
                                        $('#CompetitorRemove').submit();
                                       " style="margin:0px;padding:1px 2px;" type="submit" value="x" class="delete">
                                   <?php } ?>
                        </td>
                        <?php for ($i = 1; $i <= $Attemps; $i++) { ?>
                            <td class="attempt"> <?= $competitor['MeetingCompetitorDiscipline_Attempt' . $i] ?></td>
                        <?php } ?>
                        <?php foreach ($Formats as $f => $format) { ?>  
                            <td class="attempt" <?= $f == 0 ? 'style="font-weight:bold"' : '' ?>> <?= $competitor['MeetingCompetitorDiscipline_' . $format] ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <form method="POST" action="" ID="CompetitorRemove">
                <input hidden name="CompetitorRemove">
                <input hidden id="CompetitorIDRemove" name="CompetitorID">
            </form>
        <?php } ?> 
        <br>    
        <?php if (sizeof($competitors) > 0) { ?>
            <a target="_blank" href="<?= PageIndex() . "Actions/MeetingPrintScoreCards/?Secret=" . RequestClass::getParam1(); ?>&Discipline=<?= $current_discipline ?>">Print competitors cards [<?= sizeof($competitors) ?>]</a> ▪
        <?php } ?>        
        <a target="_blank" href="<?= PageIndex() . "Actions/MeetingPrintScoreCards/?Secret=" . RequestClass::getParam1(); ?>&Discipline=<?= $current_discipline ?>&blank">Print blank competitors cards</a> ▪
        <a target="_blank" href="<?= PageIndex() . "Actions/MeetingPrintResult/?Secret=" . RequestClass::getParam1(); ?>&Discipline=<?= $current_discipline ?>">Print the results</a> ▪
        <a target="_blank" href="<?= PageIndex() . "Actions/MeetingExportResult/?Secret=" . RequestClass::getParam1(); ?>&Discipline=<?= $current_discipline ?>">Export results</a>

        <br>
        <script>
            function ParseName(name) {
                name = name.replace(/[^A-zА-яЁё]/gim, ' ');
                name = name.replace(/ {1,}/g, " ").trim();
                $('#ParseName').html(name.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase()));
            }

            function ParseResults(results, pos) {

                var result_in = results;
                var Attemps_amount =<?= $Attemps ?>;
                var attemps = [];
                for (var i = 1; i <= 5; i++) {
                    attemps[i] = $('#Attempt' + i);
                    attemps[i].css("color", "var(--black)");
                    attemps[i].css("border", "2px solid var(--white)");
                }

                var res_cut = (results.substr(0, pos));
                results = results.replace(/DNF/gim, '-');
                results = results.replace(/dnf/gim, '-');
                results = results.replace(/DNS/gim, '0');
                results = results.replace(/dns/gim, '0');
                results = results.replace(/DN/gim, '0');
                results = results.replace(/dn/gim, '0');
                results = results.replace(/D/gim, '0');
                results = results.replace(/d/gim, '0');
                results = results.replace(/[-]/gim, ' - ');
                results = results.replace(/[^-0-9 ]/gim, '');
                results = results.replace(/ {1,}/g, " ").trim();

                res_cut = res_cut.replace(/DNF/gim, '-');
                res_cut = res_cut.replace(/dnf/gim, '-');
                res_cut = res_cut.replace(/DNS/gim, '0');
                res_cut = res_cut.replace(/dns/gim, '0');
                res_cut = res_cut.replace(/DN/gim, '0');
                res_cut = res_cut.replace(/dn/gim, '0');
                res_cut = res_cut.replace(/D/gim, '0');
                res_cut = res_cut.replace(/d/gim, '0');
                res_cut = res_cut.replace(/[-]/gim, ' - ');
                res_cut = res_cut.replace(/[^-0-9 ]/gim, '');
                res_cut = res_cut.replace(/ {1,}/g, " ").trim();

                var atts = results.split(" ");
                pos_len = 0;

                if (result_in.substr(pos - 1, 1) === ' ') {
                    res_current = res_cut.split(' ').length + 1;
                } else {
                    res_current = res_cut.split(' ').length;
                }

                if (res_current <= 5) {
                    attemps[res_current].css("border", "1px solid var(--blue)");
                }

                var attemp_input = results.split(' ').length;
                if (attemp_input > Attemps_amount) {
                    $("#AttemptS").css("background-color", " var(--light_red)");
                } else if (attemp_input < Attemps_amount) {
                    $("#AttemptS").css("background-color", "#FFD");
                } else {
                    $("#AttemptS").css("background-color", "var(--light_green)");
                }

                if (attemp_input > 0 && pos > 0) {
                    $("#AttemptBest").css("color", "var(--green)");
                } else {
                    $("#AttemptBest").css("color", "var(--red)");
                }

                var att;
                var att_correct = 0;
                var attemp_miliseconds = [];
                for (a = atts.length; a < Attemps_amount; a++) {
                    attemps[a + 1].html('DNS');
                    attemps[a + 1].css("color", "var(--red)");
                    attemp_miliseconds[a + 1] = 999999;
                }
                for (a = 0; a < atts.length; a++) {
                    attemp_miliseconds[a + 1] = 999999;
                    att = atts[a];
                    if (att === '-') {
                        attemps[a + 1].html('DNF');
                        attemps[a + 1].css("color", "var(--red)");
                    } else if (att == '') {
                        attemps[a + 1].html('DNS');
                        attemps[a + 1].css("color", "var(--red)");
                    } else if (att == '0') {
                        attemps[a + 1].html('DNS');
                        attemps[a + 1].css("color", "var(--red)");
                    } else {
                        if (a < Attemps_amount) {
                            attemps[a + 1].css("color", "var(--black)");
                            att_correct = att_correct + 1;
                            if (<?= $isAmount ?>) {
                                attemp_miliseconds[a + 1] = Number(att);
                                attemps[a + 1].html(att);
                            } else {
                                att = "000000" + att;
                                att = att.substring(att.length - 6);
                                var minute = att.substr(0, 2);
                                var second = att.substr(2, 2);
                                var milisecond = att.substr(4, 2);
                                if (minute.substr(0, 1) === '0') {
                                    minute = minute.substr(1, 1);
                                }
                                attemp_miliseconds[a + 1] = Number(minute) * 60 * 100 + Number(second) * 100 + Number(milisecond);
                                attemps[a + 1].html(minute + ':' + second + '.' + milisecond);
                            }
                        }

                    }
                }

                if (att_correct === 5) {
                    var average = 0;
                    var max = 0;
                    var min = 999999;
                    for (var a = 0; a < 5; a++) {
                        average = average + attemp_miliseconds[a + 1];
                        if (min > attemp_miliseconds[a + 1]) {
                            min = attemp_miliseconds[a + 1];
                        }
                        if (max < attemp_miliseconds[a + 1]) {
                            max = attemp_miliseconds[a + 1];
                        }

                    }

                    average = average - min - max;
                    $("#AttemptAverage").css("color", "var(--green)");
                    if (<?= $isAmount ?>) {
                        average = Math.round(average * 100 / 3.0) / 100;
                        average = average.toFixed(2);
                        $("#AttemptAverage").html(average);
                    } else {
                        average = Math.round(average / 3.0, 0);
                        var minute = Math.floor(average / 60 / 100);
                        var second = Math.floor((average - minute * 60 * 100) / 100);
                        var milisecond = Math.round(average - minute * 60 * 100 - second * 100, 0);
                        $("#AttemptAverage").html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
                    }
                }
                if (att_correct === 4) {
                    var average = 0;
                    var min = 999999;
                    for (var a = 0; a < 5; a++) {
                        if (attemp_miliseconds[a + 1] !== 999999) {
                            average = average + attemp_miliseconds[a + 1];
                            if (min > attemp_miliseconds[a + 1]) {
                                min = attemp_miliseconds[a + 1];
                            }
                        }
                    }
                    average = average - min;

                    $("#AttemptAverage").css("color", "var(--green)");
                    if (<?= $isAmount ?>) {
                        average = Math.round(average * 100 / 3.0) / 100;
                        average = average.toFixed(2);
                        $("#AttemptAverage").html(average);
                    } else {
                        average = Math.round(average / 3.0, 0);
                        var minute = Math.floor(average / 60 / 100);
                        var second = Math.floor((average - minute * 60 * 100) / 100);
                        var milisecond = Math.round(average - minute * 60 * 100 - second * 100, 0);
                        $("#AttemptAverage").html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
                    }
                }

                if (att_correct === 3) {

                    var mean = 0;
                    for (var a = 0; a < 3; a++) {
                        mean = mean + attemp_miliseconds[a + 1];
                    }
                    $("#AttemptMean").css("color", "var(--green)");
                    if (<?= $isAmount ?>) {
                        mean = Math.round(mean * 100 / 3.0) / 100;
                        mean = mean.toFixed(2);
                        $("#AttemptMean").html(mean);
                    } else {
                        mean = Math.round(mean / 3.0);
                        var minute = Math.floor(mean / 60 / 100);
                        var second = Math.floor((mean - minute * 60 * 100) / 100);
                        var milisecond = Math.round(mean - minute * 60 * 100 - second * 100, 0);
                        $("#AttemptMean").html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
                    }
                } else {
                    $("#AttemptMean").css("color", "var(--red)");
                    $("#AttemptMean").html('DNF');
                }

                if (att_correct < 4) {
                    $("#AttemptAverage").css("color", "var(--red)");
                    $("#AttemptAverage").html('DNF');
                }

                if (att_correct <= 2 && attemps[3].html() == 'DNS' && attemps[4].html() == 'DNS' && attemps[5].html() == 'DNS') {
                    $("#AttemptAverage").css("color", "var(--green)");
                    $("#AttemptAverage").html('-cutoff');
                }

                if (att_correct >= 1) {
                    var min = 999999;
                    for (var a = 0; a < 5; a++) {
                        if (attemp_miliseconds[a + 1] !== 999999) {
                            if (min > attemp_miliseconds[a + 1]) {
                                min = attemp_miliseconds[a + 1];
                            }
                        }
                    }
                    var minute = Math.floor(min / 60 / 100);
                    var second = Math.floor((min - minute * 60 * 100) / 100);
                    var milisecond = Math.round(min - minute * 60 * 100 - second * 100, 0);

                    $("#AttemptBest").css("color", "var(--green)");
                    if (<?= $isAmount ?>) {
                        $("#AttemptBest").html(min);
                    } else {
                        $("#AttemptBest").html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
                    }
                } else {
                    $("#AttemptBest").css("color", "var(--red)");
                    $("#AttemptBest").html('DNF');
                }

            }

            //ParseResults($("#AttemptS").val(),0);
            //ParseName($("#CompetitorName").val());
        </script>
    </div>     
    <?php if ($Current_discipline['MeetingDiscipline_Round'] == 1) { ?>
        <?php
        DataBaseClass::Query("Select MC.ID,MC.Name from MeetingCompetitor MC where MC.Meeting=" . $meeting['Meeting_ID']
                . " and  MC.ID not in (select MeetingCompetitor from MeetingCompetitorDiscipline MCD where MCD.MeetingDiscipline=$current_discipline) order by MC.Name");
        $competitors = DataBaseClass::GetRows();
        ?>
        <?php if (sizeof($competitors)) { ?>
            <div class="shadow2" >
                <h2>Adding competitors to the event</h2>
                <form method="POST" action="">
                    <table class="table_new">
                        <thead>
                            <tr>
                                <td>Competitor</td>
                                <td>Add</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($competitors as $competitor) { ?>
                                <tr>
                                    <td><?= $competitor['Name'] ?> </td>
                                    <td align="center"><input name="Competitor[<?= $competitor['ID'] ?>]"  type="Checkbox"></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <input type="hidden" value="<?= $current_discipline ?>" name="Discipline">
                    <input type="hidden" value="AddRegistrations" name="AddRegistrations">
                    <button>
                        <i class="fas fa-user-plus"></i>
                        Add competitions
                    </button>
                </form>
            <?php } ?>
        </div>     
    <?php } else { ?>
        <?php
        DataBaseClass::Query("select MC.ID,MC.Name,MCD.Place,MCD2.ID Reg from `MeetingDiscipline` MD
join MeetingDiscipline MD1 on MD1.MeetingDisciplineList=MD.MeetingDisciplineList and MD1.Round=1 and MD.Meeting=MD1.Meeting
join `MeetingCompetitorDiscipline` MCD on MCD.MeetingDiscipline=MD1.ID and MCD.Place is not null
join `MeetingCompetitor` MC on MC.ID=MCD.MeetingCompetitor
left outer join MeetingCompetitorDiscipline MCD2 on MCD2.MeetingCompetitor=MC.ID and MCD2.MeetingDiscipline=MD.ID
where MD.ID=$current_discipline
order by MCD.Place");
        $competitors = DataBaseClass::GetRows();
        ?>


        <?php if (sizeof($competitors)) { ?>
            <div class="shadow2" >
                <h2>Adding competitors to round <?= $Current_discipline['MeetingDiscipline_Round'] ?></h2>
                <form method="POST" action="">
                    <table class="table_new">
                        <thead>
                            <tr>
                                <td>
                                    Place in the preview round
                                </td>
                                <td>
                                    Competitor
                                </td>
                                <td>
                                    Add
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($competitors as $competitor) { ?>
                                <tr>
                                    <td align="right">
                                        <?= $competitor['Place'] ?> 
                                    </td>
                                    <td>
                                        <?= $competitor['Name'] ?>
                                    </td>
                                    <td align="center">
                                        <?php if ($competitor['Reg']) { ?>
                                            +
                                        <?php } else { ?>
                                            <input name="Competitor[<?= $competitor['ID'] ?>]"  type="Checkbox">
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <input type="hidden" value="<?= $current_discipline ?>" name="Discipline">
                    <input type="hidden" value="AddRegistrations" name="AddRegistrations">
                    <button>
                        <i class="fas fa-user-plus"></i>
                        Add competitions
                    </button>
                </form>  
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>