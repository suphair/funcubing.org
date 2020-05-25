<?php

    function Group_Name($n){
        $Group_Name=array(-1=>"","A","B","C","D","E","F");
        return $Group_Name[$n];
    }
    
    function Groups_Name($n){
        $groups=array();
        for($i=0;$i<$n;$i++){
            $groups[]=Group_Name($i);
        }
        return implode(", ", $groups).".";
    }

    
function CommandDeleter(){
    DataBaseClass::Query("Delete from Command where ID not in (Select Command from CommandCompetitor)");
}