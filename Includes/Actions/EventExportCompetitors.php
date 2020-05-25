<?php
if(isset($_GET['ID']) and is_numeric(($_GET['ID'])) ){
    $ID=$_GET['ID'];
    
    
    DataBaseClass::FromTable('Competitor');
    DataBaseClass::Join_current('CompetitorEvent');
    DataBaseClass::Join_current('Event');
    DataBaseClass::Where_current("ID=$ID");
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('Event','Competition');
    DataBaseClass::OrderClear("Competitor","Name");

    $competitors=DataBaseClass::QueryGenerate();
      
    DataBaseClass::Query("select  C.WCA Competition, D.Code Discipline,E.Groups from `Discipline` D "
    . " join `Event` E on E.Discipline = D.ID "
    ." join Competition C on C.ID=E.Competition where E.ID='$ID'");
    $data=DataBaseClass::getRow();
        
    $ExcelName='Competitors '.$data['Competition'].'-'.$data['Discipline']; 
    $ExcelData=array();
    $ExcelColumn=array();
    $ExcelColumn['#']=array('width'=>5);
    $ExcelColumn['Name']=array('bold'=>1,'width'=>20);
    $ExcelColumn['WCAID']=array('width'=>15);
    $ExcelColumn['Country']=array('width'=>10);
    $ExcelColumn['Group']=array('width'=>7);
    $ExcelColumn['ID']=array('width'=>5);
    foreach($competitors as $i=>$competitor){ 
       $ExcelData[]=array(
           '#'=>$i+1,
           'Name'=>$competitor['Competitor_Name'],
           'WCAID'=>$competitor['Competitor_WCAID'],
           'Country'=>$competitor['Competitor_Country'],
           'Group'=> Group_Name($competitor['CompetitorEvent_Group']),
           'ID'=> $competitor['CompetitorEvent_CardID'],
           ); 
    }
    
    GenerateExcel($ExcelName,$ExcelColumn,$ExcelData);
    
}else{
     echo 'Not found';
}
