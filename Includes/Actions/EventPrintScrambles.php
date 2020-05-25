<?php
$Letter=array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"H",9=>"I");

$Y_Content_S=33;
$Y_Content_E=275;
$scramble_font='courier';  


$y_att=20;
$dy=33;
$dyy=3;

        
$X_IMG_0=140;

$X_IMG_1=205;

if((isset($_GET['ID']) and is_numeric($_GET['ID'])) or (isset($Get_ID) and is_numeric($Get_ID))){
    if(isset($_GET['ID'])){
        $ID=$_GET['ID'];
        $massPrint=false;
    }else{
        $ID=$Get_ID;
        $massPrint=true;
    }

    
    DataBaseClass::FromTable('Event', "ID=$ID");
    DataBaseClass::Join_current('Scramble');
    DataBaseClass::Join('Event','DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format');
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::OrderClear('Scramble', 'Group');
    DataBaseClass::Order('Scramble','Attempt');
    $data=DataBaseClass::QueryGenerate();
        
    if($data[0]['Event_Competition']!=129){
        CheckingRoleDelegateEvent($ID,false);
    }

$scramble_max=0;
foreach($data as $row){
    $scramble_max=max($scramble_max,strlen($row['Scramble_Scramble']));
}

@$pdf = new FPDF('P','mm');
$pdf->SetFont('courier');
$pdf->SetLineWidth(0.3);
$group=0;    
$n=0;

if(isset($_GET['Name'])){
    $Competition_name=$_GET['Name'];
}else{
    $Competition_name=$data[0]['Competition_Name'];
}

$Competition_name=iconv('utf-8', 'cp1252//TRANSLIT', $Competition_name);

foreach($data as $row){ 
    
    $n++;
    if($group<>$row['Scramble_Group']){
        $pdf->AddPage();    
        $group=$row['Scramble_Group'];
        $n=0;
            
        //Instructions
        if($row['Discipline_Code']=='Ivy'){
            $pdf->SetFont('Arial','',10);
            $pdf->Text(120, 13, "Up-White, Right-Green, Left-Orange");
            $pdf->Text(120, 18, "U - WBR, R - GRY, L - OYB, F - WGO");
        }
        
        $pdf->SetFont('Arial','',10);
        if($row['Discipline_Code']=='Mirror'){
            $pdf->Text(110, 13, "UF the thickest");
            $pdf->Text(110, 18, "FD more thinly than UB");
        }
        
        if($row['Discipline_Code']=='Redi'){
            $pdf->Text(100, 10, 'R - Right Front Up corner');
            $pdf->Text(100, 15, 'L - Left Front Up corner');
        }
        
        if($row['Discipline_Code']=='Dino'){
            $pdf->Text(100, 10, 'R - Right Front Up corner');
            $pdf->Text(100, 15, 'L - Left Front Up corner');
        }
        
        if($row['Discipline_Code']=='Pyraminx2x2x2'){
            $pdf->Text(100, 10, 'Yellow side to the top, Green - on themselves');
            $pdf->Text(100, 15, 'Yellow-Green-Red corner remains in place');
            $pdf->Text(100, 20, 'R - rotation line near to Green sticker');
            $pdf->Text(100, 25, 'U - rotation line near to Yellow sticker');
            $pdf->Text(100, 30, 'B - rotation line near to Red sticker');
        }
        
        //Header
        if(file_exists("Image/Discipline/".$row['Discipline_Code'].'.jpg')){
            $pdf->Image("Image/Discipline/".$row['Discipline_Code'].'.jpg',5,10,20,20,'jpg');
        }
        $pdf->Image("Image/FC.png",$pdf->w-25,10,20,20,'png');
        $pdf->SetFont('Arial','',16);
        $pdf->SetTextColor(162,0,0);
        $pdf->Text(30, 13, $row['Discipline_Name']);
        $pdf->SetTextColor(0,182,67);
        $pdf->Text(30, 20, 'Group '.$Letter[$group]);
        $pdf->SetTextColor(17,31,135);
        $pdf->Text(30, 27,$Competition_name);
        
        //Footer
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->Text(80, 286,GetIni('TEXT','print'));
        $pdf->Text(150, 286,$row['Scramble_Timestamp']);
        
        $Y=$Y_Content_S;
    }
        
    
 //   $pdf->SetDrawColor(255,0,0);
   
    
    $pdf->Line(10,$Y,$X_IMG_1,$Y);
 //   $pdf->SetDrawColor(0,0,0);

    $y0=43;
    //$pdf->Line(10,$y0,205,$y0);
    
    if($n==$row['Format_Attemption']){
        //$Y=$y0+$n*$dy-8;/////
        $pdf->SetFont('Arial','',12);
        $pdf->SetFillColor(230,230,230);
        $pdf->Rect(17, $Y , $X_IMG_1-17, 6,'DF');
        $pdf->Text(90, $Y+4, 'Extra scrambles');
        $Y+=6;
        //$pdf->Line(10,$y0+$n*$dy,205,$y0+$n*$dy);
    }
    
    
    if($n>=$row['Format_Attemption']){
        $y0=60;    
    }
    
    $pdf->SetFont('times','B',24);
    
    
    if($row['Discipline_ID']==49){
        $pdf->Text(5, $y0+$n*$dy+4, $Letter[$group]);    
    }
        
    $texts=array();
    if(strpos($row['Scramble_Scramble'],"&")===false){

        $scramble_len=strlen($row['Scramble_Scramble']);
        if($scramble_max>44*3){         $scramble_row=3; $scramble_size=12; 
        }elseif($scramble_max>38*3){    $scramble_row=3; $scramble_size=12; 
        }elseif($scramble_max>102){     $scramble_row=3; $scramble_size=16; 
        }elseif($scramble_max>90){      $scramble_row=3; $scramble_size=18; 
        }elseif($scramble_max>68){      $scramble_row=3; $scramble_size=20; 
        }elseif($scramble_max>60){      $scramble_row=2; $scramble_size=18; 
        }elseif($scramble_max>34){      $scramble_row=2; $scramble_size=18; 
        }elseif($scramble_max>20){      $scramble_row=1; $scramble_size=18; 
        }else{                          $scramble_row=1; $scramble_size=20; }

        if($scramble_len<10)$scramble_row=1;
        
        
        if($scramble_row==3){
            //$Y=$y0+$n*$dy-4;////
            $d=8;
            
            $r1=ceil($scramble_len/3);
            $r2=ceil($scramble_len/3*2);
            while(substr($row['Scramble_Scramble'],$r1,1)!=" "){$r1--;}
            while(substr($row['Scramble_Scramble'],$r2,1)!=' '){$r2--;}
            $texts[]=trim(substr($row['Scramble_Scramble'],0,$r1));
            $texts[]=trim(substr($row['Scramble_Scramble'],$r1+1,$r2-$r1));
            $texts[]=trim(substr($row['Scramble_Scramble'],$r2));
            //$pdf->SetFont($scramble_font,'',$scramble_size);
            //$pdf->Text(20, $Y     ,$text1);
            //$pdf->Text(20, $Y+$d  ,$text2);
            //$pdf->Text(20, $Y+$d*2,$text3);

        }elseif($scramble_row==2){
            //$Y=$y0+$n*$dy;////
            $d=10;
            
            $r1=ceil($scramble_len/2);
            while(substr($row['Scramble_Scramble'],$r1,1)!=" "){$r1--;}
            $texts[]=trim(substr($row['Scramble_Scramble'],0,$r1));
            $texts[]=trim(substr($row['Scramble_Scramble'],$r1));
            //$pdf->SetFont($scramble_font,'',$scramble_size);
            //$pdf->Text(20, $Y   ,$text1);
            //$pdf->Text(20, $Y+$d,$text2);
        }else{
            //$Y=$y0+$n*$dy+4;////
            
            $texts[]=trim($row['Scramble_Scramble']);
            //$pdf->SetFont($scramble_font,'',$scramble_size);
            //$pdf->Text(20, $Y, $text1);
        }

    }else{
        $texts=explode(" & ",$row['Scramble_Scramble']);
        $scramble_max=0;
        foreach($texts as $text){
            $scramble_max=max(array($scramble_max,strlen($text)));
        }
            if($scramble_max>=50){        $scramble_size=10; 
            }elseif($scramble_max>42){    $scramble_size=12; 
            }elseif($scramble_max>38){    $scramble_size=12; 
            }elseif($scramble_max>33){    $scramble_size=16; 
            }elseif($scramble_max>30){    $scramble_size=18; 
            }else{ $scramble_size=20;} 
    }
    
    $scramble_row=sizeof($texts);
    $pdf->SetFont($scramble_font,'',$scramble_size);
 
    $D_Att=($scramble_row)*$scramble_size*0.3+20;
    if($D_Att<33)$D_Att=33;

        $t=0;
        if(sizeof($texts)==1){
            $t=-10;
        }
        if(sizeof($texts)==2){
            $t=-2;
        }
        if(sizeof($texts)>3){
            $t=1;
        }
        foreach($texts as $r=>$text){
            if($r%2!=0){
                $pdf->SetFillColor(230,230,230);
                $pdf->Rect(17, $Y+$D_Att/$scramble_row*($r+1)-$scramble_size/2-2+$t , $X_IMG_0-10, $scramble_size/2,'F');
            }
            $pdf->Text(20, $Y+$D_Att/$scramble_row*($r+1) -$scramble_size*.3 +$t,$text);  

        }
        
    $pdf->SetFont('times','B',16);    
    if($row['Discipline_CutScrambles']){
        if($n+1>$row['Format_Attemption']){
            $pdf->Text(6, $Y+$D_Att/2, $Letter[$group]."E".($n+1-$row['Format_Attemption']));    
        }else{    
            $pdf->Text(8, $Y+$D_Att/2, $Letter[$group]."".($n+1));
        }
    }else{
        if($n+1>$row['Format_Attemption']){
            $pdf->Text(10, $Y+$D_Att/2, "E".($n+1-$row['Format_Attemption']));    
        }else{    
            $pdf->Text(10, $Y+$D_Att/2, $n+1);
        }    
    }
    
    $filename="Image/Scramble/".$row['Scramble_ID'].".png";
    
    $size=getimagesize($filename);
    $max_width=$X_IMG_1-$X_IMG_0;
    $max_height=$D_Att-1;
    $k=min($max_width/$size[0],$max_height/$size[1]);
    //$pdf->SetLineWidth(0.2);
    
    $img_dx=($max_width-$k*$size[0])/2;
    $img_dy=($D_Att-$k*$size[1])/2;
    
    $pdf->Image($filename, $X_IMG_0+$img_dx, $Y+$img_dy, $k*$size[0], $k*$size[1]);
    $Y+=$D_Att;
    
    $pdf->Rect(17,$Y_Content_S,$X_IMG_1-17,$Y-$Y_Content_S);
    
}

    if(!$massPrint){
        $pdf->Output($Competition_name.'_Scrambles_'.$data[0]['Discipline_Code'].".pdf",'I');              
        $pdf->Close();
        exit();
    }else{
        $file = "Image/Scramble/Mass_".md5($Competition_name.'_Scrambles_'.$data[0]['Discipline_Code']).".pdf";
        $pdf->Output($file,'F');              
        $pdf->Close();
    }
}else{
     echo 'Not found';
}
