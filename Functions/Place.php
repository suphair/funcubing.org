<?php


function SetValueAttempts($ID,$attemption,$result,$ext_result,$values,$amounts){
    
    $dnfcount=0;
    $results=array();
    $minutes=array();
    $seconds=array();
    $miliseconds=array();
    $dnfs=array();
    
    DataBaseClass::FromTable('Command',"ID=$ID");
    DataBaseClass::Join_current('Event');
    $event=DataBaseClass::QueryGenerate(false);
    
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join_current('FormatResult');
    
    $FormatResult=DataBaseClass::QueryGenerate(false)['FormatResult_Format'];
    
    $limit=$event['Event_LimitMinute']*60*100+$event['Event_LimitSecond']*100;
    $sum=0;    
    $mean=0;
    $meanAmount=0;    
    $warn=false;
    for($i=1;$i<=$attemption;$i++){
        $value=$values[$i];
        $amount=$amounts[$i];
        if($value=='DNF'){
            $dnfs[]=$i;
            $sum+=$limit;
            DataBaseClass::Query("Select ID from `Attempt` where  Command='$ID' and Attempt='$i'");
            if(DataBaseClass::rowsCount()){
                DataBaseClass::Query("Update `Attempt` set IsDNF=1, IsDNS=0, Minute=0, Second=0, Milisecond=0,Amount=0 where  Command='$ID' and Attempt='$i'");    
            }else{
                DataBaseClass::Query("Insert into `Attempt` (Command, Attempt, IsDNF, IsDNS, Minute, Second, Milisecond,Amount) values ('$ID','$i',1,0,0,0,0,0)");     
            }
        }elseif($value=='DNS'){
            $dnfs[]=$i;
            $sum+=$limit;
            DataBaseClass::Query("Select ID from `Attempt` where  Command='$ID' and Attempt='$i'");
            if(DataBaseClass::rowsCount()){
                DataBaseClass::Query("Update `Attempt` set IsDNF=0, IsDNS=1, Minute=0, Second=0, Milisecond=0,Amount=0 where  Command='$ID' and Attempt='$i'");    
            }else{
                DataBaseClass::Query("Insert into `Attempt` (Command, Attempt, IsDNF, IsDNS, Minute, Second, Milisecond,Amount) values ('$ID','$i',0,1,0,0,0,0)");     
            }    
        }else{
           $value=str_replace(".","",$value);
           $value=str_replace(":","",$value);
           $value=sprintf("%06d",$value);
          
           if($value<>"000000" or $amount>0){
               $warn=true;
               $minute=(int)substr($value,0,2); 
               $second=(int)substr($value,2,2); 
               $milisecond=(int)substr($value,4,2);
                DataBaseClass::Query("Select ID from `Attempt` where  Command='$ID' and Attempt='$i'");
                if(DataBaseClass::rowsCount()){
                    DataBaseClass::Query("Update `Attempt` set IsDNF=0, IsDNS=0, Minute='$minute', Second='$second', Milisecond='$milisecond',Amount='$amount' where  Command='$ID' and Attempt='$i'");    
                }else{
                    DataBaseClass::Query("Insert into `Attempt` (Command, Attempt, IsDNF, IsDNS, Minute, Second, Milisecond,Amount) values ('$ID','$i',0,0,'$minute','$second','$milisecond','$amount')");     
                } 
                $results[$i]=$minute*60+$second+$milisecond/100.;
                $minutes[$i]=$minute;
                $seconds[$i]=$second;
                $miliseconds[$i]=$milisecond;
                $sum+=$results[$i]*100;
                $mean+=$results[$i]*100;
                $meanAmount+=$amount;
           }else{
                $sum+=$limit;
                DataBaseClass::Query("Select ID from `Attempt` where  Command='$ID' and Attempt='$i'");
                if(DataBaseClass::rowsCount()){
                    DataBaseClass::Query("Delete from `Attempt` where Command='$ID' and Attempt='$i'");    
                }
           }
        }   
    }
    
    DataBaseClass::Query("Update `Attempt` set Except=0 where  Command='$ID'");    
    DataBaseClass::Query("Delete from `Attempt` where Command='$ID' and Special is not null");
    
    
    //Sum
    if($result=='Sum'){                  
        $minute=(int)($sum/60/100); 
        $second=(int)($sum/100)-$minute*60; 
        $milisecond=$sum-$minute*60*100-$second*100;
               
        DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',0,0,'$minute','$second','$milisecond','Sum')");     
        AttemptUpdater($ID);
        return;
    }
    
    
    
    if($FormatResult=='T'){
        //Best Time
        if(sizeof($results)>0){
            $best=min($results);
            $best_n=0;
            for($i=1;$i<=$attemption;$i++){
                if(isset($results[$i]) and $results[$i]==$best){
                    $best_n=$i;
                    break;
                }
            }

            if(in_array('Best',array($result,$ext_result))){
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',0,0,'$minutes[$best_n]','$seconds[$best_n]','$miliseconds[$best_n]','Best')");     
            }
        }elseif(sizeof($dnfs)){
            $best_n=0;  
            if(in_array('Best',array($result,$ext_result))){
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',1,0,0,0,0,'Best')");     
            }
        }
    }
    
    if($FormatResult=='A T'){
        //Best Amount Time
        if(sizeof($amounts)>0){
            $best_amount=max($amounts);
            $best_time=999999;
            $best_n=-1;
            for($i=1;$i<=$attemption;$i++){
                if(isset($amounts[$i]) and $amounts[$i]==$best_amount and isset($results[$i]) and $best_time>$results[$i]){
                    $best_time=$results[$i];
                    $best_n=$i;
                }
            }

            if($best_n>-1 and in_array('Best',array($result,$ext_result))){
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special,Amount) values ('$ID',0,0,'$minutes[$best_n]','$seconds[$best_n]','$miliseconds[$best_n]','Best',$best_amount)");     
            }
        }else{
            $best_n=0;  
            if(in_array('Best',array($result,$ext_result))){
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special,Amount) values ('$ID',1,0,0,0,0,'Best',$best_amount)");     
            }
        }
    }
    
    //Average/Except 
  
    if($result=='Average' and (sizeof($dnfs)+sizeof($results))==$attemption and sizeof($dnfs)<=1){
        if(sizeof($dnfs)==0){
            $wrost=max($results);
            $wrost_n=0;
            for($i=$attemption;$i>=1;$i--){
                if(isset($results[$i]) and $results[$i]==$wrost){
                    $wrost_n=$i;
                    break;
                }
            }
        }else{
            $wrost_n=$dnfs[0];
        }
        DataBaseClass::Query("Update `Attempt` set Except=1  where  Command='$ID' and Attempt='$best_n'");    
        DataBaseClass::Query("Update `Attempt` set Except=1  where  Command='$ID' and Attempt='$wrost_n'");    
    }
    
    //Average
    if(in_array('Average',array($result,$ext_result)) and (sizeof($dnfs)+sizeof($results))==$attemption){
        if(sizeof($dnfs)>=1){
            if(sizeof($dnfs)==1){
                $average= round((array_sum($results)-min($results))/3,2);
                $minute=floor($average/60);
                $second=floor($average-$minute*60);
                $milisecond=($average-60*$minute-$second)*100;
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',0,0,'$minute','$second','$milisecond','Average')");     
                $IDAttempt=DataBaseClass::getID();
            }elseif(sizeof($dnfs)>=2){
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',1,0,'0','0','0','Average')");     
            }
        }else{
            $average= round((array_sum($results)-min($results)-max($results))/3,2);
            $minute=floor($average/60);
            $second=floor($average-$minute*60);
            $milisecond=($average-60*$minute-$second)*100;
            DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',0,0,'$minute','$second','$milisecond','Average')");     
        } 
    }
    
    if($FormatResult=='T'){
        //Mean Time
       if(in_array('Mean',array($result,$ext_result)) and sizeof($results)==$attemption){
           if(sizeof($dnfs)>=1){
               DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',1,0,'0','0','0','Mean')");     
           }else{
               $mean=round($mean/$attemption,0);
               $minute=(int)($mean/60/100); 
               $second=(int)(($mean/100)-$minute*60); 
               $milisecond=$mean-$minute*60*100-$second*100;
               
               DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special) values ('$ID',0,0,'$minute','$second','$milisecond','Mean')");     

           } 
       }
    }
    if($FormatResult=='A T'){
    //Mean Amount Time
        if(in_array('Mean',array($result,$ext_result))){
            if(min($amounts)==0 or sizeof($dnfs)>=1 ){
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special,Amount) values ('$ID',1,0,'0','0','0','Mean',0)");
            }else{
                $meanAmount=round($meanAmount/$attemption,1);
                DataBaseClass::Query("Insert into `Attempt` (Command, IsDNF, IsDNS, Minute, Second, Milisecond,Special,Amount) values ('$ID',0,0,0,0,0,'Mean',$meanAmount)");     

            } 
        }
    }
    
    AttemptUpdater($ID);
    return $warn;
}

