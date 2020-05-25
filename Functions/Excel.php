<?php

function GenerateExcel($Excelname,$columnArray,$dataArray){
    
    
$objPHPExcel = new PHPExcel();

$sharedStyleBorder=new PHPExcel_Style();
$sharedStyleBorder->applyFromArray(array('borders'=>array('allborders'=>array('style' => PHPExcel_Style_Border::BORDER_THIN))));

$ColumnLetter=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sharedStyleBorder, $ColumnLetter[0]."1".":".$ColumnLetter[sizeof($columnArray)-1].(sizeof($dataArray)+1));    

$sharedStyleBold=new PHPExcel_Style();
$sharedStyleBold->applyFromArray(array(
                                'font'=>array('bold'=>1),
                                'borders'=>array('allborders'=>array('style' => PHPExcel_Style_Border::BORDER_THIN))));

$c=0;
foreach($columnArray as $name=>$column){
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($ColumnLetter[$c]."1", $name);
    $objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sharedStyleBold, $ColumnLetter[$c]."1".":".$ColumnLetter[$c]."1");
    if(isset($column['bold'])){
        $objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sharedStyleBold, $ColumnLetter[$c]."1".":".$ColumnLetter[$c].(sizeof($dataArray)+1));    
    }
    if(isset($column['width'])){
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($ColumnLetter[$c])->setWidth($column['width']);
    }
    
    $c++;
}

$i = 1;
foreach($dataArray as $val){
    $i++;
    $c=0;
    foreach($val as $data){
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ColumnLetter[$c]."$i", $data);
        $c++;
    }
}


 $objPHPExcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$Excelname.'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
 
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    
    
}