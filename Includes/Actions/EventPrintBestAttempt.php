<?php
if(isset($_GET['ID']) and is_numeric(($_GET['ID'])) ){
    $ID=$_GET['ID'];
    
     DataBaseClass::Query("select  C.Name Competition, D.Name Discipline, C.WCA Competition_WCA, D.Code Discipline_Code, E.Groups from `Discipline` D "
    . " join `Event` E on E.Discipline = D.ID "
    ." join Competition C on C.ID=E.Competition where E.ID='$ID'");
    $data=DataBaseClass::getRow();
    
    
    DataBaseClass::Query("select "
        . "Com.vName, A.Attempt,Minute, Second, Milisecond   from `Command` Com"
        . " join Attempt A on A.Command=Com.ID"
        . " where Com.Event='$ID' and not Com.Decline and not A.isDNF and not A.isDNS and A.Special is null "
        . " order by A.vOrder");
        
    $BestAttempt=array();
    foreach(DataBaseClass::getRows() as $row){
        if(!isset($BestAttempt[$row['Attempt']])){
            $BestAttempt[$row['Attempt']]=$row;
        }
    }
        
    $pdf = new FPDF('P','mm');   
    $pdf->SetFont('courier');
    $xStart=5;
    $xEnd=$pdf->w - 5;    
    
    $pdf->AddPage();    
    $pdf->SetLineWidth(1);
    $n=0;


foreach($BestAttempt as $n=>$row){      
        if($n%2 ==0){
            $pdf->SetFillColor(240,240,240);
            $pdf->Rect(5, 38+($n-1)*8, 119, 8, "F");
        }
        $pdf->SetLineWidth(0.3);
        if($n>0){
            $pdf->Line(5, 38+($n-1)*8 , 124, 38+($n-1)*8);
        }
        $pdf->Line(5, 38+$n*8 , 124, 38+$n*8);
    
         
        $pdf->SetFont('Arial','',10);
        $pdf->Text(50, 35+$n*8, iconv('utf-8', 'cp1252//TRANSLIT',$row['vName']));
        if($image=IconAttempt($data['Discipline_Code'],$row['Attempt'])){
            $pdf->Image($image,8,31+$n*8,5,5);       
        }     
        
        $pdf->Text(17 , 35+$n*8,IconAttempt_DisciplineName($image,$data['Discipline_Code'],$row['Attempt']));
        
        if($row['Minute']){
            $attempt=$row['Minute'].':'.sprintf('%02d', $row['Second']).".".sprintf('%02d', $row['Milisecond']);
        }else{
           $attempt=$row['Second'].".".sprintf('%02d', $row['Milisecond']);  
        }
            
        $pdf->Text(100, 35+$n*8, sprintf('%0 10s',$attempt));
    }
    
    
    if(file_exists("Image/Discipline/".$data['Discipline_Code'].'.jpg')){
        $pdf->Image("Image/Discipline/".$data['Discipline_Code'].'.jpg',5,5,20,20,'jpg');
    }
   
        $pdf->Image("Image/FC_B.png",$pdf->w-25,5,20,20,'png');

    
    $pdf->SetFont('Arial','',16);
    $pdf->Text(30, 13, 'Best attempt');
    $str=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
    $pdf->Text(30, 23, $str);
    $pdf->SetFont('Arial','',20);
    $pdf->SetLineWidth(0.3);
    
    
    $pdf->SetLineWidth(0.1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Text(17, 35, 'Puzzle');
    $pdf->Text(50, 35, 'Competitor');
    $pdf->Text(103, 35, 'Result');
    
    $pdf->Line(5, 30, 5, 32+8*(sizeof($BestAttempt)+1));
    $pdf->Line(44, 30, 44, 32+8*(sizeof($BestAttempt)+1));
    $pdf->Line(94, 30, 94, 32+8*(sizeof($BestAttempt)+1));
    $pdf->Line(124, 30, 124, 32+8*(sizeof($BestAttempt)+1));
    $pdf->SetFont('Arial','B',10);
   // $pdf->Text($xEnd -  $dX * 1+5, 35,  str_replace('Average','Avg',$data['Result']));

    $pdf->SetFont('Arial','',10);

    

    $pdf->SetFont('Arial','',10);
    $pdf->Text(80, 286,GetIni('TEXT','print'));
     
$pdf->Output();              
$pdf->Close();
exit();

}else{
     echo 'Not found';
}