<?php
$Competitor= GetCompetitorData();
if($Competitor){
    CheckPostIsset('Competitors','Secret');
    CheckPostNotEmpty('Competitors','Secret');

    
    
    $Secret= DataBaseClass::Escape($_POST['Secret']);
    
    DataBaseClass::Query("Select * from `Meeting` where  Secret='$Secret'");
    $meeting=DataBaseClass::getRow();
    if(is_array($meeting) and ($meeting['Competitor']==$Competitor->id or CheckMeetingGrand() or CheckMeetingOrganizer($meeting['ID']) )){

        DataBaseClass::FromTable("Meeting","ID=".$meeting['ID']);
        DataBaseClass::Join_current("MeetingDiscipline");
        DataBaseClass::Join_current("MeetingDisciplineList");
        DataBaseClass::Where("MeetingDiscipline","Round=1");
        $discipines=array();
        foreach(DataBaseClass::QueryGenerate() as $row){
            $discipines[mb_strtolower($row['MeetingDisciplineList_Code'])]=$row['MeetingDiscipline_ID'];
        }
        
        $Competitors = explode(",",str_replace("\n",",",$_POST['Competitors']));
        $registrations=array();
        foreach($Competitors as $c=>$comp){
            if(trim($comp)==""){
                unset($Competitors[$c]);
            }else{
                
                $comp=mb_strtolower($comp);
                $comp=str_replace(chr(13),"",$comp);
                $words=explode(" ",$comp);
                
                $regs=array();
                foreach($discipines as $code=>$discipline){
                    if(in_array($code,$words)){
                        $regs[]=$discipline;
                    }
                }
                foreach($words as $w=>$word){
                 
                    if(isset($discipines[$word])){
                        unset($words[$w]);
                    }
                }
                
                $comp=implode(" ",$words);
                $comp= trim(DataBaseClass::Escape(mb_convert_case(mb_strtolower(preg_replace("/\s{2,}/"," ",$comp)), MB_CASE_TITLE, "UTF-8")));
                $Competitors[$c]=$comp;
                $registrations[$comp]=$regs;
            }
        }    
        
        foreach($Competitors as $comp){
            
            DataBaseClass::Query("Select * from  `MeetingCompetitor` where  Meeting=".$meeting['ID']." and Name='".$comp."'");        
            $row=DataBaseClass::getRow();     
            if(!is_array($row)){
                DataBaseClass::Query("Insert into  `MeetingCompetitor` (Meeting,Name) values (".$meeting['ID'].",'".$comp."')");        
                $MeetingCompetition_ID=DataBaseClass::getID();
            }else{
                $MeetingCompetition_ID=$row['ID'];
            }
            
            foreach($registrations[$comp] as $registration){
                DataBaseClass::Query("Select * from  `MeetingCompetitorDiscipline` where MeetingCompetitor=$MeetingCompetition_ID and MeetingDiscipline=$registration");        
                $reg=DataBaseClass::getRow();
                if(!is_array($reg)){
                    DataBaseClass::Query("Insert into  `MeetingCompetitorDiscipline` (MeetingCompetitor,MeetingDiscipline) values ($MeetingCompetition_ID,$registration)");        
                }
            }
            
            
        }
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);
exit();  