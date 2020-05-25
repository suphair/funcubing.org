<?php

function GetBlockText($Name,$Country=''){
  $Name= DataBaseClass::Escape($Name);
  if($Country){
    DataBaseClass::Query("Select * from BlockText where Name='$Name' and '$Country'=Country");  
  }else{
    DataBaseClass::Query("Select * from BlockText where Name='$Name'" );  
  }
  if(DataBaseClass::rowsCount()){
    return DataBaseClass::getRow()['Value'];
  }  
   return false; 
    
}
?>