function Update_Place($event_ID){
    DataBaseClass::Query("Update `Command` set Place='0' where Event='$event_ID'"); 
    DataBaseClass::Query("Select FR.Format,Result,ExtResult from `Format` F "
            ." join DisciplineFormat DF on DF.Format=F.ID  "
            ." join `Discipline` D on D.ID=DF.Discipline "
            ." join `FormatResult` FR on FR.ID=D.FormatResult "
            . " join `Event` E on E.DisciplineFormat=DF.ID where E.ID='$event_ID'");
    $format=DataBaseClass::getRow();
    $result=$format['Result'];
    $ext_result=$format['ExtResult'];
    
    DataBaseClass::Query("Select  "
            . "case when A1.ID is null then 9999 when A1.IsDNF then 9998 else A1.minute*60+A1.second+A1.milisecond/100 end A1,"
            . "case when A2.IsDNF then 9998 else A2.minute*60+A2.second+A2.milisecond/100 end A2,"
            . "A1.Amount Amount1, A2.Amount Amount2, "
            . "Com.ID From `Command` Com  "
            . " left outer join `Attempt` A1 on A1.Command=Com.ID and A1.Special='$result' and A1.IsDNF=0"
            . " left outer join `Attempt` A2 on A2.Command=Com.ID and A2.Special='$ext_result'  and A2.IsDNF=0"
            . " where Com.Event='$event_ID' and not Com.Decline and (A1.ID is not null or A2.ID is not null) "
            . " order  by"
            . ($format['Format']=='A T'?" Amount1 desc,":"")
            . " 1,2 ");
    
    
  
    $orders=DataBaseClass::getRows();
    $value=0;
    $n=0;
    
    foreach($orders as $order){

        
        if($format['Format']=='A T'){
            if($value!=(100-$order['Amount1'])*10000+$order['A1']){
                $n++;
                $value=(100-$order['Amount1'])*10000+$order['A1'];
            }
        }else{
            if($value!=$order['A1']*10000+$order['A2']){
                $n++;
                $value=$order['A1']*10000+$order['A2'];
            }
        }

        DataBaseClass::Query("Update `Command` set Place='$n'  where  ID='".$order['ID']."'");    
    } 
}