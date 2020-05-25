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
    
    
    $ScamblesOnePage=(5+2);
    $ScramblesEvent=$Groups*($Attemption+1);
    $PagesEvent=ceil($ScramblesEvent/$ScamblesOnePage);
    $ScramblesEachAttempts=$Pages/$PagesEvent;
    
    //echo "Pages=$Pages Groups=$Groups A=".($Attemption+1)." ScramblesEvent=$ScramblesEvent PagesEvent=".$PagesEvent." ScramblesEachAttempts=$ScramblesEachAttempts";
    //echo '<hr>';
    
    for($group=1;$group<=$Groups;$group++){
        for($attemption=1;$attemption<=($Attemption+1);$attemption++){
            $StartPage=floor((($group-1)*($Attemption+1)+$attemption)/$ScamblesOnePage);
            $PageAdd=0;
            //echo "<h3>StartPage=$StartPage </h3>";
            $AttemptScrambling=0;
            $PageNumberPDF=0;
            for($BasePage=$StartPage;$BasePage<$Pages;$BasePage+=$PagesEvent){
                $AttemptScrambling++;
                $CurrentPage=$PageAdd+$BasePage;
                if(!isset($ScambleOnPage[$CurrentPage])){
                    $ScambleOnPage[$CurrentPage]=0;
                }
                $ScambleOnPage[$CurrentPage]++;
                //echo "BasePage=$BasePage CurrentPage=$CurrentPage ScambleOnPage=".$ScambleOnPage[$CurrentPage];
                //echo "Group=$group Attemption=$attemption <br>";
                //echo $CurrentPage." ".$ScambleOnPage[$CurrentPage];
                $StartLine=$lines[$CurrentPage][$ScambleOnPage[$CurrentPage]][0];
                $EndLine=$lines[$CurrentPage][$ScambleOnPage[$CurrentPage]][1];
                
                if(($pdf_img_Y+$pdf_img_X/$X*($EndLine-$StartLine+1))>($pdf->h-10)){
                    $NextPagePDF=true;
                }else{
                    $NextPagePDF=false;    
                }
                
                if($BasePage==$StartPage or $NextPagePDF){
                    $PageNumberPDF++;
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
                    if($attemption<$Attemption+1){                    
                        $pdf->Text(30, 27, $Letter[$group].' group                    '. $attemption. ' attempt                    '.$PageNumberPDF.' page');
                    }else{
                        $pdf->Text(30, 27, $Letter[$group].' group                    Extra attempt                    '.$PageNumberPDF.' page');
                    }
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetFont('Arial','',10);
                    $pdf->Text(80, 286,GetIni('TEXT','print'));
                    $pdf->SetFont('Arial','',10);
                    $pdf->Text(150, 286,date("d.m.Y"));
                    
                    $pdf_img_Y=$pdf_img_Y0;
                }

                $image_cut=imagecreatetruecolor($X, $EndLine-$StartLine+1);
                imagecolorallocate($image_cut, 0,0, 0);
                
                
                
                imagecopy($image_cut, imagecreatefromjpeg("Scramble/Hard/{$rand}_{$CurrentPage}.jpg"), 0, 0, $X0, 
                    $StartLine, $X, $EndLine-$StartLine+1);
                
                
                $file_tmp="Scramble/Hard/{$rand}_{$CurrentPage}_{$group}_{$attemption}.png";
                imagepng($image_cut, $file_tmp);

                $pdf->SetFont('times','B',24);

                if(strpos($Discipline, 'Scrambling')!=false){
                    $pdf->Text(10, $pdf_img_Y+20, $Letter[$group].$AttemptScrambling);
                }else{
                    $pdf->Text(10, $pdf_img_Y+20, $AttemptScrambling);
                }                
                
                $pdf->Image($file_tmp,
                        $pdf_img_X0,
                        $pdf_img_Y,
                        $pdf_img_X-$pdf_img_X0,
                        $pdf_img_X/$X*($EndLine-$StartLine+1));
                $pdf_img_Y+=$pdf_img_X/$X*($EndLine-$StartLine+1)+1;
                
                
                if($ScambleOnPage[$CurrentPage]==$ScamblesOnePage){
                    $PageAdd++;
                }
            }
        }
    }
    
    $pdf->Output();
    $pdf->Output("Image/Scramble/Hard_".md5($data['Event_ID'].GetIni('PASSWORD','admin')).".pdf");
    $pdf->Close();
    exit();
}