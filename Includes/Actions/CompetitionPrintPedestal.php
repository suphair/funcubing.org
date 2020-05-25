<?php
if(isset($_GET['ID']) and is_numeric(($_GET['ID'])) ){
    $ID=$_GET['ID'];
    
    @$pdf = new FPDF('P','mm');
    $pdf->SetFont('courier');
    
    DataBaseClass::FromTable("Competition");
    DataBaseClass::Where_current("ID=$ID");
    $competition=DataBaseClass::QueryGenerate(false);
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current("Discipline");
    DataBaseClass::Join("DisciplineFormat","Format");
    DataBaseClass::Select("D.Name,E.ID,F.Result,D.Code");
    DataBaseClass::OrderClear("Event", "LocalID");
    DataBaseClass::Order("Discipline", "Name");
    $disciplines=DataBaseClass::QueryGenerate();
   
    
    $pdf->AddPage();
    
    
    $pdf->Image("Logo/Full_En_Black.png",5,5,20/317*1383,20,'png');

    
    $pdf->SetFont('Arial','',18);
    $text = $str=iconv('utf-8', 'cp1252//TRANSLIT', $competition['Competition_Name']). ' ('. date("d.m.Y H:i",time()).')';
    $pdf->Text(30, 34,$text);
$Y_header=40;
    
    $DY=min(($pdf->h-$Y_header-15)/sizeof($disciplines),30);
    $X_left=5+$DY;
     
    foreach($disciplines as $n=>$discipline){
        $sy=$DY*$n+$Y_header;
        $ey=$DY*($n+1)+$Y_header;
        $pdf->line(5,$sy,$pdf->w-5,$sy);
        
        
        DataBaseClass::FromTable("Event");
        DataBaseClass::Join_current("Command");
        DataBaseClass::Where_current("Place between 1 and 3");
        //DataBaseClass::Join_current("Competitor");
        DataBaseClass::Where("Event","ID=".$discipline['ID']);
        DataBaseClass::Select("Com.Place,A.vOut,Com.vName");
        DataBaseClass::OrderClear("Command","Place");
        DataBaseClass::Join("Command","Attempt");
        DataBaseClass::Where("Attempt","Special='".$discipline['Result']."'");
        DataBaseClass::Where("Attempt", "IsDNF=0");
        $competitors=DataBaseClass::QueryGenerate();
       
              
        if(file_exists("Image/Discipline/".$discipline['Code'].'.jpg')){
            $pdf->Image("Image/Discipline/".$discipline['Code'].'.jpg',5,$sy+5,$DY-10,$DY-10,'jpg');
        }  
                
        if(sizeof($competitors)){
            $place_y=$DY/max(array(sizeof($competitors),3))*0.9;    
            
//            $pdf->SetFont('msserif','',min(array($place_y*2,10)));
//            $text = iconv('utf-8', 'windows-1251',$discipline['Name']);
//            $pdf->Text($X_left, $sy+$place_y*1,$text);   
            
            foreach($competitors as $n=>$competitor){
                $pdf->SetFont('Arial','B',min(array($place_y*2,14)));
                $pdf->Text($X_left, $sy+$place_y*($n+1),$competitor['Place']);   
                
                
                $pdf->SetFont('Arial','',min(array($place_y*2,14)));
                $pdf->Text($X_left+5, $sy+$place_y*($n+1),sprintf("%10s",$competitor['vOut']));   
                $pdf->Text($X_left+35, $sy+$place_y*($n+1),$competitor['vName']);   
            }
        }
    }
    $sy=$DY*sizeof($disciplines)+$Y_header;
    $ey=$DY*sizeof($disciplines)+$Y_header;
    $pdf->line(5,$sy,$pdf->w-5,$sy);
    $pdf->SetFont('Arial','',18);
    $pdf->Text(60, 290,GetIni('TEXT','print'));
    $pdf->Output($competition['Competition_WCA'].'_'.'Pedestal'.".pdf",'I');              
    $pdf->Close();
    exit();
    
}else{
     echo 'Not found';
}