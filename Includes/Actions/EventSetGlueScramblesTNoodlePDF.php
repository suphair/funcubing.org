<?php
$Scrambles_row=array();
if($_FILES['file']['error']==0 and $_FILES['file']['type'] == 'application/pdf'){ 
    
    
    CheckPostIsset('ID');
    CheckPostIsNumeric('ID');
    CheckPostNotEmpty('ID');
    $ID=$_POST['ID'];

    CheckingRoleDelegateEvent($ID);
    
    
    //DeleteFiles('Scramble/Hard');
    
    $pdf_file = $_FILES['file']['tmp_name'];
    
    $im = new imagick();
    $im->readimage($_FILES['file']['tmp_name']); 
    $Pages=$im->getnumberimages();
    $rand= random_string(10);
    
    $lines=[];
    
    for($i=0;$i<$Pages;$i++){
        $im = new imagick();
        $im->setResolution(300,300);
        $im->readimage($pdf_file."[$i]"); 
        $im->setImageFormat('jpeg');    
        $jpg_file =  "Scramble/Hard/{$rand}_{$i}.jpg" ;
        $im->writeImage($jpg_file); 
        $im->clear(); 
        $im->destroy();
        
        $img_lines= imagecreatefromjpeg("Scramble/Hard/{$rand}_{$i}.jpg");
        
        $B=0;
        for($y=250;$y<3050;$y++){
            if(in_array(imagecolorat($img_lines, 250, $y),[0,65793]) 
               and in_array(imagecolorat($img_lines, 250, $y+1),[0,65793]) 
               and in_array(imagecolorat($img_lines, 310, $y),[0,65793]) 
               and in_array(imagecolorat($img_lines, 310, $y+1),[0,65793]) 
              ){
                if(isset($lines[$i][$B][0]) and $y-$lines[$i][$B][0]<100){
                    $lines[$i][$B][0]=$y;
                }else{                
                    $lines[$i][$B+1][0]=$y;
                    if($B>0){
                        $lines[$i][$B][1]=$y+2;
                    }
                    $B++;
                }
                $y+=10;
            }
        }
        unset($lines[$i][$B]);
    }
    
    
    Databaseclass::FromTable('Event', "ID='$ID'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format');
    Databaseclass::Join('Event','Competition');
    $data=Databaseclass::QueryGenerate(false);
    $Discipline=$data['Discipline_Code'];

    $attemption=$data['Format_Attemption'];
    
    $X0=285;
    $X1=2353;
    
    $X=$X1-$X0;
    

    @$pdf = new FPDF('P','mm');
    
    $pdf_img_Y0=35;
    $pdf_img_X0=20;
    $pdf_img_X=$pdf->w-$pdf_img_X0;
    $pdf_img_Y=$pdf_img_Y0;
    
    $Letter=array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"H",9=>"I");
    
    $pdf->SetFont('courier');   
    $Groups=$data['Event_Groups'];
    $Attemption=$data['Format_Attemption'];
    if($Discipline=='Scrambling'){
        $Attemption=1;    
    }
    
    
    $ScamblesOnePage=sizeof($lines[0]);
    //$PagesEvent=ceil($ScramblesEvent/$ScamblesOnePage);
    //$ScramblesEachAttempts=$Pages/$PagesEvent;
    
    //echo "Pages=$Pages Groups=$Groups A=".($Attemption+1)." ScramblesEvent=$ScramblesEvent PagesEvent=".$PagesEvent." ScramblesEachAttempts=$ScramblesEachAttempts";
    //echo '<hr>';
    
    $PageAdd=0;
    for($group=0;$group<$Groups;$group++){
        
        $pdf->AddPage();

        $pdf->Image("Image/Discipline/".$Discipline.'.jpg',5,10,20,20,'jpg');
        $pdf->Image("Image/FC.png",$pdf->w-25,10,20,20,'png');

        $pdf->SetFont('Arial','',16);
        $pdf->SetTextColor(17,31,135);
        $Competition_name=iconv('utf-8', 'cp1252//TRANSLIT', $data['Competition_Name']);
        $pdf->Text(30, 13, $Competition_name);
        $pdf->SetFont('msserif','',16);
        $pdf->SetTextColor(162,0,0);
        $pdf->Text(30, 20, $data['Discipline_Name']);
        $pdf->SetFont('Arial','',16);

        $pdf->SetTextColor(0,182,67);
        $pdf->Text(30, 27, $Letter[$group+1].' group');
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Text(80, 286,GetIni('TEXT','print'));
        $pdf->SetFont('Arial','',10);
        $pdf->Text(150, 286,date("d.m.Y"));

        $pdf_img_Y=$pdf_img_Y0;
        for($attempt=1;$attempt<=$ScamblesOnePage;$attempt++){
                
            $StartLine=$lines[$group][$attempt][0];
            $EndLine=$lines[$group][$attempt][1];

            $image_cut=imagecreatetruecolor($X, $EndLine-$StartLine+1);
            imagecolorallocate($image_cut, 0,0, 0);

            imagecopy($image_cut, imagecreatefromjpeg("Scramble/Hard/{$rand}_{$group}.jpg"), 0, 0, $X0, 
                $StartLine, $X, $EndLine-$StartLine+1);


            $file_tmp="Scramble/Hard/{$rand}_{$group}_{$attempt}.png";
            imagepng($image_cut, $file_tmp);

            if($Discipline=='Scrambling'){
                $pdf->Text(10, $pdf_img_Y+20,$Letter[$group+1]);
            }else{
                if($attempt>$Attemption){
                    $att='E'.($attempt-$Attemption);
                }else{
                    $att=$attempt;
                }
                if($data['Discipline_CutScrambles']){
                    $pdf->SetFont('times','B',20); 
                    $pdf->Text(6, $pdf_img_Y+20, $Letter[$group+1].$att);
                }else{
                    $pdf->SetFont('times','B',20); 
                    $pdf->Text(10, $pdf_img_Y+20, $att);
                }
            }

            $pdf->Image($file_tmp,
                    $pdf_img_X0,
                    $pdf_img_Y,
                    $pdf_img_X-$pdf_img_X0,
                    $pdf_img_X/$X*($EndLine-$StartLine+1));
            $pdf_img_Y+=$pdf_img_X/$X*($EndLine-$StartLine+1)+1;
        }
        
    }
    
    $pdf->Output();
    $pdf->Output("Image/Scramble/Hard_".md5($data['Event_ID'].GetIni('PASSWORD','admin')).".pdf");
    $pdf->Close();
    exit();
}