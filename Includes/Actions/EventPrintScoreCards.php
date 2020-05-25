<?php
if(isset($_GET['ID']) and is_numeric(($_GET['ID'])) ){
    $ID=$_GET['ID'];
            
    DataBaseClass::Query("Select E.Cumulative, D.ID Discipline_ID, E.vRound, C.Name Competition,C.ID Competition_ID, D.Name Discipline ,C.WCA Competition_WCA, D.Code Discipline_Code, F.Attemption, F.Result, E.CutoffSecond, E.CutoffMinute, E.LimitSecond, E.LimitMinute, E.LocalID, D.Competitors "
            . " from `Event` E "
            . " join `DisciplineFormat` DF on E.DisciplineFormat= DF.ID "
            . " join `Discipline` D on D.ID=DF.Discipline "
            . " join `Format` F on F.ID= DF.Format "
            . " join `Competition` C on C.ID=E.Competition where E.ID='$ID'");
    $data=DataBaseClass::getRow();
    
    $competition=$data['Competition'];
    
    if($data['Competition_ID']!=129){
        CheckingRoleDelegateEvent($ID,false);
    }else{
        $competition='Example Scorecards';
    }
    
    
    
    DataBaseClass::Query("Select Com.vName ,Com.CardID,Com.Group "
            . " from `Event` E join `Command` Com on Com.Event=E.ID "
            . " where E.ID='$ID' order by Com.Group, Com.vName");

    $commands=DataBaseClass::getRows();
    
    if($data['Attemption'] + $data['Competitors']>7){
        include 'EventPrintScoreCardsA3.php';
        exit();
    }
    
    @$pdf = new FPDF('P','mm');

 
    
$points=array();
$points[]=array(5,5);
$points[]=array($pdf->w /2 + 5,5);
$points[]=array(5, $pdf->h /2 + 5);
$points[]=array($pdf->w /2 + 5, $pdf->h /2 + 5);
$sizeX= $pdf->w /2 -10;
$sizeY= $pdf->h /2 -10;
    
$competitors_group=array();
foreach($commands as $command){
    $command_group[$command['Group']][]=$command;
}

$command_group[-2][]=array('vName'=>'','CardID'=>'','Group'=>-1);


foreach($command_group as $group=>$commands){
    $list=ceil(sizeof($commands)/4);
    for($l=0;$l<$list;$l++){
        $pdf->AddPage();
        $pdf->SetLineWidth(0.5);
        $pdf->Line(5, $pdf->h /2 , $pdf->w - 5, $pdf->h /2);
        $pdf->Line($pdf->w /2 ,5, $pdf->w /2, $pdf->h - 5);   
        for($i=0;$i<4;$i++){
            $point=$points[$i];
 
            // if(file_exists("Image/Competition/".$data['Competition_WCA'].'.jpg')){
            //    $pdf->Image("Image/Competition/".$data['Competition_WCA'].'.jpg',$point[0],$point[1]+1,10,10,'jpg');
            // }
            if(file_exists("Image/Discipline/".$data['Discipline_Code'].'.jpg')){
                $pdf->Image("Image/Discipline/".$data['Discipline_Code'].'.jpg',$point[0],$point[1]+1,10,10,'jpg');
            }

            
            $pdf->Image("Image/FC_B.png",$point[0]+$pdf->w /2-20,$point[1]+1,10,10,'png');

            if(isset($commands[$i+$l*4])){
                $command=$commands[$i+$l*4];
            }else{
                $command=array('vName'=>'','CardID'=>'','Group'=>-1);
            }
            //$pdf->SetFont('Arial','',10);
            //$pdf->Text($point[0] + 25, $point[1] + 140,GetIni('TEXT','print'));

            $pdf->SetFont('Arial','',10);
            $pdf->SetLineWidth(0.2);
            
            $str=iconv('utf-8', 'cp1252//TRANSLIT', $competition);
            $pdf->Text($point[0] + 14, $point[1] + 10, $str); 
            $pdf->Text($point[0] + 14, $point[1] + 15, str_replace(": ","",$data['vRound'])); 
            $pdf->Text($point[0] + 14, $point[1] + 5,$data['Discipline']);
            

            $Ry=20;
            $names= explode(",", $command['vName']);      
            for($c=1;$c<=$data['Competitors'];$c++){
                if(!$command['CardID']){
                    $pdf->Rect($point[0] + 5, $point[1] + $Ry-3 ,30,10);    
                    $pdf->Rect($point[0] + 37, $point[1] + $Ry-3,55,10);
                    if($c==1){
                        $pdf->SetFont('Arial','',10);
                        $pdf->Text($point[0] + 13, $point[1] + $Ry-4, "WCA ID");
                        $pdf->Text($point[0] + 50, $point[1] + $Ry-4, "Competitor Name");
                    }
                    if($data['Competitors']>1){
                        $pdf->SetFont('Arial','',14);
                        $pdf->Text($point[0], $point[1] + $Ry+4 , $c);
                    }
                }else{
                    if($c==1){
                        $pdf->Rect($point[0] + 5, $point[1] + $Ry-3 ,15,10);    
                        $pdf->SetFont('Arial','',18);
                        $pdf->Text($point[0] + 7, $point[1] + $Ry+4, $command['CardID']);
                    }
                    $pdf->Rect($point[0] + 22, $point[1] + $Ry-3,70,10);

                    if(isset( $names[$c-1])){
                        $pdf->SetFont('Arial','',14); 
                        $pdf->Text($point[0] + 23, $point[1] + $Ry+4, iconv('utf-8', 'cp1252//TRANSLIT',Short_Name($names[$c-1])));
                    }
                }
                $Ry+=12;
            }

            if($command['Group']!=-1){
                $pdf->SetFont('Arial','',10);   
                $pdf->Text($point[0] + 83, $point[1] + 16,  'Group');
                $pdf->SetFont('Arial','',14);    
                $pdf->Rect($point[0] + 82, $point[1] + 17,10,10);
                $pdf->Text($point[0] + 85, $point[1] + 23, Group_Name($command['Group']));
            }

            $array_not_scr=array('Assembly','SportStacks');
            
            $pdf->SetFont('Arial','',10);
            if(!in_array($data['Discipline_Code'],$array_not_scr) and strpos($data['Discipline_Code'],'Scrambling')===FALSE){
                $pdf->Text($point[0] + 10, $point[1] + $Ry+1,'Scr');
                $pdf->Text($point[0] + 36, $point[1] + $Ry+1, 'Result');
            }else{
                $pdf->Text($point[0] + 30, $point[1] + $Ry+1, 'Result');
            }
            $pdf->Text($point[0] + 67, $point[1] + $Ry+1, 'Judge');
            if($data['Competitors']>1){
                $pdf->Text($point[0] + 82, $point[1] + $Ry+1, 'Comps');
            }else{
                $pdf->Text($point[0] + 83, $point[1] + $Ry+1, 'Comp');
            }

            for($k=1;$k<=$data['Attemption'];$k++){
                $pdf->SetFont('Arial','',14);
                $pdf->Text($point[0], $point[1] + $Ry+10 + ($k-1)*17, $k);
                if(!in_array($data['Discipline_Code'],$array_not_scr) and strpos($data['Discipline_Code'],'Scrambling')===FALSE){
                    $pdf->Rect($point[0] + 5, $point[1] + $Ry+2 + ($k-1)*17 ,15,13);
                    $pdf->Rect($point[0] + 21, $point[1] + $Ry+2 + ($k-1)*17 ,42,13);
                }else{
                    $pdf->Rect($point[0] + 5, $point[1] + $Ry+2 + ($k-1)*17 ,58,13);
                }
                $pdf->Rect($point[0] + 64, $point[1] + $Ry+2  + ($k-1)*17,15,13);
                
                $pdf->SetLineWidth(0.1);
                
                if($data['Competitors']==2){
                    $pdf->Line($point[0] + 80  + 15,$point[1] + $Ry+2  + ($k-1)*17, $point[0] + 80 ,$point[1] + $Ry+2  + ($k-1)*17 +13);   
                }
                
                if($data['Competitors']==3){
                    $pdf->Line($point[0] + 80  + 15,$point[1] + $Ry+2  + ($k-1)*17, $point[0] + 80 +7.5,$point[1] + $Ry+2  + ($k-1)*17 +6.5);   
                    $pdf->Line($point[0] + 80,$point[1] + $Ry+2  + ($k-1)*17, $point[0] + 80 +7.5,$point[1] + $Ry+2  + ($k-1)*17 +6.5);   
                    $pdf->Line($point[0] + 80  +7.5,$point[1] + $Ry+2  + ($k-1)*17 +13, $point[0] + 80 +7.5,$point[1] + $Ry+2  + ($k-1)*17 +6.5);   
                }
                
                if($data['Competitors']==4){
                    $pdf->Line($point[0] + 80  +7.5,$point[1] + $Ry+2  + ($k-1)*17 +13, $point[0] + 80 +7.5,$point[1] + $Ry+2  + ($k-1)*17);   
                    $pdf->Line($point[0] + 80  + 15,$point[1] + $Ry+2  + ($k-1)*17 +6.5, $point[0] + 80 ,$point[1] + $Ry+2  + ($k-1)*17 +6.5);   
                }
                
                $pdf->SetLineWidth(0.2);
                $pdf->Rect($point[0] + 80, $point[1] + $Ry+2  + ($k-1)*17,15,13);
            }
            $pdf->SetFont('Arial','',10);
            if($data['Attemption']==5)$cutoffN=2;
            if($data['Attemption']==3)$cutoffN=1;
            if($data['Attemption']==2)$cutoffN=1;
            if(($data['CutoffMinute'] or $data['CutoffSecond']) and isset($cutoffN)){
                $pdf->Text($point[0]+8 , $point[1] + $Ry+2 + $cutoffN*17-1,($data['CutoffMinute'] or $data['CutoffSecond'])?"Cutoff ".$data['CutoffMinute'].":".sprintf("%02d",$data['CutoffSecond'])."  ----------------------------":"");
            }
            
            $pdf->Text($point[0]+8 , $point[1] + $Ry+2 + $data['Attemption']*17-1,($data['Cumulative']?"Cumulative limit ":"Limit ").$data['LimitMinute'].":".sprintf("%02d",$data['LimitSecond']));


            if($data['Attemption']+$data['Competitors']<7){    
                $pdf->SetFont('Arial','',14);
                $pdf->Text($point[0]-1, $point[1] + 40 + 5*17+5, "Ex");
                $pdf->Rect($point[0] + 5, $point[1] + 32 + 5*17 +5 ,15,13);
                $pdf->Rect($point[0] + 21, $point[1] + 32 + 5*17+5 ,42,13);
                $pdf->Rect($point[0] + 64, $point[1] + 32 + 5*17+5,15,13);
                $pdf->Rect($point[0] + 80, $point[1] + 32 + 5*17+5,15,13);
            }else{
                $pdf->SetFont('Arial','',14);
                $pdf->Text($point[0]-1, $point[1] + 40 + 5*17+10, "Ex");
                $pdf->Rect($point[0] + 5, $point[1] + 32 + 5*17 +14 ,15,10);
                $pdf->Rect($point[0] + 21, $point[1] + 32 + 5*17+14 ,42,10);
                $pdf->Rect($point[0] + 64, $point[1] + 32 + 5*17+14,15,10);
                $pdf->Rect($point[0] + 80, $point[1] + 32 + 5*17+14,15,10);
            }
        }



    }
}

    $pdf->Output($data['Competition_WCA'].'_ScoreCards_'.$data['Discipline_Code'].".pdf",'I');              
    $pdf->Close();
    
    
}else{
    echo 'Not found';
}

