<?php
if(isset($_GET['ID']) and is_numeric(($_GET['ID'])) ){
    $ID=$_GET['ID'];
    
    DataBaseClass::Query("select C.Name, C.Country, C.WCAID, case when CE.Place>0 then CE.Place else '' end Place, CE.ID, CE.Decline,CE.CardID from `CompetitorEvent` CE"
        . " join `Competitor` C on C.ID=CE.Competitor where CE.Event='$ID' and not CE.Decline order by case when CE.Place>0 then CE.Place else 999 end, C.Name");
        $competitors=DataBaseClass::getRows();

    DataBaseClass::Query("select F.Attemption, F.Result, C.Name Competition, D.Name Discipline from `Format` F "
        . " join `Discipline` D on D.Format=F.ID "
        . " join `Event` E on E.Discipline = D.ID "
        ." join Competition C on C.ID=E.Competition where E.ID='$ID'");
        $data=DataBaseClass::getRow();
        
    $ExcelName='Result '.$data['Competition'].'-'.$data['Discipline']; 
    $ExcelData=array();
    $ExcelColumn=array();
    $ExcelColumn['Position']=array('width'=>8);
    $ExcelColumn['Name']=array('bold'=>1,'width'=>20);
    $ExcelColumn['WCAID']=array('width'=>15);
    $ExcelColumn['Country']=array('width'=>10);
    
    for($i=1;$i<=$data['Attemption'];$i++) {
            $ExcelColumn[$i]=array('width'=>6);
    }
    
    $ExcelColumn[$data['Result']]=array('bold'=>1,'width'=>8);
    if(print_best($data['Result'])){
        $ExcelColumn['Best']=array('width'=>8);
    }
    
    foreach($competitors as $n=>$competitor){
        
        $ExcelData[$n]['Position']=$competitor['Place'];
        $ExcelData[$n]['Name']=$competitor['Name'];
        $ExcelData[$n]['WCAID']=$competitor['WCAID'];
        $ExcelData[$n]['Country']=$competitor['Country'];

        
        DataBaseClass::Query("select * from `Attempt` A where CompetitorEvent='".$competitor['ID']."' ");
        $attempt_rows=DataBaseClass::getRows();
        $attempts=array();
        for($i=1;$i<=$data['Attemption'];$i++) {
            $attempts[$i]="";
        }
        
        foreach(DataBaseClass::SelectTableRows("Format") as $format){
            $attempts[$format['Format_Result']]="";    
        }

        foreach($attempt_rows as $attempt_row){
            if($attempt_row['IsDNF']){
                $attempt='DNF';
            }elseif($attempt_row['IsDNS']){
                $attempt='DNS';
            }else{
                $attempt=$attempt_row['Minute']*60 + $attempt_row['Second'] + $attempt_row['Milisecond']/100;
            }

            if($attempt_row['Attempt']){
               $attempts[$attempt_row['Attempt']]= $attempt;
            }else{
               $attempts[$attempt_row['Special']]= $attempt; 
            }
        }
        for($i=0;$i<$data['Attemption'];$i++){
            $ExcelData[$n][$i+1]=$attempts[$i+1];
        }
        
        $ExcelData[$n][$data['Result']]=$attempts[$data['Result']];
        if(print_best($data['Result'])){
           $ExcelData[$n]['Best']=$attempts['Best'];
        }
        
    }
    
    GenerateExcel($ExcelName,$ExcelColumn,$ExcelData);

}else{
     echo 'Not found';
}