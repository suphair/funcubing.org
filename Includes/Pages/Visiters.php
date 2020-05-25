
    <?php
    $Dates=[];
    $Counts=[];
    $CountNew=[];
    $CountOld=[];
    $Mean=[];
    $Total=0;
    DataBaseClass::Query("Select count(distinct V.IP) Count,"
            . " count(distinct V2.IP) CountNew,"
            . " V.Date Date from `Visit` V"
            . " left outer join Visit V2 on V2.IP=V.IP and V.Date>V2.Date and V2.Hidden=0"
            . " where V.Hidden=0"
            . " group by V.Date order by V.Date");
    foreach(DataBaseClass::getRows() as $row){
        $Dates[]=$row['Date'];
        $Counts[]=$row['Count'];
        $CountNew[]=$row['CountNew'];
        $CountOld[]=$row['Count']-$row['CountNew'];
        $Total+=$row['Count'];
        $Last=$row['Count'];
        $Mean[]=round($Total/sizeof($Dates));
    } 
    array_pop($Mean);
    
    ?>
<h1>Visiters (<?= end($Mean) ?> a day) </h1>

<script src="<?= PageIndex() ?>Script/Chart.js?t=1"></script>
<script src="<?= PageIndex() ?>Script/Chart.min.js"></script>
<canvas id="myChart_Visiters" width=800px height=400px></canvas>
<script>
var ctx = document.getElementById("myChart_Visiters").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['<?=  implode("','",$Dates); ?>'],
        datasets: [
            {
            label: 'Visiters',
            data: [<?= implode(",",$Counts); ?>],
            backgroundColor:window.chartColors.purple,
            borderColor:window.chartColors.purple,
            fill:false,
            borderWidth: 1,
            yAxisID: 'y-axis-1'
        },{
            label: 'New',
            data: [<?= implode(",",$CountNew); ?>],
            backgroundColor:window.chartColors.red,
            borderColor:window.chartColors.red,
            pointRadius: 1,
            fill:false,
            borderWidth: 1,
            yAxisID: 'y-axis-1'
        },{
            label: 'Returned',
            data: [<?= implode(",",$CountOld); ?>],
            backgroundColor:window.chartColors.green,
            borderColor:window.chartColors.green,
            pointRadius: 1,
            fill:false,
            borderWidth: 1,
            yAxisID: 'y-axis-1'
        },{
            label: 'Mean',
            data: [<?= implode(",",$Mean); ?>],
            backgroundColor:window.chartColors.black,
            borderColor:window.chartColors.black,
            pointRadius: 0,
            fill:false,
            borderWidth: 1,
            yAxisID: 'y-axis-1'
        }]
    },
    options: {
        responsive: false,
        tooltips: {
                mode: 'index',
                intersect: false,
        },
        hover: {
                mode: 'nearest',
                intersect: true
        },
        scales: {
            yAxes: [{
                
                display: true,
                position: 'left',
                id: 'y-axis-1',
                ticks: {
                  fontColor: window.chartColors.red
                }
            }]
        }
    }
});
</script>
    <?php
    $Dates=[];
    $Counts=[];
    $CountNew=[];
    $CountOld=[];
    $Total=0;        
    DataBaseClass::Query("Select count(distinct W.WID) Count,"
            . " count(distinct W.WID) CountNew,"
            . " date(W.Timestamp) Date from `WCAauth` W"
            . " left outer join WCAauth W2 on W2.WID=W.WID and date(W.Timestamp)>date(W2.Timestamp)"
            . " where W2.WID is null and W.WID is not null"
            . " group by date(W.Timestamp) order by date(W.Timestamp)");
    foreach(DataBaseClass::getRows() as $row){
        $Dates[]=$row['Date'];
        $CountNew[]=$row['CountNew'];
        $Total+=$row['CountNew'];
        $Counts[]=$Total;
    } ?>


<h1>Authorizations (total <?= $Total ?>)</h1>

<canvas id="myChart_Authorizations" width=800px height=400px></canvas>
<script>
var ctx = document.getElementById("myChart_Authorizations").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['<?=  implode("','",$Dates); ?>'],
        datasets: [
            {
            label: 'Authorizations',
            data: [<?= implode(",",$Counts); ?>],
            backgroundColor:window.chartColors.purple,
            borderColor:window.chartColors.purple,
            pointRadius: 1,
            fill:false,
            borderWidth: 1,
            yAxisID: 'y-axis-1'
        }]
    },
    options: {
        responsive: false,
        tooltips: {
                mode: 'index',
                intersect: false,
        },
        hover: {
                mode: 'nearest',
                intersect: true
        },
        scales: {
            yAxes: [{
                type: 'linear',
                display: true,
                position: 'left',
                id: 'y-axis-1',
                ticks: {
                  fontColor: window.chartColors.red
                }
            }]
        }
    }
});
</script>





