<?php @$pdf = new FPDF('L','mm');

    
$points=array();
$points[]=array(5,5);
$points[]=array($pdf->w /2 + 5,5);
$sizeX= $pdf->w /2 -10;
$sizeY= $pdf->h -10;
    
$commands_group=array();
foreach($commands as $command){
    $commands_group[$command['Group']][]=$command;
}

$commands_group[-2][]=array('vName'=>'','CardID'=>'','Group'=>-1);


foreach($commands_group as $group=>$commands){
    $list=ceil(sizeof($commands)/2);
    for($l=0;$l<$list;$l++){
        $pdf->AddPage();
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pdf->w /2 ,5, $pdf->w /2, $pdf->h - 5);   
        for($i=0;$i<2;$i++){
            $point=$points[$i];
 
            if(file_exists("Image/Discipline/".$data['Discipline_Code'].'.jpg')){
                $pdf->Image("Image/Discipline/".$data['Discipline_Code'].'.jpg',$point[0],$point[1]+1,10,10,'jpg');
            }

            
            $pdf->Image("Image/FC_B.png",$point[0]+$pdf->w /2-20,$point[1]+1,10,10,'png');

            if(isset($commands[$i+$l*2])){
                $command=$commands[$i+$l*2];
            }else{
                $command=array('vName'=>'','CardID'=>'','Group'=>-1);
            }
            //$pdf->SetFont('Arial','',10);
            //$pdf->Text($point[0] + 25, $point[1] + 140,GetIni('TEXT','print'));

            $pdf->SetFont('Arial','',10);
            $pdf->SetLineWidth(0.2);
            $str=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition']);
            $pdf->Text($point[0] + 14, $point[1] + 10,$str); 
            
            $pdf->SetFont('msserif','',14);
            $pdf->Text($point[0] + 14, $point[1] + 5,iconv('utf-8', 'windows-1251',$data['Discipline']));
            
            $Ry=20;
            
            $names= explode(",", $command['vName']);            
            for($c=1;$c<=$data['Competitors'];$c++){
                if($c==1){
                    $pdf->Rect($point[0] + 15, $point[1] + $Ry-3 ,15,10);    
                    $pdf->SetFont('Arial','',18);
                    $pdf->Text($point[0] + 17, $point[1] + $Ry+4, $command['CardID']);
                }
                $pdf->Rect($point[0] + 32, $point[1] + $Ry-3,70,10);
                
                if(isset( $names[$c-1])){
                    $pdf->SetFont('Arial','',14);
                    $pdf->Text($point[0] + 33, $point[1] + $Ry+4, iconv('utf-8', 'cp1252//TRANSLIT', Short_Name($names[$c-1])));
                }
                $Ry+=12;
            }

            if($command['Group']!=-1){
                $pdf->SetFont('Arial','',10);    
                $pdf->Text($point[0] + 93, $point[1] + 16,  'Group');
                $pdf->Rect($point[0] + 92, $point[1] + 17,10,10);
                $pdf->Text($point[0] + 95, $point[1] + 23, Group_Name($command['Group']));
            }
              $pdf->Text($point[0] + 105 , $point[1] + 23,($data['LimitMinute']!=10)?"Limit ".$data['LimitMinute'].":".sprintf("%02d",$data['LimitSecond']):"");

            $pdf->SetFont('Arial','',10); 
            $pdf->Text($point[0] + 39, $point[1] + $Ry+1, 'Result');
            $pdf->Text($point[0] + 74, $point[1] + $Ry+1, 'Judge');
            $pdf->Text($point[0] + 90, $point[1] + $Ry+1, 'Comp');

            for($k=1;$k<=$data['Attemption'];$k++){
                $pdf->SetFont('Arial','',14);
                if($image=IconAttempt($data['Discipline_Code'],$k)){
                    $pdf->Image($image,$point[0], $point[1] + $Ry+10 + ($k-1)*13-7,10,10);
                    $pdf->Text($point[0]+105, $point[1] + $Ry+10 + ($k-1)*13-1, IconAttempt_DisciplineName($image,$data['Discipline_Code'],$k));
                }else{
                    $pdf->Text($point[0]+2, $point[1] + $Ry+10 + ($k-1)*13-1, $k);
                }
                
                $pdf->Rect($point[0] + 15, $point[1] + $Ry+2 + ($k-1)*13 ,55,11);
                $pdf->Rect($point[0] + 71, $point[1] + $Ry+2  + ($k-1)*13,15,11);
                $pdf->Rect($point[0] + 87, $point[1] + $Ry+2  + ($k-1)*13,15,11);
            }


            $pdf->SetFont('Arial','',14);
            $pdf->Text($point[0]+2, $point[1] + 40 + $data['Attemption']*13+4, "Ex");
            $pdf->Rect($point[0] + 15, $point[1] + 32 + $data['Attemption']*13+5 ,55,11);
            $pdf->Rect($point[0] + 71, $point[1] + 32 + $data['Attemption']*13+5,15,11);
            $pdf->Rect($point[0] + 87, $point[1] + 32 + $data['Attemption']*13+5,15,11);
            $pdf->Text($point[0]+105, $point[1] + 32 + $data['Attemption']*13+15,'__________');
        }



    }
}
    $pdf->Output($data['Competition_WCA'].'_ScoreCards_'.$data['Discipline_Code'].".pdf",'I');              
    $pdf->Close();

