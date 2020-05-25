<?php $Competitor = GetCompetitorData(); ?>
<?php if (!$Competitor) { ?>    
    <?php $_SESSION['Refer'] = $_SERVER['REQUEST_URI']; ?>    
    <span class="error">
        To create unofficial competition you need to 
        <a href="<?= GetUrlWCA(); ?>">
            sign in with WCA
        </a>
    </span> 
<?php } else { ?>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker").datepicker({dateFormat: "dd.mm.yy"});
        });
    </script>

    <b>Create unofficial competition</b>
    <form method="POST" action="<?= PageIndex() . "Actions/MeetingCreate" ?>">
        Name 
        <input required placeholder="RamenskoeMeeting #1" type="text" name="Name" value="" />
        Date 
        <input style="width:140px" required type="text" id="datepicker" name="Date">

        <button>
            <i class="fas fa-plus-circle"></i> 
            Create
        </button>
    </form>
<?php } ?>
<hr>
<?php
$sql = "Select M.Website Meeting_Website, M.ID Meeting_ID,M.Show, M.Competitor Meeting_Competitor,M.Secret Meeting_Secret,M.Name Meeting_Name,M.Details Meeting_Details, C.Name Competitor_Name, M.Date Meeting_Date "
        . " from Meeting M "
        . " join Competitor C on C.WID=M.Competitor "
        . "where ";
if (!isset($_GET['My'])) {
    $sql .= " M.Show=1 or ";
}
if ($Competitor) {
    $sql .= " Competitor=" . $Competitor->id;
    $sql .= " or '$Competitor->wca_id' in (select WCAID from MeetingOrganizer MO where MO.Meeting=M.ID) ";
    if (CheckMeetingGrand() and isset($_GET['My'])) {
        $sql .= " or 0=0";
    }
} else {
    $sql .= " Competitor=0";
}
$sql .= " order by M.ID desc";
?>

<?php
if (CheckMeetingGrand() and isset($_GET['update'])) {
    DataBaseClass::Query("update MeetingDiscipline MD
join `MeetingDisciplineList` MDL on MDL.ID=MD.`MeetingDisciplineList`
SET MD.Name=MDL.Name
where MDL.ID>=100");

    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=83");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=84");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=173");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=201 where ID=174");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=144");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=201 where ID=145");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=216");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=201 where ID=217");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=200");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=201 where ID=214");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=157");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=240");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=201 where ID=241");
    DataBaseClass::Query("Update MeetingDiscipline set MeetingDisciplineList=200 where ID=284");
}
?>


<table class='table_new'>
    <thead>
        <tr>
            <?php if (!isset($_GET['My']) or CheckMeetingGrand()) { ?>
                <td>
                    <i class="fas fa-user-tie"></i>
                    Organizer
                </td>
            <?php } ?>
            <td>
                <i class="fas fa-cube"></i>
                Competition
            </td>
            <td>
                <i class="far fa-calendar-alt"></i> 
                Date
            </td>
            <td>
                <i class="fas fa-link"></i>
                Website
            </td>
            <?php if (CheckMeetingGrand() and isset($_GET['My'])) { ?>
                <td>Action</td>
            <?php } ?>
        </tr>    
    </thead>
    <tbody>
        <?php
        DataBaseClass::Query($sql);
        foreach (DataBaseClass::getRows() as $meeting) {
            ?>
        <form method="POST" action="<?= PageIndex() . "Actions/MeetingShow" ?>">
            <tr>   

                <?php if (!isset($_GET['My']) or CheckMeetingGrand()) { ?>
                    <td>
                        <span class="<?= ($Competitor and $meeting['Meeting_Competitor'] == $Competitor->id) ? 'message' : '' ?>">
                            <?= short_name($meeting['Competitor_Name']) ?>
                        </span>
                    </td>   
                <?php } ?>

                <td>
                    <a href="<?= PageIndex() ?>Meetings/<?= $meeting['Meeting_Secret'] ?>"><?= $meeting['Meeting_Name'] ?> </a>
                </td>
                <td>
            <nobr><?= date('d M Y', strtotime($meeting['Meeting_Date'])) ?></nobr>
            </td>
            <td align=right>
                <?php if ($meeting['Meeting_Website']) { ?>
                    <a target="_blank" href="<?= $meeting['Meeting_Website'] ?>">
                        <?php preg_match('/https?:\/\/(?:www\.|)([\w.-]+).*/', $meeting['Meeting_Website'], $matches); ?>
                        <?php if (isset($matches[1])) { ?>
                            <?= $matches[1] ?>
                        <?php } else { ?>
                            Website
                        <?php } ?>        
                    </a>
                <?php } ?>
            </td>
            <?php if (CheckMeetingGrand() and isset($_GET['My'])) { ?>
                <td>
                    <input type="hidden" name="Meeting" value="<?= $meeting['Meeting_ID'] ?>">
                    <?php if ($meeting['Show']) { ?>
                        <input style="margin:0px;padding:1px 2px;" type="submit" name="Action" class="delete" value="Hide">
                    <?php } else { ?>
                        <input style="margin:0px;padding:1px 2px;" type="submit" name="Action" value="Show">
                    <?php } ?>        
                </td>
            <?php } ?>

            </tr>
        </form>
    <?php } ?>
