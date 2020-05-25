<?php
if(isset($_GET['ID']) and is_numeric(($_GET['ID'])) ){
    $ID=$_GET['ID'];
    
    CheckingRoleDelegateEvent($ID,false);
        
    @$pdf = new FPDF('P','mm');
    $pdf->SetFont('courier');

    DataBaseClass::FromTable('Command');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Where_current("ID=$ID");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::OrderClear("Command","vName");

    $commands=DataBaseClass::QueryGenerate();
      
    DataBaseClass::Query("select  E.vRound, C.Name Competition, D.Name Discipline, C.WCA Competition_WCA, D.Code Discipline_Code, E.Groups from `Discipline` D "
    . " join `DisciplineFormat` DF on DF.Discipline = D.ID "
    . " join `Event` E on E.DisciplineFormat = DF.ID "
    ." join Competition C on C.ID=E.Competition where E.ID='$ID'");
    $data=DataBaseClass::getRow();
        
    $max_page=30;    
    $pages=ceil(sizeof($commands)/$max_page);
    
for($p=0;$p<$pages;$p++){  
    $start = $p * $max_page;
    $end = min (array(($p+1) * $max_page,sizeof($commands)));
    $on_page=($end-$start+1);
    $pdf->AddPage();    

    $n=0;
    for($c=$start;$c<$end;$c++){
        $command=$commands[$c];
        $n++;

        if($c%2 ==0){
            $pdf->SetFillColor(240,240,240);
            $pdf->Rect(5, 38+($n-1)*8, $pdf->w - 10, 8, "F");
        }
        $pdf->SetFont('Arial','',12);
        $g=$command['Command_Group'];
        if($g!=-1){
            $pdf->Text(109+20*$g, 43+($n-1)*8, Group_Name($g));
        }
        $pdf->SetLineWidth(0.3);
        if($n>0){
            $pdf->Line(5, 38+($n-1)*8 , $pdf->w - 5, 38+($n-1)*8);
        }
        $pdf->Line(5, 38+$n*8 , $pdf->w - 5, 38+$n*8);
    
        $pdf->SetFont('Arial','B',12);
        $pdf->Text(7, 35+$n*8, $n);
    
        $pdf->SetFont('Arial','',10);
        $names=explode(",",$command['Command_vName']);
        foreach($names as $i=>$name){
            $names[$i]= Short_Name($name);
        }
        $pdf->Text(18, 35+$n*8, iconv('utf-8', 'cp1252//TRANSLIT',implode(", ",$names))); 
    }
    
    if(file_exists("Image/Discipline/".$data['Discipline_Code'].'.jpg')){
        $pdf->Image("Image/Discipline/".$data['Discipline_Code'].'.jpg',5,5,20,20,'jpg');
    }
   
        $pdf->Image("Image/FC_B.png",$pdf->w-25,5,20,20,'png');

    
    $pdf->SetFont('Arial','',16);
    $str=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
    $pdf->Text(30, 23, $str);
    $pdf->SetFont('msserif','',20);
    $pdf->Text(30, 13, iconv('utf-8', 'windows-1251',$data['Discipline'].$data['vRound']));
    $pdf->SetLineWidth(0.3);
    $pdf->Line(5, 38 , $pdf->w - 5, 38);
    
    $pdf->SetLineWidth(0.1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Line(15, 30, 15,32+8*$on_page);
    
    $pdf->Line(100, 30, 100,32+8*$on_page);
    

    $pdf->SetFont('Arial','',10);
    $pdf->Text(45, 35, 'Name');    
    for($g=0;$g<$data['Groups'];$g++){
        $pdf->Text(104+20*$g, 35, 'Group '.Group_Name($g));
        $pdf->Line(100+20*($g+1), 30, 100+20*($g+1),32+8*$on_page);
    }
    
    
        $pdf->SetFont('Arial','',10);
        $pdf->Text(80, 286,GetIni('TEXT','print'));
}        
$pdf->Output();              
$pdf->Close();
exit();

}else{
     echo 'Not found';
}