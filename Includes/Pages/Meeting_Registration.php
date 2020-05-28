<br><br><br>
<div class="shadow2">
<h2>
        <i class="fas fa-user-plus"></i>
        Self-registration
</h2>
    <br>
<?php
DataBaseClass::FromTable("MeetingCompetitor", "Session='" . session_id() . "'");
DataBaseClass::Where_current("Meeting=" . $meeting['Meeting_ID']);
DataBaseClass::Join_current("MeetingCompetitorDiscipline");
DataBaseClass::Join_current("MeetingDiscipline");
DataBaseClass::Join_current("MeetingDisciplineList");
$CompetitorsDiscipline = array();
$Competitors = array();
foreach (DataBaseClass::QueryGenerate() as $row) {
    ?>
    <?php
    $CompetitorsDiscipline[$row['MeetingCompetitor_ID']][] = $row;
    $Competitors[$row['MeetingCompetitor_ID']] = $row['MeetingCompetitor_Name'];
    ?>
<?php } ?>
<?php if (sizeof($Competitors)) { ?>
    <h3>
        <i class="fas fa-user-check"></i>
        You have registered {Вы зарегистрировались}
    </h3>
    <table class="table_new">
        <tbody>
    <?php foreach ($Competitors as $ID => $Name) { ?>
                <tr>
                    <td><nobr><?= $Name ?></nobr></td>
        <td><input style='padding:3px; margin:0px;' type="submit" value="Change"
                   onclick="
                           $('.Discipline_check').attr('checked', false);
                           $('#SubmitRegister').val('Change {Изменить}');
                           $('#CompetitorName').val('<?= $Name ?>');
                   <?php foreach ($CompetitorsDiscipline[$ID] as $discipline) { ?>
                               $('#<?= $discipline['MeetingDiscipline_ID'] ?>').attr('checked', true);
                   <?php } ?>
                           CheckName();
                           ChecksActual();
                           $('#CancelEditRegister').show();
                   ">
        </td>
        <td>
            <form method="POST" action="<?= PageIndex() . "Actions/MeetingRegistrationDelete" ?>">
                <input hidden name="Secret" value="<?= $meeting['Meeting_Secret'] ?>">
                <input hidden name="SecretRegistration" value="<?= $_GET['secret'] ?>">
                <input hidden name="Competitor" value="<?= $ID ?>">
                <input style='padding:3px; margin:0px;' 
                       type="submit" 
                       class="delete" 
                       value="Delete"
                       onclick="return confirm('Delete registration <?= $Name ?>?')">
            </form>
        </td>
        <td>
            <?php foreach ($CompetitorsDiscipline[$ID] as $discipline) { ?>   
                <i  style="cursor:pointer" class="<?= $discipline['MeetingDisciplineList_Image'] ?>"></i>
        <?php } ?>
        </td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    <hr>
<?php } ?>

<script>
    function ParseName(name) {
        name = name.replace(/[^A-zА-яЁё\- ]/gim, '');
        name = name.replace(/ {1,}/g, " ").trim();
        return name.toLowerCase().replace(/(^|\s)\S/g, l => l.toUpperCase());
    }

    function CheckDisabled() {
        var name = $('#DescriptionParseName').val();
        var disabled = true;
        if (name.replace(/[A-z]/gim, '') == name || name.replace(/[А-яЁё]/gim, '') == name) {
            if ($('#DescriptionParseName').val() !== '' && $('#DescriptionParseName').val().indexOf(' ') > -1
                    && $('[data-check-event]:checked').length > 0) {
                disabled = false;
            }
        }
        $('#SubmitRegister').prop('disabled', disabled);
    }

    function CheckName() {
        var Name_ = ParseName($("#CompetitorName").val());
        $('#DescriptionName').html(Name_);
        $('#DescriptionParseName').val(Name_);

        CheckDisabled();
    }
</script>   
<?php
DataBaseClass::FromTable("Meeting", "ID=" . $meeting['Meeting_ID']);
DataBaseClass::Join_current("MeetingDiscipline");
DataBaseClass::Where_current("Round=1");
DataBaseClass::Join_current("MeetingDisciplineList");
DataBaseClass::OrderClear("MeetingDisciplineList", "ID");
?>
        <form method="POST" action="<?= PageIndex() . "Actions/MeetingRegistration" ?>">
            <h3>Select your events {Выберите дисциплины}</h3>
            <script>
                var Disciplines = [];
            </script>
            <table class="table_new">
                <thead>
                </thead>
                <tbody>
<?php foreach (DataBaseClass::QueryGenerate() as $row) { ?>
                        <tr>
                            <td>
                                <label for="<?= $row['MeetingDiscipline_ID'] ?>">
                                    <i  style="cursor:pointer" class="<?= $row['MeetingDisciplineList_Image'] ?>"></i>
                                </label>
                            </td>
                            <td>
    <?= $row['MeetingDiscipline_Name'] ?>

                            </td>
                            <td>
                                <input 
                                    data-check-event
                                    name='Disciplines[<?= $row['MeetingDiscipline_ID'] ?>]' id="<?= $row['MeetingDiscipline_ID'] ?>" 
                                    class="check_big Discipline_check" type='checkbox'>
                            </td>
                        </tr>
<?php } ?>
                </tbody>
            </table>
            <p style="color:rgb(0,182,67)" id='DescriptionDiscipline'></p>
            <h3>Enter your name {Введите ваше имя и фамилию}</h3>
            <input ID="CompetitorName" oninput="CheckName();" required="" placeholder="Name Surname" style="font-size:24px; width:500px">
            <input hidden  hidden id='DescriptionParseName'  name='Name' >
            <p style="color:rgb(0,182,67); font-size:24px" id='DescriptionName'></p>
            <input disabled style="font-size:24px" type="submit" id="SubmitRegister" name="CreateRegistration" value="Register {Зарегистрироваться}">
            <input hidden 
                   onclick="
                           $('.Discipline_check').attr('checked', false);
                           $('#SubmitRegister').val('Register {Зарегистрироваться}');
                           $('#CompetitorName').val('');
                           CheckName();
                           $(this).hide();
                           return(false);

                   "
                   class="delete" style="font-size:24px" type="submit" id="CancelEditRegister" value="Cancel {Не изменять}">
            <input hidden name="Secret" value="<?= $meeting['Meeting_Secret'] ?>">
            <input hidden name="SecretRegistration" value="<?= $_GET['secret'] ?>">
        </form>
<script>
    $('[data-check-event]').change(function () {
        CheckDisabled();
    });
</script>
</div>