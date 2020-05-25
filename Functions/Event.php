<?php

function EventRoundView($Competition=0){
    
        DataBaseClass::FromTable("Event");
        if($Competition){
            DataBaseClass::Where_current("Competition=$Competition");    
        }
        DataBaseClass::OrderClear("Event", "Round");
        $events=array();
        foreach(DataBaseClass::QueryGenerate() as $event){
            $events[$event['Event_Competition']][$event['Event_DisciplineFormat']]=$event['Event_Round'];
        }
        
        foreach(DataBaseClass::QueryGenerate() as $event){     
            if($events[$event['Event_Competition']][$event['Event_DisciplineFormat']]>1){
                $round_out=': '.array('1'=>'1st','2'=>'2nd','3'=>'3rd','4'=>'4th')[$event['Event_Round']].' round';
            }else{
                $round_out="";
            }
                
            DataBaseClass::Query("Update Event set vRound='$round_out' where ID=".$event['Event_ID']);
        }
}