<?php if(CheckAdmin()){ 
     //CommandUpdate();
?>
<table style="white-space:nowrap" >
<thead>
<tr class="tr_title">
    <td>DateTime</td>
    <td>Competitor</td>
    <td>Action</td>
    <td>Object</td>
    <td>Details</td>
</tr>
</thead>
    <?php DataBaseClass::Query("Select C.Name Competitor_Name, C.ID Competitor_ID, L.*  from Logs L"
            . " left outer join Competitor C on C.WID=L.Competitor "
            . " where date(DateTime)>=DATE_ADD(current_date(),INTERVAL -3 Day)"
            . "  order by L.ID desc");
        foreach(DataBaseClass::getRows() as $log){ ?>
<tr>
<td><?= $log['DateTime'] ?></td>
<td><a href="<?= LinkCompetitor($log['Competitor_ID']) ?>"><?= short_Name($log['Competitor_Name']) ?></a></td>
<td><?= $log['Action'] ?></td>
<td><?= $log['Object'] ?></td>
<td>: <?= $log['Details'] ?></td>
</tr>
        <?php } ?>         
</table>
<?php }else{ ?>
 access denied
<?php }?>