</tbody>
</table>   

<?php if (!isset($_GET['My']) or ! $Competitor) { ?>    
    <hr class="hr_round">
    <div class="wrapper">
        <div class="form instruction" align='left'>
            <h2>Instructions</h2>
            <font style='color:rgb(0,182,67)'>▪</font> <b>Create</b> unofficial competition<br>
            <img  width="600px" src='Image/MeetingInstructions/1.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Share</b> the link (example: <a target="_blank" href="https://funcubing.org/Meetings/o3lispqv86">https://funcubing.org/Meetings/<font style='color:rgb(0,182,67)'>o3lispqv86</font></a>)<br>
            <font style='color:rgb(0,182,67)'>▪</font> Add the <b>rounds</b> to the <b>disciplines</b><br>
            <img width="300px"  src='Image/MeetingInstructions/2.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> Add <b>competitors</b> with registrations of the first rounds<br>
            <img width="400px" src='Image/MeetingInstructions/3.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> Or add <b>competitors</b> without registrations<br>
            <img width="300px" src='Image/MeetingInstructions/16.png'><br>
            <br>&nbsp;&nbsp;<font style='color:rgb(0,182,67)'>▪▪</font> And then <b>assign</b> competitor of the disciplines<br>
            <img width="400px" src='Image/MeetingInstructions/5.png'><br>
            <br>&nbsp;&nbsp;<font style='color:rgb(0,182,67)'>▪▪</font> Or <b>assign</b> competitors of the discipline<br>
            <img width="400px" src='Image/MeetingInstructions/17.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Print</b> competitors cards of the first rounds<br>
            <img src='Image/MeetingInstructions/7.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Enter</b> the results of the first rounds<br>
            <img width="600px"  src='Image/MeetingInstructions/8.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Print</b> competitors results of the first rounds<br>
            <img width="600px"  src='Image/MeetingInstructions/10.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Assign</b> competitors to the finals<br>
            <img width="300px" src='Image/MeetingInstructions/11.png'><br>
            <br><br><font style='color:rgb(0,182,67)'>▪</font> <b>Print</b> competitors cards of the finals<br>
            <img src='Image/MeetingInstructions/13.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Enter</b> the results of the finals<br>
            <img width="600px"  src='Image/MeetingInstructions/14.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Print</b> competitors results of the finals<br>
            <img width="600px" src='Image/MeetingInstructions/18.png'><br>
            <br><font style='color:rgb(0,182,67)'>▪</font> <b>Download</b> competitors sertificates<br>
            <img width="600px"   src='Image/MeetingInstructions/15.png'><br>
        </div>
    </div>
<?php } ?>