<?php

if(CheckAdmin()){
    CheckPostIsset('Country','Discipline','Text');
    CheckPostNotEmpty('Country','Discipline');
    CheckPostIsNumeric('Discipline');
    
    $Country = substr(DataBaseClass::Escape($_POST['Country']),0,2);
    $Text = DataBaseClass::Escape($_POST['Text']);
    $Discipline = $_POST['Discipline'];
    
    DataBaseClass::FromTable("Discipline","ID='$Discipline'");
    $disciplineCode=DataBaseClass::QueryGenerate(false)['Discipline_Code'];
    
    DataBaseClass::FromTable("Regulation","Discipline='$Discipline'");
    DataBaseClass::Where_current("Country='$Country'");
    DataBaseClass::QueryGenerate();
    if(DataBaseClass::rowsCount()==1){
        DataBaseClass::Query("Update Regulation set Text='$Text' where Discipline='$Discipline' and Country='$Country'" );    
    }else{ 
        DataBaseClass::Query("Insert into  `Regulation` ( Text,Discipline,Country) VALUES('$Text','$Discipline','$Country')");
    }
}    
$url=PageIndex()."?Regulations&Country=$Country#$disciplineCode";
header('Location: '.$url);
exit();  
